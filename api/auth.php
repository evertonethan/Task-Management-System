<?php
// api/auth.php
header('Content-Type: application/json');

// Permitir requisições de origem cruzada (CORS) em ambiente de desenvolvimento
if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    // Para requisições OPTIONS (preflight)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }
}

// Incluir arquivos necessários
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

// Verificar o método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método não permitido.']);
    exit;
}

// Receber os dados do corpo da requisição
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos.']);
    exit;
}

// Instanciar classe User
$user = new User();

// Processar ação conforme solicitação
switch ($input['action']) {
    case 'login':
        // Validar dados recebidos
        if (!isset($input['username']) || !isset($input['password'])) {
            echo json_encode(['success' => false, 'error' => 'Usuário e senha são obrigatórios.']);
            exit;
        }

        $username = $input['username'];
        $password = $input['password'];

        // Tentar autenticar o usuário
        try {
            $result = $user->login($username, $password);

            if ($result) {
                // Iniciar sessão para o usuário
                $_SESSION['user_id'] = $result['id'];
                $_SESSION['username'] = $result['username'];
                $_SESSION['email'] = $result['email'];

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
                echo json_encode(['success' => false, 'error' => 'Usuário ou senha incorretos.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Erro ao autenticar: ' . $e->getMessage()]);
        }
        break;

    case 'register':
        // Validar dados recebidos
        if (!isset($input['username']) || !isset($input['password']) || !isset($input['email'])) {
            echo json_encode(['success' => false, 'error' => 'Todos os campos são obrigatórios.']);
            exit;
        }

        $username = $input['username'];
        $password = $input['password'];
        $email = $input['email'];

        // Tentar registrar o usuário
        try {
            $user_id = $user->register($username, $email, $password);

            echo json_encode([
                'success' => true,
                'message' => 'Usuário registrado com sucesso.',
                'user_id' => $user_id
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    case 'logout':
        // Limpar a sessão
        session_unset();
        session_destroy();

        echo json_encode(['success' => true, 'message' => 'Logout realizado com sucesso.']);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Ação desconhecida.']);
}
