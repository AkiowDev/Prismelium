<?php
declare(strict_types=1);

function prismelium_pdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('PRISMELIUM_DB_HOST') ?: 'localhost';
    $port = getenv('PRISMELIUM_DB_PORT') ?: '3306';
    $database = getenv('PRISMELIUM_DB_NAME') ?: 'Prismelium';
    $username = getenv('PRISMELIUM_DB_USER') ?: 'root';
    $password = getenv('PRISMELIUM_DB_PASSWORD') ?: '';

    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $exception) {
        error_log('Prismelium database connection failed: ' . $exception->getMessage());
        http_response_code(500);
        throw new RuntimeException('La connexion à la base de données est indisponible.');
    }

    return $pdo;
}

$pdo = prismelium_pdo();
