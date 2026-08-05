<?php
// Blocks the page unless a user of this role is signed in.
require_once __DIR__ . '/../../config/db.php';

if (empty($_SESSION['id']) || ($_SESSION['usertype'] ?? 0) != 1) {
    header("Location: ../../public/index.php");
    exit;
}
