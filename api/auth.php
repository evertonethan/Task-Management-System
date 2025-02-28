<?php
// api/auth.php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

// Configurações CORS
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Para requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verificar método da requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Método não permitido. Use POST.']);
    exit;
}

// Receber dados JSON
$data = json_decode(file_get_contents('php://input'), true);

// Verificar se os dados foram recebidos corretamente
if (!$data) {
    // Tentar ler do $_POST para compatibilidade
    $data = $_POST;

    // Se ainda estiver vazio, retornar erro
    if (empty($data)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Dados inválidos ou mal formatados.']);
        exit;
    }
}

// Verificar se a ação foi fornecida via parâmetro GET ou no corpo
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($data['action'])) {
    $action = $data['action'];
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Ação não especificada.']);
    exit;
}

// Instanciar a classe User
$user = new User();

// Determinar a ação
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
        echo json_encode(['error' => 'Ação não encontrada.']);
        break;
}

// Função para login
function handleLogin($user, $data)
{
    // Validar dados recebidos
    if (!isset($data['username']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nome de usuário e senha são obrigatórios.']);
        return;
    }

    $username = trim($data['username']);
    $password = $data['password'];

    try {
        $result = $user->login($username, $password);

        if ($result) {
            // Iniciar sessão
            session_start();
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['username'] = $result['username'];

            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso.',
                'user' => [
                    'id' => $result['id'],
                    'username' => $result['username'],
                    'email' => $result['email']
                ]
            ]);
        } else {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Nome de usuário ou senha incorretos.']);
        }
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Erro ao processar login: ' . $e->getMessage()]);
    }
}

// Função para registro
function handleRegister($user, $data)
{
    // Validar dados recebidos
    if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Todos os campos são obrigatórios.']);
        return;
    }

    $username = trim($data['username']);
    $email = trim($data['email']);
    $password = $data['password'];

    // Validações simples
    $errors = [];

    if (strlen($username) < 3) {
        $errors[] = 'Nome de usuário deve ter pelo menos 3 caracteres.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail inválido.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Senha deve ter pelo menos 6 caracteres.';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['error' => implode(' ', $errors)]);
        return;
    }

    try {
        // Tentar registrar o usuário
        $userId = $user->register($username, $email, $password);

        if ($userId) {
            // Iniciar sessão automaticamente após o registro
            session_start();
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;

            echo json_encode([
                'success' => true,
                'message' => 'Cadastro realizado com sucesso.',
                'user_id' => $userId
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno ao processar o cadastro.']);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Função para logout
function handleLogout()
{
    session_start();
    $_SESSION = array();
    session_destroy();

    echo json_encode([
        'success' => true,
        'message' => 'Logout realizado com sucesso.'
    ]);
}
