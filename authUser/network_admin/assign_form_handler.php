<?php
require_once(__DIR__ . '/guard.php');
require_once __DIR__ . '/../../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $ip_address = $_POST['ip_address'];
    $subnet_mask = $_POST['subnet_mask'];
    $gateway = $_POST['gateway'];
    $dns1 = $_POST['dns1'];
    $dns2 = $_POST['dns2'];

    // Step 1: Get emp_id and device_id from ip_request
    $stmt = $conn->prepare("SELECT emp_id, device_id FROM ip_request WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->bind_result($emp_id, $device_id);
    $stmt->fetch();
    $stmt->close();

    if (!$emp_id || !$device_id) {
        echo "Error: Employee ID or Device ID not found.";
        exit;
    }

    $given_by_email = $_SESSION['login-email'] ?? 'admin@example.com';

    // Step 2: Check if this device_id has already been processed via any ip_transaction
    $checkStmt = $conn->prepare("
        SELECT it.id 
        FROM ip_transaction it
        JOIN ip_request ir ON it.ip_request_id = ir.id
        WHERE ir.device_id = ?
    ");
    $checkStmt->bind_param("s", $device_id);
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


        if ($updateStmt->execute()) {
            header("Location: assign_ip.php?success=1");
            exit;
        } else {
            echo "Update Error: " . $updateStmt->error;
        }
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

        if ($insertStmt->execute()) {
            header("Location: assign_ip.php?success=1");
            exit;
        } else {
            echo "Insert Error: " . $insertStmt->error;
        }
        $insertStmt->close();
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
