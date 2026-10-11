<?php
require_once __DIR__ . '/functions.php';

$id = (int)($_GET['id'] ?? 0);

try {
    // === MULAI TRANSACTION ===
    $pdo->beginTransaction();

    // Ambil data sebelum hapus (untuk log)
    $stmt = $pdo->prepare("SELECT * FROM inventories WHERE inventory_id = :id");
    $stmt->execute([':id' => $id]);
    $item = $stmt->fetch();

    if (!$item) {
        throw new Exception('Data tidak ditemukan');
    }

    // Hapus
    $stmt = $pdo->prepare("DELETE FROM inventories WHERE inventory_id = :id");
    $stmt->execute([':id' => $id]);

    // Catat log
    $stmt = $pdo->prepare(
        "INSERT INTO activity_logs (action, table_name, record_id, description)
         VALUES ('DELETE', 'inventories', :id, :desc)"
    );
    $stmt->execute([
        ':id'   => $id,
        ':desc' => "Menghapus barang: {$item['item_name']} (qty: {$item['quantity']})"
    ]);

    // Commit
    $pdo->commit();
    set_flash('success', 'Data berhasil dihapus!');

} catch (Exception $ex) {
    $pdo->rollBack();
    set_flash('danger', 'Gagal hapus: ' . $ex->getMessage());
}

redirect('index.php');