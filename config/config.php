<?php
// config/config.php
session_start();

// Configurações gerais
define('APP_NAME', 'Sistema de Gestão de Tarefas');
define('APP_VERSION', '1.0.0');

// Ambiente (true para produção, false para desenvolvimento)
define('IS_PRODUCTION', false);

// Configurações CORS para desenvolvimento
if (!IS_PRODUCTION) {
    // Permitir solicitações AJAX de qualquer origem em desenvolvimento
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    // Lidar com preflight OPTIONS
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// Detectar base URL automaticamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);
$path = rtrim($path, '/') . '/';
$auto_base_url = $protocol . $host . $path;

// URLs base (com detecção automática como backup)
if (IS_PRODUCTION) {
    define('BASE_URL', 'https://seu-dominio.com/');
    define('API_URL', 'https://seu-dominio.com/api/');
} else {
    // Você pode definir manualmente ou usar a detecção automática
    define('BASE_URL', $auto_base_url); // ou 'http://localhost/todo-list/'
    define('API_URL', $auto_base_url . 'api/'); // ou 'http://localhost/todo-list/api/'
}

// Tempo de expiração da sessão (2 horas)
define('SESSION_EXPIRATION', 7200);

// Configurações de segurança
define('CSRF_TOKEN_SECRET', 'SeuTokenSecretoAqui123!@#');

// Funções úteis
function redirect($path)
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function checkAuth()
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
}

// Incluir arquivo de configuração do banco de dados
require_once 'db.php';
