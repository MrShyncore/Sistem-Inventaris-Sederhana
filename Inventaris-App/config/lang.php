<?php
// Memuat file bahasa sesuai session
$lang = $_SESSION['lang'] ?? 'id';
$allowed = ['id', 'en'];

if (!in_array($lang, $allowed, true)) {
    $lang = 'id';
}

$langFile = __DIR__ . '/../lang/' . $lang . '.php';
$t = file_exists($langFile) ? require $langFile : require __DIR__ . '/../lang/id.php';

// Helper: label kategori berdasarkan kunci i18n
if (!function_exists('kategoriLabel')) {
    function kategoriLabel($key)
    {
        global $t;
        $map = [
            'elektronik' => 'kat_elektronik',
            'atk'        => 'kat_atk',
            'furniture'  => 'kat_furniture',
            'lainnya'    => 'kat_lainnya',
        ];
        return $t[$map[$key] ?? ''] ?? $key;
    }
}