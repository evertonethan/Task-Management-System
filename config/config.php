<?php
// config/config.php

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Alterar para 1 em produção com HTTPS

// Iniciar sessão se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Corrigir problema de caminhos com barras duplas
$path = trim($path, '/');
if (!empty($path)) {
    $path = '/' . $path;
}

$auto_base_url = $protocol . $host . $path . '/';

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
    // Remover barras iniciais para evitar URLs mal formadas
    $path = ltrim($path, '/');

    // Construir URL completa
    $url = BASE_URL . $path;

    // Garantir que não haja barras duplas
    $url = str_replace('//', '/', $url);

    // Restaurar protocolo caso tenha sido afetado
    $url = preg_replace('|^http:/|', 'http://', $url);
    $url = preg_replace('|^https:/|', 'https://', $url);

    header('Location: ' . $url);
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
