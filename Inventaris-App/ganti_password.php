<?php
// ============================================
// ganti_password.php — Ganti Password User
// ============================================
// HAPUS FILE INI SETELAH DIPAKAI!

require_once 'config/db.php';

// ============================================
// UBAH DI SINI:
// ============================================
$username     = 'admin';
$password_baru = 'PasswordBaru123!';   // ← ganti dengan password baru
// ============================================

echo "<pre style='font-family:monospace;font-size:15px;padding:20px;'>";
echo "=== GANTI PASSWORD ===\n\n";
echo "User           : $username\n";
echo "Password baru  : " . htmlspecialchars($password_baru) . "\n\n";

// 1. Cek user ada
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "❌ User '$username' tidak ditemukan\n";
    $stmt->close();
    exit;
}
$stmt->close();

// 2. Generate hash baru
$hash = password_hash($password_baru, PASSWORD_DEFAULT);
echo "Hash baru      : " . substr($hash, 0, 25) . "...\n";
echo "Panjang        : " . strlen($hash) . " karakter\n\n";

// 3. Update
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
$stmt->bind_param('ss', $hash, $username);

if ($stmt->execute()) {
    echo "✅ Password berhasil diubah (" . $stmt->affected_rows . " baris)\n\n";
} else {
    echo "❌ Gagal: " . $stmt->error . "\n";
}
$stmt->close();

// 4. Verifikasi
$stmt = $conn->prepare("SELECT password FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($hashDb);
$stmt->fetch();
$stmt->close();

echo "=== VERIFIKASI ===\n";
echo "Cek password baru : " . (password_verify($password_baru, $hashDb) ? "✅ COCOK" : "❌ GAGAL") . "\n\n";
echo "Login sekarang dengan:\n";
echo "  Username : $username\n";
echo "  Password : " . htmlspecialchars($password_baru) . "\n";
echo "</pre>";