<?php
require_once __DIR__ . '/../config/db.php';

if (!verify_token()) {
	session_destroy();
	echo '<script>alert("Security token mismatch. Please log in again.");window.location="index.php";</script>';
	exit();
}

if (isset($_POST['submit'])) {
	$username = clean($_POST['login-email']); 
	$password = $_POST['login-pwd']; // Don't sanitize passwords
	$captcha_input = $_POST['captcha_input'];
	$usertype = (int)$_POST['usertype']; // Cast to int for safety

	$_SESSION['username'] = $username;

	if ($username == '' || $password == '' || $captcha_input == '') {
		$_SESSION['msg'] = '<div class="alert alert-warning">Email, password and captcha are required</div>';
		echo '<script> window.location = "index.php"; </script>';
		exit;
	}

	if (!isset($_SESSION['captcha']) || $captcha_input != $_SESSION['captcha']) {
		$_SESSION['msg'] = '<div class="alert alert-warning">INCORRECT CAPTCHA</div>';
		echo '<script> window.location = "index.php"; </script>'; // Fixed redirect to index.php (login modal is there)
		exit;
	}

    // Mapping for user types
    $user_roles = [
        1 => ['table' => 'reporting_officer', 'redirect' => '../authUser/reporting_officer/dashboard.php'],
        2 => ['table' => 'network_admin', 'redirect' => '../authUser/network_admin/dashboard.php'],
        3 => ['table' => 'employee', 'redirect' => '../authUser/employee/dashboard.php']
    ];

    if (!array_key_exists($usertype, $user_roles)) {
        $_SESSION['msg'] = "<div style='color:red'>INVALID USER TYPE</div>";
		echo '<script>window.location="index.php"</script>';
		exit;
    }

    $role = $user_roles[$usertype];
    $table = $role['table'];
    $redirect_url = $role['redirect'];

    $query = "SELECT * FROM $table WHERE email = ? AND status = 1";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        $_SESSION['msg'] = "<div style='color:red'>System Error</div>";
		echo '<script>window.location="index.php"</script>';
        exit;
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $dpassword = $row['password'];
        $dpassword1 = $dpassword . $_SESSION['pkskey'];
        $dpassword2 = hash('sha256', $dpassword1);

        if ($dpassword2 == $password) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['trk'] = "123432456987567893465346463463464678568678942423535663744848";
            $_SESSION['pro_signin_token'] = $password;
            $_SESSION['pro_signin_token_user'] = $username;
            $_SESSION['login-email'] = $username; 

            $sign_in = date("Y-m-d H:i:s");

            // Update the role table's last-login column, but only if it exists
            $col_check = $conn->query("SHOW COLUMNS FROM `$table` LIKE 'logged_on'");
            if ($col_check && $col_check->num_rows > 0) {
                $update_stmt = $conn->prepare("UPDATE `$table` SET logged_on = ? WHERE email = ?");
                $update_stmt->bind_param("ss", $sign_in, $username);
                $update_stmt->execute();
                $update_stmt->close();
            }

            $_SESSION['logged_on'] = $sign_in;
            $_SESSION['usertype'] = $usertype;

            // Employee pages need these two ids for the request workflow
            if ($usertype == 3) {
                $_SESSION['emp_id'] = $row['id'];
                $_SESSION['reporting_officer_id'] = $row['reporting_officer_id'] ?? null;
            }

            // Open a login_log entry; logout.php closes it
            $log_stmt = $conn->prepare("INSERT INTO login_log (user_email, user_role, login_date_time) VALUES (?, ?, NOW())");
            $log_stmt->bind_param("ss", $username, $table);
            $log_stmt->execute();
            $_SESSION['last_login_id'] = $conn->insert_id;
            $log_stmt->close();

            header("Location: $redirect_url");
            exit;
        } else {
            $_SESSION['msg'] = "<div style='color:red'>INCORRECT PASSWORD</div>"; // More specific message for debugging, usually vague in prod but this is requested fix
            echo '<script>window.location="index.php"</script>';
            exit;
        }
    } else {
        $_SESSION['msg'] = "<div style='color:red'>USER NOT FOUND OR INACTIVE</div>";
        echo '<script>window.location="index.php"</script>';
        exit;
    }

    $stmt->close();
}
?>