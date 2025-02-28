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
     * Buscar uma tarefa por ID
     * 
     * @param int $id ID da tarefa
     * @param int $userId ID do usuário (para segurança)
     * @return array|false Dados da tarefa ou false se não encontrada
     */
    public function findById($id, $userId)
    {
        $sql = "SELECT * FROM tasks WHERE id = :id AND user_id = :user_id";
        $params = [
            ':id' => $id,
            ':user_id' => $userId
        ];

        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Buscar todas as tarefas de um usuário com filtros
     * 
     * @param int $userId ID do usuário
     * @param array $filters Filtros (status, priority, search, order_by, order_direction)
     * @return array Lista de tarefas
     */
    public function findAll($userId, $filters = [])
    {
        $status = isset($filters['status']) ? $filters['status'] : 'todos';
        $priority = isset($filters['priority']) ? $filters['priority'] : 'todas';
        $search = isset($filters['search']) ? $filters['search'] : '';
        $orderBy = isset($filters['order_by']) ? $filters['order_by'] : 'created_at';
        $orderDirection = isset($filters['order_direction']) ? $filters['order_direction'] : 'DESC';

        // Validar ordem para evitar injeção SQL
        $allowedOrderFields = ['title', 'created_at', 'updated_at', 'status', 'priority'];
        $orderBy = in_array($orderBy, $allowedOrderFields) ? $orderBy : 'created_at';

        $orderDirection = strtoupper($orderDirection) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT * FROM tasks WHERE user_id = :user_id";
        $params = [':user_id' => $userId];

        if ($status !== 'todos') {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        if ($priority !== 'todas') {
            $sql .= " AND priority = :priority";
            $params[':priority'] = $priority;
        }

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $sql .= " ORDER BY {$orderBy} {$orderDirection}";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Contar tarefas por status
     * 
     * @param int $userId ID do usuário
     * @return array Contagem por status
     */
    public function countByStatus($userId)
    {
        $sql = "SELECT status, COUNT(*) as count FROM tasks WHERE user_id = :user_id GROUP BY status";
        $params = [':user_id' => $userId];

        $results = $this->db->fetchAll($sql, $params);

        // Inicializar contagem para todos os status
        $counts = [
            'pendente' => 0,
            'em_andamento' => 0,
            'concluido' => 0,
            'total' => 0
        ];

        // Preencher com os valores do banco
        foreach ($results as $result) {
            $counts[$result['status']] = (int) $result['count'];
            $counts['total'] += (int) $result['count'];
        }

        return $counts;
    }

    /**
     * Criar uma nova tarefa
     * 
     * @param int $userId ID do usuário
     * @param string $title Título da tarefa
     * @param string $description Descrição da tarefa
     * @param string $priority Prioridade da tarefa (baixa, media, alta)
     * @return int ID da tarefa criada
     */
    public function create($userId, $title, $description = '', $priority = 'media')
    {
        $sql = "INSERT INTO tasks (user_id, title, description, priority) 
                VALUES (:user_id, :title, :description, :priority)";

        $params = [
            ':user_id' => $userId,
            ':title' => $title,
            ':description' => $description,
            ':priority' => $priority
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Atualizar uma tarefa
     * 
     * @param int $id ID da tarefa
     * @param int $userId ID do usuário (para segurança)
     * @param array $data Dados a serem atualizados
     * @return bool True se a atualização for bem-sucedida
     */
    public function update($id, $userId, $data)
    {
        $allowedFields = ['title', 'description', 'priority', 'status'];
        $setFields = [];
        $params = [
            ':id' => $id,
            ':user_id' => $userId
        ];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $setFields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $value;
            }
        }

        if (empty($setFields)) {
            return false;
        }

        $setClause = implode(', ', $setFields);
        $sql = "UPDATE tasks SET {$setClause} WHERE id = :id AND user_id = :user_id";

        return $this->db->update($sql, $params) > 0;
    }

    /**
     * Atualizar apenas o status de uma tarefa
     * 
     * @param int $id ID da tarefa
     * @param int $userId ID do usuário (para segurança)
     * @param string $status Novo status
     * @return bool True se a atualização for bem-sucedida
     */
    public function updateStatus($id, $userId, $status)
    {
        if (!in_array($status, ['pendente', 'em_andamento', 'concluido'])) {
            throw new Exception("Status inválido.");
        }

        $sql = "UPDATE tasks SET status = :status WHERE id = :id AND user_id = :user_id";
        $params = [
            ':id' => $id,
            ':user_id' => $userId,
            ':status' => $status
        ];

        return $this->db->update($sql, $params) > 0;
    }

    /**
     * Excluir uma tarefa
     * 
     * @param int $id ID da tarefa
     * @param int $userId ID do usuário (para segurança)
     * @return bool True se a exclusão for bem-sucedida
     */
    public function delete($id, $userId)
    {
        $sql = "DELETE FROM tasks WHERE id = :id AND user_id = :user_id";
        $params = [
            ':id' => $id,
            ':user_id' => $userId
        ];

        return $this->db->delete($sql, $params) > 0;
    }
}
