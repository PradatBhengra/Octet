<?php
require_once __DIR__ . '/../config/db.php';

// close the login_log entry if one was opened at sign in
if (isset($_SESSION['last_login_id'])) {
    $stmt = $conn->prepare("UPDATE login_log SET logout_date_time = NOW() WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['last_login_id']);
    $stmt->execute();
    $stmt->close();
}

setcookie(session_name(), '', time() - 3600, '/');
setcookie('token', '', time() - 3600, '/');
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logged out</title>
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container text-center" style="margin-top:100px">
    <h4>You have been logged out.</h4>
    <p><a href="index.php" class="btn btn-primary mt-3">Log in again</a></p>
  </div>
</body>
</html>
