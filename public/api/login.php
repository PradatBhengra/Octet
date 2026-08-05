<?php
//header('Content-Type: application/json');
include("../../config/db.php");
//echo $_SESSION['token']."<br>".$_POST['token']."<br>".$_COOKIE['token'];exit;
if ( (!empty($_SESSION['token'])) && (!empty($_POST['token'])) && (!empty($_COOKIE['token'])) ){
	if ($_SESSION['token'] != $_POST['token']){
		session_destroy();
		http_response_code(401);
		echo json_encode(["status" => "error", "message" => "Security token mismatch."]);
		exit();
	}
	if ($_SESSION['token'] != $_COOKIE['token']){
		session_destroy();
		http_response_code(401);
		echo json_encode(["status" => "error", "message" => "Security token mismatch.."]);
		exit();
	}
	if ($_POST['token'] != $_COOKIE['token']){
		session_destroy();
		http_response_code(401);
		echo json_encode(["status" => "error", "message" => "Security token mismatch..."]);
		exit();
	}
}else{
	session_destroy();
	http_response_code(400);
	echo json_encode(["status" => "error", "message" => "Blank token found"]);
	exit();
}

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$captcha_input = $_POST['captcha_input'] ?? '';
if (empty($email) || empty($password) || empty($captcha_input)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Email, password and captcha are required"]);
    exit();
}

$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(["status" => "success", "message" => "Login successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>