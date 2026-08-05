<?php
require_once(__DIR__ . '/guard.php');


require_once __DIR__ . '/../../config/db.php';
//echo $_SESSION['token']."</br>".$_POST['token']."</br>".$_COOKIE['token'];exit;
if (!verify_token()) {
	session_destroy();
	echo '<script>alert("Security token mismatch. Please log in again.");window.location="../../public/index.php";</script>';
	exit();
}

if(isset($_POST['submit_request'])) {
    $device_id = (int)($_POST['device_id'] ?? 0);
    $mac_address = $_POST['mac_address'];
    $ip_address = $_POST['ip_address_of_employee'];
    $ip_purpose =  clean($_POST['ip_purpose']);
   
        

    $_SESSION['form_errors'] = '';  // Initialize

if (empty($device_id)) {
    $_SESSION['form_errors'] .= "Device ID is required. ";
}

if (empty($mac_address)) {
    $_SESSION['form_errors'] .= "MAC Address is required. ";
}

if (empty($ip_purpose)) {
    $_SESSION['form_errors'] .= "Purpose is required. ";
}

// If there are errors, redirect back
if (!empty($_SESSION['form_errors'])) {
    header("Location: request_ip.php");
    exit();
}

    
    
    // Validate MAC address format
    if(!preg_match('/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', $mac_address)) {
        echo '<script> alert("Invalid MAC address format.")</script>';
        $_SESSION['form_errors'] .= "Invalid MAC address format. ";

        header("location:request_ip.php");


        exit();
        }
    
    // Validate IP address format
    if(!empty($ip_address) && !filter_var($ip_address, FILTER_VALIDATE_IP)) {
        echo '<script> alert("Invalid IP address format.")</script>';
        $_SESSION['form_errors'] .= "Invalid MAC address format. ";

        header("location:request_ip.php");
        exit();
    }

    // Insert into database
    $emp_id = $_SESSION['emp_id'];
    $reporting_officer_id = $_SESSION['reporting_officer_id'];
    // print_r($_SESSION);
    // exit();

    $stmt = $conn->prepare("INSERT INTO ip_request (emp_id, reporting_officer_id, device_id, mac_address, ip_address_of_employee, datetime_of_employee, action_status_reporting_officer, query_reporting_officer, ip_purpose) VALUES (?, ?, ?, ?, ?, NOW(), ?, '', ?)");
    
    if ($stmt) {
        $pending = STATUS_PENDING;
        $stmt->bind_param("iiissis", $emp_id, $reporting_officer_id, $device_id, $mac_address, $ip_address, $pending, $ip_purpose);
        
        if ($stmt->execute()) {
            echo '<script> alert("IP request submitted successfully.")</script>';
            $_SESSION['form_errors'] = "IP request submitted successfully.";
            header("location:dashboard.php");
            exit();
        } else {
            echo '<script> alert("Database error: ' . htmlspecialchars($stmt->error) . '")>';

            $_SESSION['form_errors'] .= "Database error: " . htmlspecialchars($stmt->error);    
            header("location:request_ip.php");
            exit();
        }
        
        $stmt->close();
    } else {
    $_SESSION['form_errors'] .= "Failed to prepare statement: " . htmlspecialchars($conn->error);
        echo '<script> alert("Database error: ' . htmlspecialchars($conn->error) .
        '")</script>';

    header("location:request_ip.php");
    exit();
} 
}