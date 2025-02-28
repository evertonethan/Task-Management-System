<?php
// api/tasks.php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/Task.php';

// Setar cabeçalhos para API
header('Content-Type: application/json');

// Configurações CORS completas para compatibilidade com diferentes servidores
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-Requested-With');
header('Access-Control-Max-Age: 86400'); // Cache para 24 horas

// Compatibilidade com diferentes servidores
// Alguns servidores não passam corretamente o corpo da requisição para métodos PUT e DELETE
if ($_SERVER['REQUEST_METHOD'] == 'PUT' || $_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (empty(file_get_contents('php://input'))) {
        // Se não houver dados no corpo, tente verificar se foram enviados via POST
        $_PUT = $_POST;
        $_DELETE = $_POST;
    } else {
        // Se houver dados no corpo, decodifique-os
        $_PUT = json_decode(file_get_contents('php://input'), true) ?: [];
        $_DELETE = $_PUT;
    }
}

// Para requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verificar autenticação
if (!isLoggedIn()) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Você precisa estar autenticado para acessar este recurso']);
    exit;
}

// Obter ID do usuário da sessão
$userId = $_SESSION['user_id'];

// Instanciar a classe Task
$task = new Task();

// Determinar a ação com base no método HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($task, $userId);
        break;

    case 'POST':
        handlePost($task, $userId);
        break;

    case 'PUT':
        handlePut($task, $userId);
        break;

    case 'DELETE':
        handleDelete($task, $userId);
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Método não permitido']);
        break;
}

// Função para lidar com GET (Listar tarefas)
function handleGet($task, $userId)
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    // Se um ID específico foi fornecido, buscar apenas essa tarefa
    if ($id) {
        try {
            $result = $task->findById($id, $userId);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'task' => $result
                ]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode([
                    'success' => false,
                    'error' => 'Tarefa não encontrada'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao buscar tarefa: ' . $e->getMessage()
            ]);
        }
    } else {
        // Buscar todas as tarefas com possíveis filtros
        $filters = [
            'status' => isset($_GET['status']) ? $_GET['status'] : 'todos',
            'priority' => isset($_GET['priority']) ? $_GET['priority'] : 'todas',
            'search' => isset($_GET['search']) ? $_GET['search'] : '',
            'order_by' => isset($_GET['order_by']) ? $_GET['order_by'] : 'created_at',
            'order_direction' => isset($_GET['order_direction']) ? $_GET['order_direction'] : 'DESC'
        ];

        try {
            $results = $task->findAll($userId, $filters);
            $counts = $task->countByStatus($userId);

            echo json_encode([
                'success' => true,
                'tasks' => $results,
                'counts' => $counts
            ]);
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao buscar tarefas: ' . $e->getMessage()
            ]);
        }
    }
}

// Função para lidar com POST (Criar tarefa)
function handlePost($task, $userId)
{
    // Receber e decodificar dados JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar se os dados foram recebidos corretamente
    if (!$data) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Dados inválidos ou mal formatados']);
        return;
    }

    // Validar dados recebidos
    if (!isset($data['title']) || trim($data['title']) === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Título é obrigatório']);
        return;
    }

    $title = trim($data['title']);
    $description = isset($data['description']) ? trim($data['description']) : '';
    $priority = isset($data['priority']) ? trim($data['priority']) : 'media';

    if (!in_array($priority, ['baixa', 'media', 'alta'])) {
        $priority = 'media';
    }

    try {
        // Criar a tarefa
        $taskId = $task->create($userId, $title, $description, $priority);

        if ($taskId) {
            $newTask = $task->findById($taskId, $userId);

            echo json_encode([
                'success' => true,
                'message' => 'Tarefa criada com sucesso',
                'task' => $newTask
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'error' => 'Erro ao criar tarefa: ' . $e->getMessage()
        ]);
    }
}

// Função para lidar com PUT (Atualizar tarefa)
function handlePut($task, $userId)
{
    // Verificar se o ID da tarefa foi fornecido
    if (!isset($_GET['id'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'ID da tarefa é obrigatório']);
        return;
    }

    $id = intval($_GET['id']);

    // Receber e decodificar dados JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar se os dados foram recebidos corretamente
    if (!$data) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Dados inválidos ou mal formatados']);
        return;
    }

    // Verificar se a tarefa existe e pertence ao usuário
    $taskData = $task->findById($id, $userId);
    if (!$taskData) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Tarefa não encontrada ou não pertence ao usuário']);
        return;
    }

    // Se apenas o status está sendo atualizado
    if (isset($data['status']) && count($data) === 1) {
        $status = $data['status'];

        try {
            $result = $task->updateStatus($id, $userId, $status);

            if ($result) {
                $updatedTask = $task->findById($id, $userId);

                echo json_encode([
                    'success' => true,
                    'message' => 'Status da tarefa atualizado com sucesso',
                    'task' => $updatedTask
                ]);
            } else {
                http_response_code(400); // Bad Request
                echo json_encode([
                    'success' => false,
                    'error' => 'Não foi possível atualizar o status da tarefa'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    } else {
        // Atualização completa da tarefa
        try {
            $result = $task->update($id, $userId, $data);

            if ($result) {
                $updatedTask = $task->findById($id, $userId);

                echo json_encode([
                    'success' => true,
                    'message' => 'Tarefa atualizada com sucesso',
                    'task' => $updatedTask
                ]);
            } else {
                http_response_code(400); // Bad Request
                echo json_encode([
                    'success' => false,
                    'error' => 'Não foi possível atualizar a tarefa'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao atualizar tarefa: ' . $e->getMessage()
            ]);
        }
    }
}

// Função para lidar com DELETE (Excluir tarefa)
function handleDelete($task, $userId)
{
    // Verificar se o ID da tarefa foi fornecido
    if (!isset($_GET['id'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'ID da tarefa é obrigatório']);
        return;
    }

    $id = intval($_GET['id']);

    // Verificar se a tarefa existe e pertence ao usuário
    $taskData = $task->findById($id, $userId);
    if (!$taskData) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Tarefa não encontrada ou não pertence ao usuário']);
        return;
    }

    try {
        $result = $task->delete($id, $userId);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Tarefa excluída com sucesso'
            ]);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success' => false,
                'error' => 'Não foi possível excluir a tarefa'
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'error' => 'Erro ao excluir tarefa: ' . $e->getMessage()
        ]);
    }
}
