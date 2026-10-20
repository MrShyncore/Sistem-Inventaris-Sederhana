<?php
// ============================================
// tes_login.php — Uji Verifikasi Login
// ============================================
// HAPUS FILE INI SETELAH DIPAKAI!

require_once 'config/db.php';

$username = 'admin';
$password = 'admin123';

echo "<pre style='font-family:monospace;font-size:15px;padding:20px;'>";
echo "=== TES LOGIN ADMIN ===\n\n";
echo "Input username : $username\n";
echo "Input password : $password\n\n";

// Ambil user dari database
$stmt = $conn->prepare("SELECT id, username, password, nama FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($id, $user, $hash, $nama);

if (!$stmt->fetch()) {
    echo "❌ User '$username' TIDAK DITEMUKAN di database\n";
    echo "   Solusi: buka fix_admin.php\n";
    $stmt->close();
    exit;
}
$stmt->close();

echo "=== DATA DARI DATABASE ===\n";
echo "ID         : $id\n";
echo "Username   : $user\n";
echo "Nama       : $nama\n";
echo "Panjang hash: " . strlen($hash) . " karakter\n";
echo "10 char awal: " . substr($hash, 0, 10) . "\n";
echo "Hash lengkap: " . htmlspecialchars($hash) . "\n\n";

echo "=== VERIFIKASI ===\n";
$cocok = password_verify($password, $hash);
echo "password_verify('$password', hash_db) = " . ($cocok ? "✅ COCOK" : "❌ TIDAK COCOK") . "\n\n";

if ($cocok) {
    echo "✅ Login SEHARUSNYA berhasil.\n";
    echo "   Kalau masih gagal di login.php, cek:\n";
    echo "   1. Form method POST?\n";
    echo "   2. Nama field 'username' & 'password' benar?\n";
    echo "   3. Urutan bind_result sesuai SELECT?\n";
} else {
    echo "❌ Login AKAN GAGAL — hash di database tidak cocok dengan '$password'\n";
    echo "   Solusi: buka fix_admin.php untuk reset user admin\n";
}
echo "</pre>";