<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

try {
    $pdo = new PDO(
        "sqlsrv:Server=" . DB_HOST . ";Database=" . DB_NAME,
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('DB connection error');
}
