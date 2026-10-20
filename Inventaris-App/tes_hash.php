<?php
// ============================================
// tes_hash.php — Uji Hash Bcrypt
// ============================================
// HAPUS FILE INI SETELAH DIPAKAI!

$hash = 'PASTE_HASH_DI_SINI';   // ← paste hash yang ingin diuji
$password = 'admin123';          // ← password yang ingin diuji

echo "<pre style='font-family:monospace;font-size:15px;padding:20px;'>";
echo "=== TES HASH ===\n\n";
echo "Hash      : " . htmlspecialchars($hash) . "\n";
echo "Panjang   : " . strlen($hash) . " karakter " . (strlen($hash) == 60 ? "(✅)" : "(❌ harus 60)") . "\n";
echo "Awalan    : " . substr($hash, 0, 7) . " " . (substr($hash, 0, 7) === '$2y$10$' ? "(✅)" : "(❌)") . "\n";
echo "Password  : " . htmlspecialchars($password) . "\n\n";

if (strlen($hash) < 20) {
    echo "❌ Hash terlalu pendek — belum diisi?\n";
    exit;
}

$cocok = password_verify($password, $hash);
echo "Hasil     : " . ($cocok ? "✅ COCOK" : "❌ TIDAK COCOK") . "\n";
echo "</pre>";