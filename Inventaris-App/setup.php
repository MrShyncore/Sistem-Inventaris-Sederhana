<?php
require_once 'config/config.php';

$status = '';

// Hitung jumlah user
$stmt = $conn->prepare("SELECT COUNT(*) FROM users");
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    $status = $t['setup_exists'];
} else {
    $username = 'admin';
    $password = 'admin123';
    $nama     = 'Administrator';
    $hash     = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, nama) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $hash, $nama);
    $status = $stmt->execute() ? $t['setup_created'] : 'Gagal membuat user.';
    $stmt->close();
}
?>
<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4><?= htmlspecialchars($t['setup_title']) ?></h4>
                <p class="mb-3"><?= htmlspecialchars($status) ?></p>
                <a href="login.php" class="btn btn-primary"><?= htmlspecialchars($t['setup_login']) ?></a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>