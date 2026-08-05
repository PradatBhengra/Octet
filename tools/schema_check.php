<?php
/*
 * Diagnostic page. Open in a browser to print the real database schema.
 * Delete this file before deploying anywhere public.
 */
require_once __DIR__ . '/../config/db.php';

echo "<pre style='font:13px monospace'>";
echo "Database: " . DB_NAME . "\n\n";

$tables = $conn->query("SHOW TABLES");
while ($t = $tables->fetch_array()) {
    $name = $t[0];
    echo "=== $name ===\n";
    $cols = $conn->query("SHOW COLUMNS FROM `$name`");
    while ($c = $cols->fetch_assoc()) {
        printf("  %-35s %-25s %s\n", $c['Field'], $c['Type'], $c['Null'] === 'NO' ? 'NOT NULL' : '');
    }
    $count = $conn->query("SELECT COUNT(*) AS n FROM `$name`")->fetch_assoc()['n'];
    echo "  rows: $count\n\n";
}
echo "</pre>";
