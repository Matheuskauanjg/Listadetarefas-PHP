<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'todo_app');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tabelaExiste = $pdo->query("SHOW TABLES LIKE 'usuarios'")->rowCount() > 0;

    if (!$tabelaExiste) {
        $sql = file_get_contents(__DIR__ . '/database.sql');
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
        $queries = explode(';', $sql);
        foreach($queries as $query) {
            if(trim($query)) {
                $pdo->exec($query);
            }
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    }
} catch(PDOException $e) {
    die("ERRO: Não foi possível conectar ao banco de dados. " . $e->getMessage());
}
?>
