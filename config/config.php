<?php
// config/config.php
session_start();

// Configurações do Aplicativo
define('APP_NAME', 'Sistema de Gestão de Tarefas');
define('APP_VERSION', '1.0.0');

// Configuração de Ambiente (altere para false em produção)
define('IS_DEVELOPMENT', true);

// Configurações da Base URL
$base_path = '/Task-Management-System/'; // Altere para o seu caminho base

// Define a URL base com base no ambiente
if (IS_DEVELOPMENT) {
    define('BASE_URL', 'http://localhost' . $base_path);
    define('API_URL', BASE_URL . 'api/');
} else {
    define('BASE_URL', 'https://seudominio.com' . $base_path);
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
