<?php
// config/config.php
session_start();

// Configurações do Aplicativo
define('APP_NAME', 'Sistema de Gestão de Tarefas');
define('APP_VERSION', '1.0.0');

// Configuração de Ambiente (altere para false em produção)
define('IS_DEVELOPMENT', true);

// Obter o path base da URL atual
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$base_path = '/task-management-system/'; // Path base padrão

// Detectar o diretório base dinamicamente (compatível com localhost e produção)
if (IS_DEVELOPMENT) {
    // Obter diretório base para desenvolvimento
    $base_dir = dirname(dirname($_SERVER['SCRIPT_NAME']));
    if ($base_dir != '/' && $base_dir != '\\') {
        $base_path = $base_dir . '/';
    }

    define('BASE_URL', $protocol . $host . $base_path);
    define('API_URL', BASE_URL . 'api/');
} else {
    // URL para ambiente de produção
    define('BASE_URL', 'https://tools.devfrontend.com.br/task-management-system/');
    define('API_URL', BASE_URL . 'api/');
}

// Incluir configurações do banco de dados
require_once __DIR__ . '/db.php';

// Configurações de Timezone
date_default_timezone_set('America/Sao_Paulo');

// Funções auxiliares
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function redirect($path)
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

// Função para depuração
function debug($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    exit;
}
