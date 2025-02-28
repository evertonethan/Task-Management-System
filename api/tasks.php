<?php
// api/tasks.php
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
require_once '../classes/Task.php';

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

// Verificar se o user_id no corpo da requisição corresponde ao usuário logado
if (isset($input['user_id']) && $input['user_id'] != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit;
}

// Instanciar classe Task
$task = new Task();

// Processar ação conforme solicitação
switch ($input['action']) {
    case 'list':
        // Listar tarefas do usuário
        try {
            $userId = $_SESSION['user_id'];
            $tasks = $task->getAllByUserId($userId);

            echo json_encode([
                'success' => true,
                'tasks' => $tasks
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    case 'create':
        // Validar dados recebidos
        if (!isset($input['title'])) {
            echo json_encode(['success' => false, 'error' => 'O título da tarefa é obrigatório.']);
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            $title = $input['title'];
            $description = $input['description'] ?? '';
            $priority = $input['priority'] ?? 'media';
            $dueDate = isset($input['due_date']) && !empty($input['due_date']) ? $input['due_date'] : null;

            // Criar tarefa
            $taskId = $task->create([
                'user_id' => $userId,
                'title' => $title,
                'description' => $description,
                'priority' => $priority,
                'due_date' => $dueDate,
                'status' => 'pendente'
            ]);

            echo json_encode([
                'success' => true,
                'task_id' => $taskId,
                'message' => 'Tarefa criada com sucesso.'
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    case 'update':
        // Validar dados recebidos
        if (!isset($input['id']) || !isset($input['title'])) {
            echo json_encode(['success' => false, 'error' => 'ID e título da tarefa são obrigatórios.']);
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            $taskId = $input['id'];

            // Verificar se a tarefa pertence ao usuário
            if (!$task->belongsToUser($taskId, $userId)) {
                echo json_encode(['success' => false, 'error' => 'Você não tem permissão para editar esta tarefa.']);
                exit;
            }

            $updateData = [
                'title' => $input['title'],
                'description' => $input['description'] ?? '',
                'priority' => $input['priority'] ?? 'media',
            ];

            // Adicionar due_date apenas se estiver definido e não vazio
            if (isset($input['due_date']) && !empty($input['due_date'])) {
                $updateData['due_date'] = $input['due_date'];
            }

            // Atualizar tarefa
            $success = $task->update($taskId, $updateData);

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tarefa atualizada com sucesso.'
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Não foi possível atualizar a tarefa.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    case 'update_status':
        // Validar dados recebidos
        if (!isset($input['id']) || !isset($input['status'])) {
            echo json_encode(['success' => false, 'error' => 'ID e status da tarefa são obrigatórios.']);
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            $taskId = $input['id'];
            $status = $input['status'];

            // Verificar se o status é válido
            if (!in_array($status, ['pendente', 'em_andamento', 'concluido'])) {
                echo json_encode(['success' => false, 'error' => 'Status inválido.']);
                exit;
            }

            // Verificar se a tarefa pertence ao usuário
            if (!$task->belongsToUser($taskId, $userId)) {
                echo json_encode(['success' => false, 'error' => 'Você não tem permissão para atualizar esta tarefa.']);
                exit;
            }

            // Atualizar status
            $success = $task->updateStatus($taskId, $status);

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Status atualizado com sucesso.'
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Não foi possível atualizar o status.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    case 'delete':
        // Validar dados recebidos
        if (!isset($input['id'])) {
            echo json_encode(['success' => false, 'error' => 'ID da tarefa é obrigatório.']);
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            $taskId = $input['id'];

            // Verificar se a tarefa pertence ao usuário
            if (!$task->belongsToUser($taskId, $userId)) {
                echo json_encode(['success' => false, 'error' => 'Você não tem permissão para excluir esta tarefa.']);
                exit;
            }

            // Excluir tarefa
            $success = $task->delete($taskId);

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tarefa excluída com sucesso.'
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Não foi possível excluir a tarefa.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    case 'get':
        // Validar dados recebidos
        if (!isset($input['id'])) {
            echo json_encode(['success' => false, 'error' => 'ID da tarefa é obrigatório.']);
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            $taskId = $input['id'];

            // Verificar se a tarefa pertence ao usuário
            if (!$task->belongsToUser($taskId, $userId)) {
                echo json_encode(['success' => false, 'error' => 'Você não tem permissão para visualizar esta tarefa.']);
                exit;
            }

            // Obter tarefa
            $taskData = $task->getById($taskId);

            if ($taskData) {
                echo json_encode([
                    'success' => true,
                    'task' => $taskData
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Tarefa não encontrada.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Ação desconhecida.']);
}
