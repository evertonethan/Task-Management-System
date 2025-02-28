<?php
// classes/Task.php
class Task
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Criar nova tarefa
    public function create($userId, $title, $description, $priority = 'media')
    {
        $sql = "INSERT INTO tasks (user_id, title, description, priority) 
                VALUES (:user_id, :title, :description, :priority)";

        $params = [
            ':user_id' => $userId,
            ':title' => $title,
            ':description' => $description,
            ':priority' => $priority
        ];

        try {
            return $this->db->insert($sql, $params);
        } catch (Exception $e) {
            throw new Exception("Erro ao criar tarefa: " . $e->getMessage());
        }
    }

    // Buscar tarefa por ID
    public function findById($id, $userId)
    {
        $sql = "SELECT * FROM tasks WHERE id = :id AND user_id = :user_id";
        $params = [
            ':id' => $id,
            ':user_id' => $userId
        ];

        return $this->db->fetchOne($sql, $params);
    }

    // Listar todas as tarefas de um usuário
    public function findAll($userId, $filters = [])
    {
        $sql = "SELECT * FROM tasks WHERE user_id = :user_id";
        $params = [':user_id' => $userId];

        // Adicionar filtros se existirem
        if (!empty($filters)) {
            if (isset($filters['status']) && $filters['status'] !== 'todos') {
                $sql .= " AND status = :status";
                $params[':status'] = $filters['status'];
            }

            if (isset($filters['priority']) && $filters['priority'] !== 'todas') {
                $sql .= " AND priority = :priority";
                $params[':priority'] = $filters['priority'];
            }

            if (isset($filters['search']) && !empty($filters['search'])) {
                $sql .= " AND (title LIKE :search OR description LIKE :search)";
                $params[':search'] = "%{$filters['search']}%";
            }
        }

        // Ordenação
        $sql .= " ORDER BY ";
        if (isset($filters['order_by']) && in_array($filters['order_by'], ['created_at', 'priority', 'status'])) {
            $sql .= $filters['order_by'];
        } else {
            $sql .= "created_at";
        }

        $sql .= " ";
        if (isset($filters['order_direction']) && strtoupper($filters['order_direction']) === 'ASC') {
            $sql .= "ASC";
        } else {
            $sql .= "DESC";
        }

        return $this->db->fetchAll($sql, $params);
    }

    // Atualizar tarefa
    public function update($id, $userId, $data)
    {
        $allowedFields = ['title', 'description', 'status', 'priority'];
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

    // Atualizar status da tarefa
    public function updateStatus($id, $userId, $status)
    {
        if (!in_array($status, ['pendente', 'em_andamento', 'concluido'])) {
            throw new Exception("Status inválido");
        }

        $sql = "UPDATE tasks SET status = :status WHERE id = :id AND user_id = :user_id";
        $params = [
            ':id' => $id,
            ':user_id' => $userId,
            ':status' => $status
        ];

        return $this->db->update($sql, $params) > 0;
    }

    // Excluir tarefa
    public function delete($id, $userId)
    {
        $sql = "DELETE FROM tasks WHERE id = :id AND user_id = :user_id";
        $params = [
            ':id' => $id,
            ':user_id' => $userId
        ];

        return $this->db->delete($sql, $params) > 0;
    }

    // Contar tarefas por status
    public function countByStatus($userId)
    {
        $result = [
            'total' => 0,
            'pendente' => 0,
            'em_andamento' => 0,
            'concluido' => 0
        ];

        // Total de tarefas
        $sql = "SELECT COUNT(*) as count FROM tasks WHERE user_id = :user_id";
        $params = [':user_id' => $userId];
        $total = $this->db->fetchOne($sql, $params);
        $result['total'] = $total['count'] ?? 0;

        // Tarefas por status
        $sql = "SELECT status, COUNT(*) as count FROM tasks WHERE user_id = :user_id GROUP BY status";
        $statusCounts = $this->db->fetchAll($sql, $params);

        foreach ($statusCounts as $row) {
            $result[$row['status']] = $row['count'];
        }

        return $result;
    }
}
