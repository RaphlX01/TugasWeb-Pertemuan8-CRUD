<?php
require_once __DIR__ . '/functions.php';

$search = $_GET['search'] ?? '';

$sql = "SELECT i.*, c.category_name, l.location_name
        FROM inventories i
        LEFT JOIN categories c ON i.category_id = c.category_id
        LEFT JOIN locations  l ON i.location_id = l.location_id";

$params = [];
if ($search !== '') {
    $sql .= " WHERE i.item_name LIKE :search OR c.category_name LIKE :search";
    $params[':search'] = '%' . $search . '%';
}
$sql .= " ORDER BY i.inventory_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

$filename = 'laporan_inventaris_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

fputcsv($output, ['ID', 'Nama Barang', 'Kategori', 'Lokasi', 'Jumlah', 'Dibuat']);

foreach ($items as $i) {
    fputcsv($output, [
        $i['inventory_id'],
        $i['item_name'],
        $i['category_name'] ?? '-',
        $i['location_name'] ?? '-',
        $i['quantity'],
        $i['created_at'],
    ]);
}
fclose($output);
exit;