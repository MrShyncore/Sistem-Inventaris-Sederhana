<?php
require_once 'config/config.php';
require_once 'includes/auth.php';

// Hanya boleh via POST (praktik lebih aman daripada GET)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_POST['id'];

$stmt = $conn->prepare("DELETE FROM barang WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['flash_success'] = $t['delete_success'];
} else {
    $_SESSION['flash_error'] = $t['err_not_found'];
}
$stmt->close();

header('Location: index.php');
exit;