<?php
// api/auth.php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

// Setar cabeçalhos para API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Para requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verificar se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Receber e decodificar dados JSON
$data = json_decode(file_get_contents('php://input'), true);

// Verificar se os dados foram recebidos corretamente
if (!$data) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Dados inválidos ou mal formatados']);
    exit;
}

// Instanciar a classe User
$user = new User();

// Determinar a ação com base na rota
$action = isset($_GET['action']) ? $_GET['action'] : '';

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
        echo json_encode(['error' => 'Endpoint não encontrado']);
        break;
}

// Função para login
function handleLogin($user, $data)
{
    // Validar dados recebidos
    if (!isset($data['username']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Usuário e senha são obrigatórios']);
        return;
    }

    $username = $data['username'];
    $password = $data['password'];

    try {
        // Tentar fazer login
        $result = $user->login($username, $password);

        if ($result) {
            // Login bem-sucedido, iniciar sessão
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['username'] = $result['username'];

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
    // Validar dados recebidos
    if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Todos os campos são obrigatórios']);
        return;
    }

    $username = $data['username'];
    $email = $data['email'];
    $password = $data['password'];

    // Validações simples
    if (strlen($username) < 3) {
        http_response_code(400);
        echo json_encode(['error' => 'Nome de usuário deve ter pelo menos 3 caracteres']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'E-mail inválido']);
        return;
    }

    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['error' => 'Senha deve ter pelo menos 6 caracteres']);
        return;
    }

    try {
        // Tentar registrar o usuário
        $userId = $user->register($username, $email, $password);

        if ($userId) {
            // Registro bem-sucedido, iniciar sessão
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;

            echo json_encode([
                'success' => true,
                'message' => 'Cadastro realizado com sucesso',
                'user_id' => $userId
            ]);
        }
    } catch (Exception $e) {
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
    // Limpar e destruir a sessão
    session_unset();
    session_destroy();

    echo json_encode([
        'success' => true,
        'message' => 'Logout realizado com sucesso'
    ]);
}
