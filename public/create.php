<?php
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO inventories (item_name, quantity, category_id, location_id)
             VALUES (:item_name, :quantity, :category_id, :location_id)"
        );
        $stmt->execute([
            ':item_name'   => e($_POST['item_name']),
            ':quantity'    => (int)$_POST['quantity'],
            ':category_id' => (int)$_POST['category_id'],
            ':location_id' => (int)$_POST['location_id'],
        ]);
        set_flash('success', 'Data berhasil ditambahkan!');
    } catch (Exception $ex) {
        set_flash('danger', 'Gagal: ' . $ex->getMessage());
    }
    redirect('index.php');
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();
$locations  = $pdo->query("SELECT * FROM locations ORDER BY location_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Inventaris</title>
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
                <h4 class="card-title mb-4">Tambah Barang</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="item_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= (int)$c['category_id'] ?>"><?= e($c['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi</label>
                            <select name="location_id" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($locations as $l): ?>
                                    <option value="<?= (int)$l['location_id'] ?>"><?= e($l['location_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="quantity" class="form-control" min="0" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>