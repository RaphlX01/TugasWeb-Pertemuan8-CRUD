<?php
require_once __DIR__ . '/functions.php';

$search  = $_GET['search'] ?? '';
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;
$offset  = ($page - 1) * $perPage;

// Query dengan JOIN
$sql = "SELECT i.*, c.category_name, l.location_name
        FROM inventories i
        LEFT JOIN categories c ON i.category_id = c.category_id
        LEFT JOIN locations  l ON i.location_id = l.location_id";

$params = [];
if ($search !== '') {
    $sql .= " WHERE i.item_name LIKE :search OR c.category_name LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

$sql .= " ORDER BY i.inventory_id DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll();

// Count
$countSql = "SELECT COUNT(*) AS total FROM inventories i 
             LEFT JOIN categories c ON i.category_id = c.category_id";
if ($search !== '') $countSql .= " WHERE i.item_name LIKE :search OR c.category_name LIKE :search";
$stmt = $pdo->prepare($countSql);
if ($search !== '') $stmt->bindValue(':search', '%' . $search . '%');
$stmt->execute();
$totalData = (int)$stmt->fetch()['total'];
$totalPages = (int)ceil($totalData / $perPage);

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">📦 Inventaris</a>
            <div class="ms-auto">
                <a href="create.php" class="btn btn-primary btn-sm">+ Tambah</a>
                <a href="export.php" class="btn btn-success btn-sm">Export CSV</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h3>Daftar Inventaris</h3>
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari barang..." value="<?= e($search) ?>">
                <button class="btn btn-primary">Cari</button>
                <?php if ($search): ?>
                    <a href="index.php" class="btn btn-secondary">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (empty($items)): ?>
            <div class="alert alert-info">Tidak ada data.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $offset + 1; foreach ($items as $i): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= e($i['item_name']) ?></td>
                                <td><span class="badge bg-secondary"><?= e($i['category_name']) ?></span></td>
                                <td><?= e($i['location_name']) ?></td>
                                <td><?= (int)$i['quantity'] ?></td>
                                <td>
                                    <a href="edit.php?id=<?= (int)$i['inventory_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete.php?id=<?= (int)$i['inventory_id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin hapus barang ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">Prev</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>