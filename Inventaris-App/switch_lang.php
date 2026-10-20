<?php
require_once 'config/config.php';

$lang = $_GET['lang'] ?? 'id';
if (in_array($lang, ['id', 'en'], true)) {
    $_SESSION['lang'] = $lang;
}

// Redirect aman: hanya ke referer di host yang sama
$target = 'index.php';
if (!empty($_SERVER['HTTP_REFERER'])) {
    $ref = parse_url($_SERVER['HTTP_REFERER']);
    if (isset($ref['host']) && $ref['host'] === ($_SERVER['HTTP_HOST'] ?? '')) {
        $target = $_SERVER['HTTP_REFERER'];
    }
}
header('Location: ' . $target);
exit;