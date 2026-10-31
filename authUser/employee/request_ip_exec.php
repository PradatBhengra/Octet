<?php
require_once(__DIR__ . '/guard.php');
require_once __DIR__ . '/../../config/db.php';

if (!verify_token()) {
	session_destroy();
	echo '<script>alert("Security token mismatch. Please log in again.");window.location="../../public/index.php";</script>';
	exit();
}

if (isset($_POST['submit_request'])) {
    $device_id = (int)($_POST['device_id'] ?? 0);
    $mac_address = trim($_POST['mac_address'] ?? '');
    $ip_address = trim($_POST['ip_address_of_employee'] ?? '');
    // Stored as typed; pages escape it when they print it
    $ip_purpose = trim($_POST['ip_purpose'] ?? '');

    $_SESSION['form_errors'] = '';

    // Must be an active device from the dropdown (device_id is a foreign key)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM device_master WHERE id = ? AND status = 1");
    $stmt->bind_param("i", $device_id);
    $stmt->execute();
    if ($stmt->get_result()->fetch_row()[0] == 0) {
        $_SESSION['form_errors'] .= "Please choose a device from the list. ";
    }
    $stmt->close();

    if ($mac_address === '') {
        $_SESSION['form_errors'] .= "MAC Address is required. ";
    } elseif (!preg_match('/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', $mac_address)) {
        $_SESSION['form_errors'] .= "Invalid MAC address format. ";
    }

    // The column holds an IPv4 address (varchar 15)
    if ($ip_address !== '' && !filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $_SESSION['form_errors'] .= "Invalid IP address format. ";
    }

    if ($ip_purpose === '') {
        $_SESSION['form_errors'] .= "Purpose is required. ";
    } elseif (mb_strlen($ip_purpose) > 200) {
        $_SESSION['form_errors'] .= "Purpose must be 200 characters or less. ";
    }

    if ($_SESSION['form_errors'] !== '') {
        header("Location: request_ip.php");
        exit();
    }
    unset($_SESSION['form_errors']);

    $emp_id = $_SESSION['emp_id'];
    $reporting_officer_id = $_SESSION['reporting_officer_id'];
    $pending = STATUS_PENDING;

    $stmt = $conn->prepare("INSERT INTO ip_request (emp_id, reporting_officer_id, device_id, mac_address, ip_address_of_employee, datetime_of_employee, action_status_reporting_officer, query_reporting_officer, ip_purpose) VALUES (?, ?, ?, ?, ?, NOW(), ?, '', ?)");
    $stmt->bind_param("iiissis", $emp_id, $reporting_officer_id, $device_id, $mac_address, $ip_address, $pending, $ip_purpose);
    $stmt->execute();
    $stmt->close();

    $_SESSION['form_success'] = "IP request submitted successfully.";
    header("Location: dashboard.php");
    exit();
}
