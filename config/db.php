<?php
// config/db.php
// Configurações do banco de dados

// Verificar se a constante IS_DEVELOPMENT já está definida (pode estar em config.php)
if (!defined('IS_DEVELOPMENT')) {
    define('IS_DEVELOPMENT', true); // Valor padrão: ambiente de desenvolvimento
}

// Configurações para ambiente de produção vs desenvolvimento
if (!IS_DEVELOPMENT) {
    // Produção
    define('DB_HOST', 'localhost'); // Corrigido: o host deve ser 'localhost', não uma URL
    define('DB_NAME', 'devfro86_todo_list_system');
    define('DB_USER', 'devfro86_root');
    define('DB_PASS', 'Emanuel2033**Messias');
} else {
    // Desenvolvimento
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'todo_list_system');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}

// Charset e Collation - Certificando-se de que correspondem ao que é usado na classe Database
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

// Opções PDO - Estas são opções que podem ser usadas na classe Database
// Note que a classe Database já define as opções diretamente
$db_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE " . DB_COLLATION
];

// Se quiser usar esta constante na classe Database, defina-a
define('DB_OPTIONS', serialize($db_options));

// Define string DSN para reuso
define('DB_DSN', "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET);
