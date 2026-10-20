<?php
// Bootstrap: session + bahasa + koneksi DB
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'id';
}

require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/db.php';