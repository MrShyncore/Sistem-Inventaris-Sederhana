<?php
$isLoggedIn  = isset($_SESSION['user_id']);
$currentLang = $_SESSION['lang'] ?? 'id';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($t['app_name']) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><?= htmlspecialchars($t['app_name']) ?></a>

        <div class="d-flex align-items-center gap-2">
            <div class="btn-group btn-group-sm" role="group">
                <a href="switch_lang.php?lang=id"
                   class="btn btn-<?= $currentLang === 'id' ? 'primary' : 'outline-light' ?>">ID</a>
                <a href="switch_lang.php?lang=en"
                   class="btn btn-<?= $currentLang === 'en' ? 'primary' : 'outline-light' ?>">EN</a>
            </div>

            <?php if ($isLoggedIn): ?>
                <a class="btn btn-sm btn-outline-light" href="index.php"><?= htmlspecialchars($t['nav_barang']) ?></a>
                <a class="btn btn-sm btn-success" href="create.php"><?= htmlspecialchars($t['nav_add']) ?></a>
                <span class="text-white-50 small">
                    <?= htmlspecialchars($t['welcome']) ?>, <strong><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></strong>
                </span>
                <a class="btn btn-sm btn-outline-light" href="logout.php"><?= htmlspecialchars($t['logout']) ?></a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="container mb-5">
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>