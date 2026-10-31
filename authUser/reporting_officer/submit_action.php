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
    $status     = ($_POST['status'] ?? '') == '1' ? 1 : 0;
    $remarks    = trim($_POST['query_reporting_officer'] ?? '');
    $ip         = $_SERVER['REMOTE_ADDR'];
    $officer_id = (int)$_SESSION['id'];
    $pending    = STATUS_PENDING;

    if (mb_strlen($remarks) > 200) {
        echo "Remarks must be 200 characters or less.";
    } elseif ($request_id > 0) {
        // Only this officer's own requests, and only while they are still pending
        if ($status == 0 && $remarks !== '') {
            $stmt = $conn->prepare("UPDATE ip_request
                SET action_status_reporting_officer = ?,
                    reporting_officer_date_time = NOW(),
                    reporting_officer_ip_address = ?,
                    query_reporting_officer = ?
                WHERE id = ? AND reporting_officer_id = ? AND action_status_reporting_officer = ?");
            $stmt->bind_param("issiii", $status, $ip, $remarks, $request_id, $officer_id, $pending);
        } else {
            $stmt = $conn->prepare("UPDATE ip_request
                SET action_status_reporting_officer = ?,
                    reporting_officer_date_time = NOW(),
                    reporting_officer_ip_address = ?
                WHERE id = ? AND reporting_officer_id = ? AND action_status_reporting_officer = ?");
            $stmt->bind_param("isiii", $status, $ip, $request_id, $officer_id, $pending);
        }

        $stmt->execute();
        $updated = $stmt->affected_rows;
        $stmt->close();

        if ($updated === 1) {
            header("Location: review_requests.php");
            exit();
        }
        echo "Request not found or already reviewed.";
    } else {
        echo "Missing required fields.";
    }
}
