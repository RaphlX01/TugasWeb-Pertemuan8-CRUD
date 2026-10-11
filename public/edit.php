<?php
require_once __DIR__ . '/functions.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare(
            "UPDATE inventories SET 
                item_name = :item_name,
                quantity = :quantity,
                category_id = :category_id,
                location_id = :location_id
             WHERE inventory_id = :id"
        );
        $stmt->execute([
            ':id'          => $id,
            ':item_name'   => e($_POST['item_name']),
            ':quantity'    => (int)$_POST['quantity'],
            ':category_id' => (int)$_POST['category_id'],
            ':location_id' => (int)$_POST['location_id'],
        ]);
        set_flash('success', 'Data berhasil diupdate!');
    } catch (Exception $ex) {
        set_flash('danger', 'Gagal: ' . $ex->getMessage());
    }
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT * FROM inventories WHERE inventory_id = :id");
$stmt->execute([':id' => $id]);
$item = $stmt->fetch();

if (!$item) {
    set_flash('danger', 'Data tidak ditemukan');
    redirect('index.php');
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();
$locations  = $pdo->query("SELECT * FROM locations ORDER BY location_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">📦 Inventaris</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-4">Edit Barang</h4>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= (int)$item['inventory_id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="item_name" class="form-control" 
                               value="<?= e($item['item_name']) ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= (int)$c['category_id'] ?>"
                                        <?= $c['category_id'] == $item['category_id'] ? 'selected' : '' ?>>
                                        <?= e($c['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi</label>
                            <select name="location_id" class="form-select" required>
                                <?php foreach ($locations as $l): ?>
                                    <option value="<?= (int)$l['location_id'] ?>"
                                        <?= $l['location_id'] == $item['location_id'] ? 'selected' : '' ?>>
                                        <?= e($l['location_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="quantity" class="form-control" 
                               value="<?= (int)$item['quantity'] ?>" min="0" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>