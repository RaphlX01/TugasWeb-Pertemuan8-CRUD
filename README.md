# Tugas Rutin 8 — CRUD Inventaris (PHP Native + MySQL)

Aplikasi CRUD Inventaris sederhana menggunakan PHP Native dengan **PDO Singleton Pattern** dan **MySQL** sebagai penyimpanan data. Mengikuti pola **MVC sederhana**.

## Cara Menjalankan

1. Pastikan **XAMPP/Laragon** terpasang dengan **Apache** dan **MySQL** aktif.
2. Copy folder proyek ke `htdocs` (XAMPP) atau `www` (Laragon).
3. Buka **phpMyAdmin** → import file `schema.sql` (buat database `inventaris_db` + 4 tabel + seed data).
4. Sesuaikan konfigurasi database di `config/database.php` (host, user, password).
5. Akses di browser:
http://localhost/web_8/public/




## Struktur Database

| Tabel           | Fungsi                                                        |
| --------------- | ------------------------------------------------------------- |
| `categories`    | Kategori barang (Elektronik, ATK, Furnitur)                   |
| `locations`     | Lokasi penyimpanan (Gudang A, Gudang B, Lab Komputer)         |
| `inventories`   | Data inventaris (FK ke `categories` & `locations`)            |
| `activity_logs` | Log aktivitas (khusus aksi delete)                            |

## Checklist Requirement

- Database `inventaris_db` dengan 3+ tabel berelasi (FK)
- Konfigurasi PDO dengan 3 atribut wajib:
  - `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`
  - `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`
  - `PDO::ATTR_EMULATE_PREPARES => false`
- **Singleton Pattern** untuk koneksi database
- Semua query pakai **Prepared Statements** (anti SQL Injection)
- **JOIN** di halaman list (inventories + categories + locations)
- Form Create dengan dropdown dinamis
- Form Edit pre-filled
- Delete dengan konfirmasi JavaScript (`confirm()`)
- **Flash Message** (Post/Redirect/Get)
- Sanitasi output dengan `htmlspecialchars()`

## Bonus yang Diimplementasikan

- **Database Transaction** pada fitur Delete (`beginTransaction` → `commit` / `rollBack`)
- **Activity Log**: mencatat setiap aksi delete ke tabel `activity_logs`
- **Pencarian** produk berdasarkan nama/kategori (`WHERE LIKE`)
- **Pagination** (5 data per halaman, dengan tombol Prev/Next)
- **Export CSV** laporan produk (mendukung filter search)

## Konsep Penting

### 1. Singleton Pattern (`config/database.php`)

```php
class Database {
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() { /* koneksi PDO */ }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
Kenapa Singleton? Hanya ada 1 koneksi ke database yang dipakai bersama seluruh aplikasi — hemat resource, konsisten.

2. Transaction pada Delete (public/delete.php)
php
$pdo->beginTransaction();
try {
    // 1. Ambil data produk
    // 2. Hapus produk
    // 3. Catat ke activity_logs
    $pdo->commit();
} catch (Exception $ex) {
    $pdo->rollBack();
}
Kenapa Transaction? Kalau salah satu step gagal, semua dibatalkan (rollback) — data tetap konsisten.

3. Prepared Statements
php
$stmt = $pdo->prepare("SELECT * FROM inventories WHERE inventory_id = :id");
$stmt->execute([':id' => $id]);
Kenapa Prepared Statements? Input user tidak pernah digabung langsung ke query — anti SQL Injection.

Catatan Keamanan
Semua input user di-sanitasi dengan htmlspecialchars() sebelum ditampilkan (anti XSS).

Semua query pakai Prepared Statements (anti SQL Injection).

Transaction memastikan integritas data saat delete (produk + log harus sukses bareng).

Repository
text
TugasWeb-P8-CRUDInventaris
