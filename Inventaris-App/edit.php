<?php
require_once 'config/config.php';
require_once 'includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($id <= 0) {
    $_SESSION['flash_error'] = $t['err_not_found'];
    header('Location: index.php');
    exit;
}

$errors = [];

// Ambil data yang akan diedit
$stmt = $conn->prepare("SELECT * FROM barang WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$barang = $result->fetch_assoc();
$stmt->close();

if (!$barang) {
    $_SESSION['flash_error'] = $t['err_not_found'];
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi
    $kode       = trim($_POST['kode_barang'] ?? '');
    $nama       = trim($_POST['nama_barang'] ?? '');
    $kategori   = trim($_POST['kategori'] ?? '');
    $jumlah     = trim($_POST['jumlah'] ?? '');
    $harga      = trim($_POST['harga'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    // Validasi
    if ($kode === '') {
        $errors[] = $t['err_kode'];
    } elseif (!preg_match('/^[A-Za-z0-9\-_]+$/', $kode)) {
        $errors[] = $t['err_kode_format'];
    }

    if ($nama === '') {
        $errors[] = $t['err_nama'];
    }

    if (!in_array($kategori, ['elektronik', 'atk', 'furniture', 'lainnya'], true)) {
        $errors[] = $t['err_kategori'];
    }

    $jumlahVal = filter_var($jumlah, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($jumlah === '') {
        $errors[] = $t['err_jumlah'];
    } elseif ($jumlahVal === false) {
        $errors[] = $t['err_jumlah_num'];
    }

    $hargaVal = filter_var($harga, FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
    if ($harga === '') {
        $errors[] = $t['err_harga'];
    } elseif ($hargaVal === false) {
        $errors[] = $t['err_harga_num'];
    }

    // Cek duplikasi kode (kecuali milik dirinya sendiri)
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM barang WHERE kode_barang = ? AND id <> ? LIMIT 1");
        $stmt->bind_param('si', $kode, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = $t['err_kode_dup'];
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE barang SET
            kode_barang=?, nama_barang=?, kategori=?, jumlah=?, harga=?, keterangan=?
            WHERE id=?");
        $jml = (int)$jumlahVal;
        $hrg = (float)$hargaVal;
        $stmt->bind_param('sssidsi', $kode, $nama, $kategori, $jml, $hrg, $keterangan, $id);

        if ($stmt->execute()) {
            $_SESSION['flash_success'] = $t['update_success'];
            $stmt->close();
            header('Location: index.php');
            exit;
        }
        $errors[] = $t['db_error'] . ': ' . $stmt->error;
        $stmt->close();
    }

    // Isi ulang form dengan input terakhir
    $barang = array_merge($barang, [
        'kode_barang' => $kode,
        'nama_barang' => $nama,
        'kategori'    => $kategori,
        'jumlah'      => $jumlah,
        'harga'       => $harga,
        'keterangan'  => $keterangan,
    ]);
}
?>
<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <h3 class="mb-3"><?= htmlspecialchars($t['edit_title']) ?> #<?= (int)$barang['id'] ?></h3>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="edit.php?id=<?= (int)$barang['id'] ?>" class="card card-body shadow-sm">
            <input type="hidden" name="id" value="<?= (int)$barang['id'] ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><?= htmlspecialchars($t['kode']) ?> *</label>
                    <input type="text" name="kode_barang" class="form-control"
                           value="<?= htmlspecialchars($barang['kode_barang']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= htmlspecialchars($t['nama']) ?> *</label>
                    <input type="text" name="nama_barang" class="form-control"
                           value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?= htmlspecialchars($t['kategori']) ?> *</label>
                    <select name="kategori" class="form-select" required>
                        <?php foreach (['elektronik', 'atk', 'furniture', 'lainnya'] as $kat): ?>
                            <option value="<?= $kat ?>"
                                <?= $barang['kategori'] === $kat ? 'selected' : '' ?>>
                                <?= htmlspecialchars(kategoriLabel($kat)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= htmlspecialchars($t['jumlah']) ?> *</label>
                    <input type="number" name="jumlah" class="form-control" min="0"
                           value="<?= htmlspecialchars($barang['jumlah']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= htmlspecialchars($t['harga']) ?> *</label>
                    <input type="number" step="0.01" name="harga" class="form-control" min="0"
                           value="<?= htmlspecialchars($barang['harga']) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label"><?= htmlspecialchars($t['keterangan']) ?></label>
                    <textarea name="keterangan" rows="3" class="form-control"><?= htmlspecialchars($barang['keterangan']) ?></textarea>
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