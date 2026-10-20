<?php
// ============================================
// hash_gen.php — Generator Hash Bcrypt
// ============================================
// HAPUS FILE INI SETELAH DIPAKAI!

$password = 'admin123';   // ← ganti password di sini jika perlu

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<pre style='font-family:monospace;font-size:16px;padding:20px;'>";
echo "Password   : " . htmlspecialchars($password) . "\n\n";
echo "Hash Bcrypt:\n";
echo "<textarea style='width:100%;height:60px;font-size:14px;font-family:monospace;'>"
   . htmlspecialchars($hash) . "</textarea>\n\n";
echo "Panjang    : " . strlen($hash) . " karakter\n";
echo "Verifikasi : " . (password_verify($password, $hash) ? "✅ COCOK" : "❌ SALAH") . "\n\n";
echo "Copy hash di atas, paste ke database.sql:\n";
echo "INSERT IGNORE INTO users (username, password, nama) VALUES\n";
echo "('admin', '" . htmlspecialchars($hash) . "', 'Administrator');\n";
echo "</pre>";