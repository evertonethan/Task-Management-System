<?php
// api/profile.php
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

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
    exit;
}

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

// Obter o ID do usuário da sessão
$userId = $_SESSION['user_id'];

// Instanciar classe User
$user = new User();

// Processar ação conforme solicitação
switch ($input['action']) {
    case 'update_profile':
        // Validar dados recebidos
        if (!isset($input['username']) || !isset($input['email'])) {
            echo json_encode(['success' => false, 'error' => 'Nome de usuário e e-mail são obrigatórios.']);
            exit;
        }

        $username = $input['username'];
        $email = $input['email'];

        try {
            // Verificar se o nome de usuário já está em uso por outro usuário
            $existingUser = $user->findByUsername($username);
            if ($existingUser && $existingUser['id'] != $userId) {
                echo json_encode(['success' => false, 'error' => 'Este nome de usuário já está sendo utilizado.']);
                exit;
            }

            // Verificar se o e-mail já está em uso por outro usuário
            $existingEmail = $user->findByEmail($email);
            if ($existingEmail && $existingEmail['id'] != $userId) {
                echo json_encode(['success' => false, 'error' => 'Este e-mail já está sendo utilizado.']);
                exit;
            }

            // Atualizar os dados do usuário
            $updateData = [
                'username' => $username,
                'email' => $email
            ];

            $success = $user->update($userId, $updateData);

            if ($success) {
                // Atualizar a sessão se o nome de usuário mudou
                if ($_SESSION['username'] !== $username) {
                    $_SESSION['username'] = $username;
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Perfil atualizado com sucesso.'
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Não foi possível atualizar o perfil.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    case 'change_password':
        // Validar dados recebidos
        if (!isset($input['current_password']) || !isset($input['new_password'])) {
            echo json_encode(['success' => false, 'error' => 'Senha atual e nova senha são obrigatórias.']);
            exit;
        }

        $currentPassword = $input['current_password'];
        $newPassword = $input['new_password'];

        try {
            // Verificar se a senha atual está correta
            $userData = $user->findById($userId);

            if (!$userData) {
                echo json_encode(['success' => false, 'error' => 'Usuário não encontrado.']);
                exit;
            }

            // Verificar se a senha atual está correta usando o método login
            $authenticated = $user->login($userData['username'], $currentPassword);

            if (!$authenticated) {
                echo json_encode(['success' => false, 'error' => 'Senha atual incorreta.']);
                exit;
            }

            // Atualizar a senha
            $success = $user->updatePassword($userId, $newPassword);

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Senha alterada com sucesso.'
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Não foi possível alterar a senha.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    case 'get_profile':
        try {
            // Obter dados do usuário
            $userData = $user->findById($userId);

            if ($userData) {
                // Remover campos sensíveis
                unset($userData['password']);

                echo json_encode([
                    'success' => true,
                    'user' => $userData
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Usuário não encontrado.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Ação desconhecida.']);
}
