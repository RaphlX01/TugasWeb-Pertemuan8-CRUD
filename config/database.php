<?php
// ============================================
// PDO Singleton — Requirement
// ============================================
class Database {
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        $host    = 'localhost';
        $dbname  = 'inventaris_db';
        $dbuser  = 'root';
        $dbpass  = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $dbuser, $dbpass, $options);
        } catch (PDOException $e) {
            die('Koneksi database gagal. Silakan periksa konfigurasi database Anda.');
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }
}