<?php
require_once(__DIR__ . '/guard.php');
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = (int)($_POST['request_id'] ?? 0);
    $status     = ($_POST['status'] ?? '') == '1' ? 1 : 0;
    $remarks    = $_POST['query_reporting_officer'] ?? '';
    $ip         = $_SERVER['REMOTE_ADDR'];

    if ($request_id > 0) {
        if ($status == 0 && $remarks !== '') {
            $stmt = $conn->prepare("UPDATE ip_request
                SET action_status_reporting_officer = ?,
                    reporting_officer_date_time = NOW(),
                    reporting_officer_ip_address = ?,
                    query_reporting_officer = ?
                WHERE id = ?");
            $stmt->bind_param("issi", $status, $ip, $remarks, $request_id);
        } else {
            $stmt = $conn->prepare("UPDATE ip_request
                SET action_status_reporting_officer = ?,
                    reporting_officer_date_time = NOW(),
                    reporting_officer_ip_address = ?
                WHERE id = ?");
            $stmt->bind_param("isi", $status, $ip, $request_id);
        }

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: review_requests.php");
            exit();
        }
        echo "Database update failed.";
        $stmt->close();
    } else {
        echo "Missing required fields.";
    }
}
