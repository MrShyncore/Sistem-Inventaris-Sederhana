<?php
// ============================================
// fix_admin.php — Reset User Admin
// ============================================
// HAPUS FILE INI SETELAH DIPAKAI!

require_once 'config/db.php';

$username = 'admin';
$password = 'admin123';
$nama     = 'Administrator';

// 1. Generate hash baru
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<pre style='font-family:monospace;font-size:15px;padding:20px;'>";
echo "=== FIX ADMIN USER ===\n\n";
echo "Hash baru : " . htmlspecialchars($hash) . "\n";
echo "Panjang   : " . strlen($hash) . " karakter\n\n";

// 2. Hapus admin lama (jika ada)
$stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$deleted = $stmt->affected_rows;
$stmt->close();
echo "Admin lama dihapus : " . $deleted . " baris\n";

// 3. Insert admin baru
$stmt = $conn->prepare("INSERT INTO users (username, password, nama) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $username, $hash, $nama);

if ($stmt->execute()) {
    echo "✅ Admin baru dibuat (id=" . $conn->insert_id . ")\n\n";
} else {
    echo "❌ Gagal insert: " . $stmt->error . "\n";
    exit;
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
echo "Hash di DB : " . substr($hashDb, 0, 25) . "...\n";
echo "Panjang    : " . strlen($hashDb) . " karakter\n";
echo "Cek admin123 : " . (password_verify($password, $hashDb) ? "✅ COCOK" : "❌ GAGAL") . "\n\n";
echo "Login sekarang dengan:\n";
echo "  Username : admin\n";
echo "  Password : admin123\n";
echo "</pre>";