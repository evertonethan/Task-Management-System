<?php
// api/debug.php
// Arquivo para depurar problemas com a API

// Mostrar todos os erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurar cabeçalhos
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Função para depurar requisição
function debugRequest()
{
    $debug = array(
        'server' => array(
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'request_uri' => $_SERVER['REQUEST_URI'],
            'query_string' => $_SERVER['QUERY_STRING'] ?? '',
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'não definido',
            'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'não definido',
            'php_self' => $_SERVER['PHP_SELF'],
            'server_software' => $_SERVER['SERVER_SOFTWARE'],
            'remote_addr' => $_SERVER['REMOTE_ADDR'],
            'script_filename' => $_SERVER['SCRIPT_FILENAME'],
            'http_host' => $_SERVER['HTTP_HOST']
        ),
        'request' => array(
            'get' => $_GET,
            'post' => $_POST,
            'files' => $_FILES,
            'cookies' => $_COOKIE,
            'session' => isset($_SESSION) ? 'ativa' : 'inativa',
            'raw_input' => file_get_contents('php://input')
        ),
        'environment' => array(
            'php_version' => phpversion(),
            'extensions' => get_loaded_extensions(),
            'json_support' => function_exists('json_decode') && function_exists('json_encode'),
            'error_reporting' => error_reporting(),
        )
    );

    // Tentar decodificar o input se for JSON
    if (!empty($debug['request']['raw_input'])) {
        $json = json_decode($debug['request']['raw_input'], true);
        $debug['request']['parsed_json'] = $json !== null ? $json : 'Não é um JSON válido';
        $debug['request']['json_last_error'] = json_last_error_msg();
    }

    return $debug;
}

// Teste simples de conectividade com o banco de dados
function testDatabase()
{
    $dbResult = array(
        'config_loaded' => false,
        'connection_attempt' => false,
        'connection_success' => false,
        'error_message' => ''
    );

    // Verificar se os arquivos de configuração existem
    if (file_exists('../config/config.php') && file_exists('../config/db.php')) {
        try {
            require_once '../config/config.php';
            $dbResult['config_loaded'] = true;

            // Tentar conexão
            $dbResult['connection_attempt'] = true;
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Verificar conexão com uma query simples
            $stmt = $pdo->query("SELECT 1");
            $dbResult['connection_success'] = true;
        } catch (Exception $e) {
            $dbResult['error_message'] = $e->getMessage();
        }
    } else {
        $dbResult['error_message'] = 'Arquivos de configuração não encontrados';
    }

    return $dbResult;
}

// Responder com informações de depuração
echo json_encode([
    'success' => true,
    'message' => 'API Debug funcionando',
    'timestamp' => date('Y-m-d H:i:s'),
    'request_debug' => debugRequest(),
    'database_test' => testDatabase()
], JSON_PRETTY_PRINT);
