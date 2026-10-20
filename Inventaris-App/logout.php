<?php
require_once 'config/config.php';

// Hapus data otentikasi, tetap simpan preferensi bahasa
unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['nama']);
session_regenerate_id(true);

header('Location: login.php');
exit;