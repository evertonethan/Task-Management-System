<?php
// api/auth.php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

// Setar cabeçalhos para API
header('Content-Type: application/json');

// Configurações CORS completas
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400'); // Cache para 24 horas

// Para requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verificar se a requisição é POST ou GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Método não permitido. Use POST ou GET.']);
    exit;
}

// Receber e decodificar dados de várias fontes possíveis
$data = [];

// Verificando se há dados POST tradicionais (form)
if (!empty($_POST)) {
    $data = $_POST;
    error_log("API auth.php: Dados recebidos via POST: " . print_r($_POST, true));
}
// Verificando se há dados JSON 
else {
    $json_data = file_get_contents('php://input');
    if (!empty($json_data)) {
        $decoded = json_decode($json_data, true);
        if ($decoded !== null) {
            $data = $decoded;
            error_log("API auth.php: Dados recebidos via JSON: " . print_r($data, true));
        } else {
            error_log("API auth.php: Erro ao decodificar JSON: " . json_last_error_msg());
        }
    }
}

// Se não há dados POST nem JSON, verificar dados GET
if (empty($data) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = $_GET;
    error_log("API auth.php: Dados recebidos via GET: " . print_r($_GET, true));
}

// Verificar se action existe nos parâmetros GET, mesmo que tenhamos dados POST/JSON
if (isset($_GET['action']) && !isset($data['action'])) {
    $data['action'] = $_GET['action'];
}

// Depuração - imprimir todos os dados disponíveis
error_log("API auth.php: Headers: " . print_r(getallheaders(), true));
error_log("API auth.php: Método: " . $_SERVER['REQUEST_METHOD']);
error_log("API auth.php: Dados finais: " . print_r($data, true));

// Para login e registro, não exigimos dados completos se vieram via GET
$bypass_empty_check = ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']));

// Verificar se os dados foram recebidos corretamente
if (empty($data) && !$bypass_empty_check) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'error' => 'Dados inválidos ou mal formatados',
        'debug' => [
            'method' => $_SERVER['REQUEST_METHOD'],
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'não definido',
            'get_params' => $_GET,
            'raw_input' => file_get_contents('php://input')
        ]
    ]);
    exit;
}

// Instanciar a classe User
$user = new User();

// Determinar a ação com base na rota
$action = isset($data['action']) ? $data['action'] : '';

// Se a ação não estiver nos dados, verificar se está na URL
if (empty($action) && isset($_GET['action'])) {
    $action = $_GET['action'];
}

error_log("API auth.php: Ação determinada: " . $action);

switch ($action) {
    case 'login':
        handleLogin($user, $data);
        break;

    case 'register':
        handleRegister($user, $data);
        break;

    case 'logout':
        handleLogout();
        break;

    default:
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Endpoint não encontrado. Ação esperada: login, register ou logout']);
        break;
}

// Função para login
function handleLogin($user, $data)
{
    // Depuração
    error_log("Função handleLogin iniciada: " . print_r($data, true));

    // Validar dados recebidos
    if (!isset($data['username']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Usuário e senha são obrigatórios']);
        return;
    }

    $username = $data['username'];
    $password = $data['password'];

    // Depuração
    error_log("Tentativa de login para usuário: " . $username);

    try {
        // Tentar fazer login
        $result = $user->login($username, $password);

        // Depuração
        error_log("Resultado do login: " . ($result ? "sucesso" : "falha"));

        if ($result) {
            // Login bem-sucedido, iniciar sessão
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['username'] = $result['username'];

            // Registrar início da sessão
            session_regenerate_id(true);

            // Depuração
            error_log("Sessão iniciada com sucesso para: " . $username . ", ID: " . $result['id']);

            // Retornar dados do usuário (exceto senha)
            unset($result['password']);

            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'user' => $result
            ]);
        } else {
            // Login falhou
            http_response_code(401); // Unauthorized
            echo json_encode([
                'success' => false,
                'error' => 'Usuário ou senha incorretos'
            ]);
        }
    } catch (Exception $e) {
        // Registrar erro detalhado
        error_log("Erro durante login: " . $e->getMessage() . "\n" . $e->getTraceAsString());

        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'error' => 'Erro ao realizar login: ' . $e->getMessage()
        ]);
    }
}

// Função para registro
function handleRegister($user, $data)
{
    // Depuração
    error_log("Função handleRegister iniciada: " . print_r($data, true));

    // Validar dados recebidos
    if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Todos os campos são obrigatórios',
            'debug' => [
                'dados_recebidos' => $data
            ]
        ]);
        return;
    }

    $username = trim($data['username']);
    $email = trim($data['email']);
    $password = $data['password'];

    // Validações simples
    $errors = [];

    if (strlen($username) < 3) {
        $errors[] = 'Nome de usuário deve ter pelo menos 3 caracteres';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail inválido';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Senha deve ter pelo menos 6 caracteres';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => implode(', ', $errors)
        ]);
        return;
    }

    // Depuração
    error_log("Validação básica de registro bem-sucedida para usuário: {$username}");

    try {
        // Tentar registrar o usuário
        $userId = $user->register($username, $email, $password);

        if ($userId) {
            // Registro bem-sucedido, iniciar sessão
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;

            // Regenerar ID da sessão para segurança
            session_regenerate_id(true);

            error_log("Registro bem-sucedido e sessão iniciada para: {$username} (ID: {$userId})");

            echo json_encode([
                'success' => true,
                'message' => 'Cadastro realizado com sucesso',
                'user_id' => $userId,
                'username' => $username
            ]);
        } else {
            error_log("Falha no registro - userId não retornado");
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Erro interno ao processar o cadastro'
            ]);
        }
    } catch (Exception $e) {
        error_log("Exceção no registro: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        http_response_code(400); // Bad Request para erros de validação
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

// Função para logout
function handleLogout()
{
    // Não precisamos de dados para logout

    // Limpar e destruir a sessão
    session_unset();
    session_destroy();

    echo json_encode([
        'success' => true,
        'message' => 'Logout realizado com sucesso'
    ]);
}
