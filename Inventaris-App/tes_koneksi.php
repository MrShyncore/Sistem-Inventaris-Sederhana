<?php
// ============================================
// tes_koneksi.php — Uji Koneksi Database
// ============================================
// HAPUS FILE INI SETELAH DIPAKAI!

require_once 'config/db.php';

echo "<pre style='font-family:monospace;font-size:15px;padding:20px;'>";
echo "=== TES KONEKSI DATABASE ===\n\n";

// 1. Koneksi
if ($conn->connect_error) {
    echo "❌ Koneksi gagal: " . $conn->connect_error . "\n";
    exit;
}
echo "✅ Koneksi berhasil\n";
echo "Host       : " . $DB_HOST . "\n";
echo "User       : " . $DB_USER . "\n";
echo "Database   : " . $DB_NAME . "\n";
echo "Charset    : " . $conn->character_set_name() . "\n";
echo "Server info: " . $conn->server_info . "\n\n";

// 2. Cek tabel
echo "=== CEK TABEL ===\n";
$tables = ['users', 'barang'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        $countResult = $conn->query("SELECT COUNT(*) AS total FROM $table");
        $count = $countResult->fetch_assoc()['total'];
        echo "✅ Tabel '$table' ada — $count baris\n";
    } else {
        echo "❌ Tabel '$table' TIDAK ADA — import database.sql dulu!\n";
    }
}
echo "\n";

// 3. Cek user admin
echo "=== CEK USER ADMIN ===\n";
$stmt = $conn->prepare("SELECT id, username, nama, LENGTH(password) AS len FROM users WHERE username = 'admin' LIMIT 1");
$stmt->execute();
$stmt->bind_result($id, $user, $nama, $len);

if ($stmt->fetch()) {
    echo "✅ User admin ditemukan\n";
    echo "   ID         : $id\n";
    echo "   Username   : $user\n";
    echo "   Nama       : $nama\n";
    echo "   Panjang hash: $len karakter " . ($len == 60 ? "(✅ valid)" : "(❌ harus 60)") . "\n";
} else {
    echo "❌ User admin TIDAK ADA — jalankan setup.php atau fix_admin.php\n";
}
$stmt->close();

echo "</pre>";