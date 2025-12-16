<?php
// Conexión reutilizable a MySQL vía PDO.
// Si la base de datos `sigc` no existe, la crea e importa `sql/ddl_sigc.sql` si está disponible.
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = '127.0.0.1';
    $port = 3306;
    $db = 'sigc';
    $user = 'root';
    $pass = '';

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $dsnWithDb = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $dsnNoDb = "mysql:host={$host};port={$port};charset=utf8mb4";

    try {
        $pdo = new PDO($dsnWithDb, $user, $pass, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Si falla por base de datos desconocida, intentamos crearla
        try {
            $tmp = new PDO($dsnNoDb, $user, $pass, $options);
            $tmp->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $tmp->exec("USE `{$db}`");

            // Importar esquema si existe el archivo SQL
            $sqlFile = __DIR__ . '/../sql/ddl_sigc.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                // Separar por ';' y ejecutar sentencias individuales
                $stmts = array_filter(array_map('trim', explode(';', $sql)));
                foreach ($stmts as $stmt) {
                    if ($stmt === '') continue;
                    try {
                        $tmp->exec($stmt);
                    } catch (PDOException $eStmt) {
                        // Ignorar errores por sentencias vacías u otras pequeñas inconsistencias
                    }
                }
            }

            // Conectar finalmente a la base de datos creada
            $pdo = new PDO($dsnWithDb, $user, $pass, $options);
            return $pdo;
        } catch (PDOException $e2) {
            throw $e2;
        }
    }
}
