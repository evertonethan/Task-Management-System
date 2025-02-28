<?php
// config/db.php
// Configurações do banco de dados

// Configurações para ambiente de produção vs desenvolvimento
if (defined('IS_PRODUCTION') && IS_PRODUCTION) {
    // Produção
    define('DB_HOST', 'seu-servidor-producao');
    define('DB_NAME', 'seu_db_producao');
    define('DB_USER', 'seu_usuario_producao');
    define('DB_PASS', 'sua_senha_producao');
} else {
    // Desenvolvimento
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'todo_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}

// Charset e Collation
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

// Opções PDO
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
]);
