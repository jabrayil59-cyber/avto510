<?php
// Verilənlər bazası konfiqurasiyası
define('DB_HOST', 'localhost');
define('DB_NAME', 'u552663723_avto510_db');
define('DB_USER', 'u552663723_avto510_user2');
define('DB_PASS', 'C5910c610');
define('DB_CHARSET', 'utf8mb4');

// Bağlantı
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT         => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Baza bağlantısı uğursuz oldu: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}
?>
