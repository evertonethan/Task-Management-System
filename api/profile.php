<?php
// api/profile.php
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

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Você precisa estar autenticado para acessar este recurso']);
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

// Verificar se a ação foi fornecida
if (!isset($data['action'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Ação não especificada.']);
    exit;
}

// Obter ID do usuário da sessão
$userId = $_SESSION['user_id'];

// Instanciar a classe User
$user = new User();

// Determinar a ação
$action = $data['action'];

switch ($action) {
    case 'update_profile':
        handleUpdateProfile($user, $userId, $data);
        break;

    case 'change_password':
        handleChangePassword($user, $userId, $data);
        break;

    default:
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Ação não encontrada.']);
        break;
}

// Função para atualizar perfil
function handleUpdateProfile($user, $userId, $data)
{
    // Validar dados recebidos
    if (!isset($data['username']) || !isset($data['email'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Nome de usuário e e-mail são obrigatórios.']);
        return;
    }

    $username = trim($data['username']);
    $email = trim($data['email']);

    // Validações simples
    if (strlen($username) < 3) {
        http_response_code(400);
        echo json_encode(['error' => 'Nome de usuário deve ter pelo menos 3 caracteres.']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'E-mail inválido.']);
        return;
    }

    try {
        // Preparar dados para atualização
        $updateData = [
            'username' => $username,
            'email' => $email
        ];

        // Tentar atualizar o perfil
        $result = $user->update($userId, $updateData);

        if ($result) {
            // Atualizar nome de usuário na sessão
            $_SESSION['username'] = $username;

            echo json_encode([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso.'
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Não foi possível atualizar o perfil. Verifique se os dados são diferentes dos atuais.']);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Função para alterar senha
function handleChangePassword($user, $userId, $data)
{
    // Validar dados recebidos
    if (!isset($data['current_password']) || !isset($data['new_password'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Senha atual e nova senha são obrigatórias.']);
        return;
    }

    $currentPassword = $data['current_password'];
    $newPassword = $data['new_password'];

    // Validação simples
    if (strlen($newPassword) < 6) {
        http_response_code(400);
        echo json_encode(['error' => 'A nova senha deve ter pelo menos 6 caracteres.']);
        return;
    }

    try {
        // Verificar primeiro se a senha atual está correta
        $userData = $user->findById($userId);

        if (!$userData || !password_verify($currentPassword, $userData['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Senha atual incorreta.']);
            return;
        }

        // Atualizar a senha
        $result = $user->updatePassword($userId, $newPassword);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Senha alterada com sucesso.'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Não foi possível alterar a senha.']);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
