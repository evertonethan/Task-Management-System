<?php
// classes/Task.php
class Task
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Criar nova tarefa
     * 
     * @param array $data Dados da tarefa (user_id, title, description, status, priority, due_date)
     * @return int ID da tarefa criada
     * @throws Exception
     */
    public function create($data)
    {
        // Validar dados mínimos
        if (!isset($data['user_id']) || !isset($data['title'])) {
            throw new Exception('Dados incompletos para criar tarefa.');
        }

        // Definir dados padrão
        $title = $data['title'];
        $description = $data['description'] ?? '';
        $status = $data['status'] ?? 'pendente';
        $priority = $data['priority'] ?? 'media';
        $dueDate = $data['due_date'] ?? null;
        $userId = $data['user_id'];

        // Validar status e prioridade
        if (!in_array($status, ['pendente', 'em_andamento', 'concluido'])) {
            throw new Exception('Status inválido.');
        }

        if (!in_array($priority, ['baixa', 'media', 'alta'])) {
            throw new Exception('Prioridade inválida.');
        }

        try {
            // Preparar consulta SQL
            $sql = "INSERT INTO tasks (user_id, title, description, status, priority, due_date, created_at) 
                    VALUES (:user_id, :title, :description, :status, :priority, :due_date, NOW())";

            $params = [
                ':user_id' => $userId,
                ':title' => $title,
                ':description' => $description,
                ':status' => $status,
                ':priority' => $priority,
                ':due_date' => $dueDate
            ];

            return $this->db->insert($sql, $params);
        } catch (Exception $e) {
            throw new Exception('Erro ao criar tarefa: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar tarefa existente
     * 
     * @param int $id ID da tarefa
     * @param array $data Dados para atualizar
     * @return bool
     * @throws Exception
     */
    public function update($id, $data)
    {
        try {
            // Preparar campos e valores para atualização
            $updateFields = [];
            $params = [':id' => $id];

            // Campos permitidos para atualização
            $allowedFields = ['title', 'description', 'status', 'priority', 'due_date'];

            foreach ($data as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $updateFields[] = "{$field} = :{$field}";
                    $params[":{$field}"] = $value;
                }
            }

            // Se não houver campos para atualizar, retornar
            if (empty($updateFields)) {
                return true;
            }

            // Preparar consulta SQL
            $sql = "UPDATE tasks SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = :id";

            return $this->db->update($sql, $params) > 0;
        } catch (Exception $e) {
            throw new Exception('Erro ao atualizar tarefa: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar apenas o status da tarefa
     * 
     * @param int $id ID da tarefa
     * @param string $status Novo status
     * @return bool
     * @throws Exception
     */
    public function updateStatus($id, $status)
    {
        // Validar status
        if (!in_array($status, ['pendente', 'em_andamento', 'concluido'])) {
            throw new Exception('Status inválido.');
        }

        try {
            // Preparar consulta SQL
            $sql = "UPDATE tasks SET status = :status, updated_at = NOW() WHERE id = :id";

            $params = [
                ':id' => $id,
                ':status' => $status
            ];

            return $this->db->update($sql, $params) > 0;
        } catch (Exception $e) {
            throw new Exception('Erro ao atualizar status: ' . $e->getMessage());
        }
    }

    /**
     * Excluir tarefa
     * 
     * @param int $id ID da tarefa
     * @return bool
     * @throws Exception
     */
    public function delete($id)
    {
        try {
            // Preparar consulta SQL
            $sql = "DELETE FROM tasks WHERE id = :id";

            $params = [':id' => $id];

            return $this->db->delete($sql, $params) > 0;
        } catch (Exception $e) {
            throw new Exception('Erro ao excluir tarefa: ' . $e->getMessage());
        }
    }

    /**
     * Obter todas as tarefas de um usuário
     * 
     * @param int $userId ID do usuário
     * @return array
     * @throws Exception
     */
    public function getAllByUserId($userId)
    {
        try {
            // Preparar consulta SQL
            $sql = "SELECT * FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC";

            $params = [':user_id' => $userId];

            return $this->db->fetchAll($sql, $params);
        } catch (Exception $e) {
            throw new Exception('Erro ao listar tarefas: ' . $e->getMessage());
        }
    }

    /**
     * Obter uma tarefa pelo ID
     * 
     * @param int $id ID da tarefa
     * @return array|false
     * @throws Exception
     */
    public function getById($id)
    {
        try {
            // Preparar consulta SQL
            $sql = "SELECT * FROM tasks WHERE id = :id";

            $params = [':id' => $id];

            return $this->db->fetchOne($sql, $params);
        } catch (Exception $e) {
            throw new Exception('Erro ao buscar tarefa: ' . $e->getMessage());
        }
    }

    /**
     * Verificar se a tarefa pertence ao usuário
     * 
     * @param int $taskId ID da tarefa
     * @param int $userId ID do usuário
     * @return bool
     * @throws Exception
     */
    public function belongsToUser($taskId, $userId)
    {
        try {
            // Preparar consulta SQL
            $sql = "SELECT COUNT(*) as count FROM tasks WHERE id = :task_id AND user_id = :user_id";

            $params = [
                ':task_id' => $taskId,
                ':user_id' => $userId
            ];

            $result = $this->db->fetchOne($sql, $params);

            return isset($result['count']) && $result['count'] > 0;
        } catch (Exception $e) {
            throw new Exception('Erro ao verificar proprietário da tarefa: ' . $e->getMessage());
        }
    }

    /**
     * Contar tarefas por status para um usuário
     * 
     * @param int $userId ID do usuário
     * @return array
     * @throws Exception
     */
    public function countByStatus($userId)
    {
        try {
            // Preparar consulta SQL
            $sql = "SELECT status, COUNT(*) as count FROM tasks WHERE user_id = :user_id GROUP BY status";

            $params = [':user_id' => $userId];

            $results = $this->db->fetchAll($sql, $params);

            // Preparar resultado formatado
            $counts = [
                'pendente' => 0,
                'em_andamento' => 0,
                'concluido' => 0
            ];

            foreach ($results as $row) {
                $counts[$row['status']] = (int)$row['count'];
            }

            $counts['total'] = array_sum($counts);

            return $counts;
        } catch (Exception $e) {
            throw new Exception('Erro ao contar tarefas: ' . $e->getMessage());
        }
    }

    /**
     * Pesquisar tarefas de um usuário
     * 
     * @param int $userId ID do usuário
     * @param string $query Termo de pesquisa
     * @return array
     * @throws Exception
     */
    public function search($userId, $query)
    {
        try {
            // Preparar consulta SQL
            $sql = "SELECT * FROM tasks 
                    WHERE user_id = :user_id 
                    AND (title LIKE :query OR description LIKE :query)
                    ORDER BY created_at DESC";

            $params = [
                ':user_id' => $userId,
                ':query' => "%{$query}%"
            ];

            return $this->db->fetchAll($sql, $params);
        } catch (Exception $e) {
            throw new Exception('Erro ao pesquisar tarefas: ' . $e->getMessage());
        }
    }
}
