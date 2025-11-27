<?php
// Basic site configuration for Proporção Áurea – Artesanato em Resina
// Update the constants below with your Hostinger credentials before deploying.

session_start();

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'proporcao');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

define('STORAGE_PATH', __DIR__ . '/storage');
define('INSCRICOES_PATH', STORAGE_PATH . '/inscricoes');
define('COMPROVATIVOS_PATH', STORAGE_PATH . '/comprovativos');

define('SITE_NAME', 'Proporção Áurea – Artesanato em Resina');
define('PAYMENT_DETAILS', "IBAN: XX00 0000 0000 0000 0000 0000\nMB Way: (+351) 900 000 000\nEntidade/Referência: 00000 / 000 000 000");

date_default_timezone_set('Europe/Lisbon');

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Erro ao ligar à base de dados: ' . $e->getMessage());
}

foreach ([STORAGE_PATH, INSCRICOES_PATH, COMPROVATIVOS_PATH] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}
