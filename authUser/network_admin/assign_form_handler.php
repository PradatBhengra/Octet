<?php
require_once(__DIR__ . '/guard.php');
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_token()) {
        session_destroy();
        echo '<script>alert("Security token mismatch. Please log in again.");window.location="../../public/index.php";</script>';
        exit();
    }

    $request_id = (int)($_POST['request_id'] ?? 0);
    $ip_address = trim($_POST['ip_address'] ?? '');
    $subnet_mask = trim($_POST['subnet_mask'] ?? '');
    $gateway = trim($_POST['gateway'] ?? '');
    $dns1 = trim($_POST['dns1'] ?? '');
    $dns2 = trim($_POST['dns2'] ?? '');

    foreach ([$ip_address, $subnet_mask, $gateway, $dns1, $dns2] as $value) {
        if (!filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            echo "Error: '" . htmlspecialchars($value) . "' is not a valid IPv4 address.";
            exit;
        }
    }

    // Step 1: Get emp_id and mac_address from the approved ip_request
    $approved = STATUS_APPROVED;
    $stmt = $conn->prepare("SELECT emp_id, mac_address FROM ip_request WHERE id = ? AND action_status_reporting_officer = ?");
    $stmt->bind_param("ii", $request_id, $approved);
    $stmt->execute();
    $stmt->bind_result($emp_id, $mac_address);
    $stmt->fetch();
    $stmt->close();

    if (!$emp_id) {
        echo "Error: Approved request not found.";
        exit;
    }

    $given_by_email = $_SESSION['login-email'] ?? 'admin@example.com';

    $conn->begin_transaction();

    // Step 2: Check if this employee's device already has an ip_transaction.
    // A device is identified by its MAC; device_id is only the type (Laptop, Printer...).
    $checkStmt = $conn->prepare("
        SELECT it.id
        FROM ip_transaction it
        JOIN ip_request ir ON it.ip_request_id = ir.id
        WHERE it.emp_id = ? AND ir.mac_address = ?
    ");
    $checkStmt->bind_param("is", $emp_id, $mac_address);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // Device already exists → update the existing transaction
        $checkStmt->bind_result($transaction_id);
        $checkStmt->fetch();
        $checkStmt->close();

        $updateStmt = $conn->prepare("
            UPDATE ip_transaction 
            SET ip_address_assigned = ?, subnet_mask = ?, gateway = ?, dns1 = ?, dns2 = ?, date_time = NOW(), given_by_email = ?, used_unsed = 1, ip_request_id = ?, ip_address = ?
            WHERE id = ?
        ");
        $updateStmt->bind_param(
            'ssssssisi', // ip, mask, gateway, dns1, dns2, email, request_id, ip, transaction_id
            $ip_address,
            $subnet_mask,
            $gateway,
            $dns1,
            $dns2,
            $given_by_email,
            $request_id,
            $ip_address,
            $transaction_id
        );


        $updateStmt->execute();
        $updateStmt->close();
    } else {
        // Device not used → insert new transaction
        $checkStmt->close();

        $insertStmt = $conn->prepare("INSERT INTO ip_transaction 
            (ip_address_assigned, subnet_mask, gateway, dns1, dns2, emp_id, date_time, given_by_email, used_unsed, ip_request_id, ip_address)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, 1, ?, ?)"
        );

        $insertStmt->bind_param(
            "sssssisis", // ip, mask, gateway, dns1, dns2, emp_id, email, request_id, ip
            $ip_address,
            $subnet_mask,
            $gateway,
            $dns1,
            $dns2,
            $emp_id,
            $given_by_email,
            $request_id,
            $ip_address
        );

        $insertStmt->execute();
        $insertStmt->close();
    }

    // Record that this request now has its IP, so it leaves the assign list
    $markStmt = $conn->prepare("UPDATE ip_request SET assigned_date_time = NOW() WHERE id = ?");
    $markStmt->bind_param("i", $request_id);
    $markStmt->execute();
    $markStmt->close();
    $conn->commit();

    header("Location: assign_ip.php?success=1");
    exit;
} else {
    echo "Invalid request.";
}
