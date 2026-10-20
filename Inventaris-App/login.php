<?php
require_once 'config/config.php';

// Jika sudah login, langsung ke index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors   = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi kelengkapan
    if ($username === '' || $password === '') {
        $errors[] = $t['err_login_empty'];
    } else {
        // Prepared statement (aman dari SQL injection)
        $stmt = $conn->prepare("SELECT id, username, password, nama FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $user, $hash, $nama);
            $stmt->fetch();

            if (password_verify($password, $hash)) {
                // Regenerasi session ID untuk mencegah session fixation
                session_regenerate_id(true);
                $_SESSION['user_id']  = $id;
                $_SESSION['username'] = $user;
                $_SESSION['nama']     = $nama;

                $stmt->close();
                header('Location: index.php');
                exit;
            }
        }
        $errors[] = $t['err_login_failed'];
        $stmt->close();
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3 text-center"><?= htmlspecialchars($t['login_title']) ?></h4>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="login.php" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label"><?= htmlspecialchars($t['username']) ?></label>
                        <input type="text" name="username" class="form-control"
                               value="<?= htmlspecialchars($username) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= htmlspecialchars($t['password']) ?></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <?= htmlspecialchars($t['sign_in']) ?>
                    </button>
                </form>

                <p class="text-muted small mt-3 mb-0 text-center">
                    Default: <code>admin</code> / <code>admin123</code>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>