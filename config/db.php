<?php
/*
 * Database connection, session bootstrap and shared helpers.
 * Guarded so that including this file twice is harmless.
 */
if (defined('IPM_DB_LOADED')) {
    return;
}
define('IPM_DB_LOADED', true);

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Log the real error, show the user something generic
    error_log($e->getMessage());
    die("Connection failed. Please try again later.");
}

/*
 * Request status values stored in ip_request.action_status_reporting_officer.
 * The column is an integer, so pending needs its own value rather than the
 * string 'Pending' the original code tried to insert.
 */
define('STATUS_REJECTED', 0);
define('STATUS_APPROVED', 1);
define('STATUS_PENDING', 2);

function clean($data) {
    if (is_array($data)) {
        return array_map('clean', $data);
    }
    // Basic sanitization; SQL safety comes from prepared statements
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/*
 * Issues the anti-CSRF token used by the login and IP request forms.
 * The cookie is only marked Secure when the site is actually served over
 * HTTPS, otherwise it would never be set on a local XAMPP install.
 */
function issue_token() {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
             || (($_SERVER['SERVER_PORT'] ?? 80) == 443);

    $token = bin2hex(random_bytes(16));
    setcookie('token', $token, [
        'expires'  => time() + 3600,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => $https ? 'None' : 'Lax'
    ]);
    $_SESSION['token'] = $token;
    return $token;
}

/*
 * Checks the posted token against the session and the cookie.
 * Returns true only when all three match.
 */
function verify_token() {
    $posted  = $_POST['token']    ?? '';
    $session = $_SESSION['token'] ?? '';
    $cookie  = $_COOKIE['token']  ?? '';

    if ($posted === '' || $session === '' || $cookie === '') {
        return false;
    }
    return hash_equals($session, $posted) && hash_equals($session, $cookie);
}

function status_name($s) {
    if ((int)$s === STATUS_APPROVED) return 'Approved';
    if ((int)$s === STATUS_REJECTED) return 'Rejected';
    return 'Pending';
}

function status_badge($s) {
    $name  = status_name($s);
    $class = $name === 'Approved' ? 'badge-success'
           : ($name === 'Rejected' ? 'badge-danger' : 'badge-warning');
    return '<span class="badge ' . $class . '">' . $name . '</span>';
}
