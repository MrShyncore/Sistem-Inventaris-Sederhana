<?php
// Proteksi halaman: wajib login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_error'] = $t['login_required'];
    header('Location: login.php');
    exit;
}