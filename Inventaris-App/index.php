<?php
require_once 'config/config.php';
require_once 'includes/auth.php';

// Pencarian (opsional)
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM barang
                            WHERE kode_barang LIKE ? OR nama_barang LIKE ?
                            ORDER BY id DESC");
    $like = '%' . $search . '%';
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM barang ORDER BY id DESC");
}
?>
<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h3 class="mb-0"><?= htmlspecialchars($t['barang_list']) ?></h3>
    <a href="create.php" class="btn btn-primary">+ <?= htmlspecialchars($t['add_new']) ?></a>
</div>

<form class="row g-2 mb-3" method="get" action="index.php">
    <div class="col-md-8">
        <input type="text" name="q" class="form-control"
               placeholder="<?= htmlspecialchars($t['search_placeholder']) ?>"
               value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100"><?= htmlspecialchars($t['search']) ?></button>
    </div>
</form>

<div class="table-responsive table-card">
    <table class="table table-striped table-bordered align-middle bg-white">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th><?= htmlspecialchars($t['kode']) ?></th>
                <th><?= htmlspecialchars($t['nama']) ?></th>
                <th><?= htmlspecialchars($t['kategori']) ?></th>
                <th class="text-end"><?= htmlspecialchars($t['jumlah']) ?></th>
                <th class="text-end"><?= htmlspecialchars($t['harga']) ?></th>
                <th><?= htmlspecialchars($t['keterangan']) ?></th>
                <th class="text-center"><?= htmlspecialchars($t['aksi']) ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): $no = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="#"><?= $no++ ?></td>
                    <td data-label="<?= htmlspecialchars($t['kode']) ?>"><?= htmlspecialchars($row['kode_barang']) ?></td>
                    <td data-label="<?= htmlspecialchars($t['nama']) ?>"><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td data-label="<?= htmlspecialchars($t['kategori']) ?>"><?= htmlspecialchars(kategoriLabel($row['kategori'])) ?></td>
                    <td data-label="<?= htmlspecialchars($t['jumlah']) ?>" class="text-end"><?= (int)$row['jumlah'] ?></td>
                    <td data-label="<?= htmlspecialchars($t['harga']) ?>" class="text-end">Rp <?= number_format((float)$row['harga'], 0, ',', '.') ?></td>
                    <td data-label="<?= htmlspecialchars($t['keterangan']) ?>"><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td data-label="" class="text-center text-nowrap">
                        <a href="edit.php?id=<?= (int)$row['id'] ?>"
                           class="btn btn-sm btn-warning"><?= htmlspecialchars($t['edit']) ?></a>
                        <form action="delete.php" method="post" class="d-inline"
                              onsubmit="return confirm('<?= htmlspecialchars($t['confirm_delete']) ?>');">
                            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                                <?= htmlspecialchars($t['delete']) ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" data-label="" class="text-center text-muted"><?= htmlspecialchars($t['no_data']) ?></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>