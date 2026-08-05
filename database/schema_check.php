<?php
/*
 * Schema check.
 * Open http://localhost/ip-management-system/database/schema_check.php
 * to see whether the database has every table and column the app queries.
 * Delete this file before deploying anywhere public.
 */
require_once(__DIR__ . '/../config/config.php');

$required = [
    'employee' => ['id', 'name', 'email', 'password', 'status', 'reporting_officer_id'],
    'reporting_officer' => ['id', 'name', 'email', 'password', 'status'],
    'network_admin' => ['id', 'name', 'email', 'password', 'status'],
    'ip_request' => [
        'id', 'emp_id', 'reporting_officer_id', 'device_id', 'mac_address',
        'ip_address_of_employee', 'ip_purpose', 'datetime_of_employee',
        'action_status_reporting_officer', 'query_reporting_officer',
        'reporting_officer_date_time', 'reporting_officer_ip_address'
    ],
    'ip_transaction' => [
        'id', 'ip_request_id', 'emp_id', 'ip_address', 'ip_address_assigned',
        'subnet_mask', 'gateway', 'dns1', 'dns2', 'date_time',
        'given_by_email', 'used_unsed'
    ],
    'login_log' => ['id', 'user_email', 'user_role', 'login_date_time', 'logout_date_time'],
];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
} catch (mysqli_sql_exception $e) {
    die("<h2>Cannot connect to the database</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}

$problems = [];
$fixes = [];

foreach ($required as $table => $columns) {
    $res = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($table) . "'");
    if ($res->num_rows === 0) {
        $problems[] = "Table <code>$table</code> is missing.";
        $fixes[] = "-- create the $table table (see ip_management.sql)";
        continue;
    }
    $existing = [];
    $res = $conn->query("SHOW COLUMNS FROM `$table`");
    while ($row = $res->fetch_assoc()) {
        $existing[] = $row['Field'];
    }
    foreach ($columns as $col) {
        if (!in_array($col, $existing, true)) {
            $problems[] = "Table <code>$table</code> is missing column <code>$col</code>.";
            $fixes[] = "ALTER TABLE `$table` ADD COLUMN `$col` VARCHAR(100) NULL;";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Schema check</title>
  <style>
    body { font-family: system-ui, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 20px; }
    .ok { color: #14622f; background: #e6f4ea; padding: 14px; border-radius: 6px; }
    .bad { color: #8a1c1c; background: #fdecec; padding: 14px; border-radius: 6px; }
    li { margin: 6px 0; }
    pre { background: #f4f4f4; padding: 14px; border-radius: 6px; overflow-x: auto; }
    code { background: #f0f0f0; padding: 1px 5px; border-radius: 3px; }
  </style>
</head>
<body>
  <h1>Schema check</h1>
  <p>Database: <code><?= htmlspecialchars(DB_NAME) ?></code> on <code><?= htmlspecialchars(DB_HOST) ?></code></p>

  <?php if (empty($problems)): ?>
    <p class="ok">Everything the application queries exists. You are good to go.</p>
  <?php else: ?>
    <p class="bad">Found <?= count($problems) ?> problem(s):</p>
    <ul>
      <?php foreach ($problems as $p) echo "<li>$p</li>"; ?>
    </ul>
    <h3>Suggested SQL</h3>
    <p>Review the column types before running these, then paste into the SQL tab in phpMyAdmin:</p>
    <pre><?= htmlspecialchars(implode("\n", $fixes)) ?></pre>
  <?php endif; ?>

  <p style="margin-top:40px;color:#666;font-size:14px">Delete this file before deploying to a public server.</p>
</body>
</html>
