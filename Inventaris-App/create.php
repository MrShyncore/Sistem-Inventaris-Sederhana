<?php
require_once 'config/config.php';
require_once 'includes/auth.php';

$errors = [];
$old = [
    'kode_barang' => '',
    'nama_barang' => '',
    'kategori'    => '',
    'jumlah'      => '',
    'harga'       => '',
    'keterangan'  => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // === 1. SANITASI INPUT ===
    $old['kode_barang'] = trim($_POST['kode_barang'] ?? '');
    $old['nama_barang'] = trim($_POST['nama_barang'] ?? '');
    $old['kategori']    = trim($_POST['kategori'] ?? '');
    $old['jumlah']      = trim($_POST['jumlah'] ?? '');
    $old['harga']       = trim($_POST['harga'] ?? '');
    $old['keterangan']  = trim($_POST['keterangan'] ?? '');

    // === 2. VALIDASI (percabangan if-else) ===
    if ($old['kode_barang'] === '') {
        $errors[] = $t['err_kode'];
    } elseif (!preg_match('/^[A-Za-z0-9\-_]+$/', $old['kode_barang'])) {
        $errors[] = $t['err_kode_format'];
    }

    if ($old['nama_barang'] === '') {
        $errors[] = $t['err_nama'];
    }

    if (!in_array($old['kategori'], ['elektronik', 'atk', 'furniture', 'lainnya'], true)) {
        $errors[] = $t['err_kategori'];
    }

    if ($old['jumlah'] === '') {
        $errors[] = $t['err_jumlah'];
    } else {
        $jumlahVal = filter_var($old['jumlah'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($jumlahVal === false) {
            $errors[] = $t['err_jumlah_num'];
        }
    }

    if ($old['harga'] === '') {
        $errors[] = $t['err_harga'];
    } else {
        $hargaVal = filter_var($old['harga'], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
        if ($hargaVal === false) {
            $errors[] = $t['err_harga_num'];
        }
    }

    // Cek duplikasi kode barang
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM barang WHERE kode_barang = ? LIMIT 1");
        $stmt->bind_param('s', $old['kode_barang']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = $t['err_kode_dup'];
        }
        $stmt->close();
    }

    // === 3. SIMPAN KE DATABASE ===
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO barang
            (kode_barang, nama_barang, kategori, jumlah, harga, keterangan)
            VALUES (?, ?, ?, ?, ?, ?)");

        $jumlah = (int)$old['jumlah'];
        $harga  = (float)$old['harga'];

        $stmt->bind_param(
            'sssids',
            $old['kode_barang'], $old['nama_barang'], $old['kategori'],
            $jumlah, $harga, $old['keterangan']
        );

        if ($stmt->execute()) {
            $_SESSION['flash_success'] = $t['create_success'];
            $stmt->close();
            header('Location: index.php');
            exit;
        }
        $errors[] = $t['db_error'] . ': ' . $stmt->error;
        $stmt->close();
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <h3 class="mb-3"><?= htmlspecialchars($t['add_new']) ?></h3>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="create.php" class="card card-body shadow-sm">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><?= htmlspecialchars($t['kode']) ?> *</label>
                    <input type="text" name="kode_barang" class="form-control"
                           value="<?= htmlspecialchars($old['kode_barang']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= htmlspecialchars($t['nama']) ?> *</label>
                    <input type="text" name="nama_barang" class="form-control"
                           value="<?= htmlspecialchars($old['nama_barang']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= htmlspecialchars($t['kategori']) ?> *</label>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- <?= htmlspecialchars($t['kategori']) ?> --</option>
                        <?php foreach (['elektronik', 'atk', 'furniture', 'lainnya'] as $kat): ?>
                            <option value="<?= $kat ?>" <?= $old['kategori'] === $kat ? 'selected' : '' ?>>
                                <?= htmlspecialchars(kategoriLabel($kat)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= htmlspecialchars($t['jumlah']) ?> *</label>
                    <input type="number" name="jumlah" class="form-control" min="0"
                           value="<?= htmlspecialchars($old['jumlah']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= htmlspecialchars($t['harga']) ?> *</label>
                    <input type="number" step="0.01" name="harga" class="form-control" min="0"
                           value="<?= htmlspecialchars($old['harga']) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label"><?= htmlspecialchars($t['keterangan']) ?></label>
                    <textarea name="keterangan" rows="3" class="form-control"><?= htmlspecialchars($old['keterangan']) ?></textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><?= htmlspecialchars($t['save']) ?></button>
                <a href="index.php" class="btn btn-secondary"><?= htmlspecialchars($t['cancel']) ?></a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>