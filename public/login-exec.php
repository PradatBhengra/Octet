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

	// Each captcha image is good for one attempt only
	$captcha = $_SESSION['captcha'] ?? null;
	unset($_SESSION['captcha']);

	$_SESSION['username'] = $username;

	if ($username == '' || $password == '' || $captcha_input == '') {
		$_SESSION['msg'] = '<div class="alert alert-warning">Email, password and captcha are required</div>';
		echo '<script> window.location = "index.php"; </script>';
		exit;
	}

	if ($captcha === null || $captcha_input != $captcha) {
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
        $valid = password_verify($password, $row['password']);

        // Accounts made before password_hash() still hold an unsalted SHA-256 hash:
        // accept it this once and replace it with a proper hash
        if (!$valid && hash_equals($row['password'], hash('sha256', $password))) {
            $valid = true;
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $rehash_stmt = $conn->prepare("UPDATE `$table` SET password = ? WHERE id = ?");
            $rehash_stmt->bind_param("si", $new_hash, $row['id']);
            $rehash_stmt->execute();
            $rehash_stmt->close();
        }

        if ($valid) {
            // New session id on login so a pre-set session cookie can't be reused
            session_regenerate_id(true);
            $_SESSION['id'] = $row['id'];
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
                $_SESSION['employee_name'] = $row['name'];
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