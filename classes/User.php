<?php
// classes/User.php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Registrar um novo usuário
    public function register($username, $email, $password)
    {
        // Verificar se o usuário já existe
        $user = $this->findByUsername($username);
        if ($user) {
            throw new Exception("Nome de usuário já está sendo utilizado.");
        }

        // Verificar se o email já existe
        $user = $this->findByEmail($email);
        if ($user) {
            throw new Exception("E-mail já está sendo utilizado.");
        }

        // Hash da senha
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Inserir usuário
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $params = [
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword
        ];

        try {
            $userId = $this->db->insert($sql, $params);
            return $userId;
        } catch (Exception $e) {
            throw new Exception("Erro ao registrar usuário: " . $e->getMessage());
        }
    }

    // Autenticar usuário
    public function login($username, $password)
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        $params = [':username' => $username];

        $user = $this->db->fetchOne($sql, $params);

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    // Buscar usuário por ID
    public function findById($id)
    {
        $sql = "SELECT id, username, email, created_at FROM users WHERE id = :id";
        $params = [':id' => $id];

        return $this->db->fetchOne($sql, $params);
    }

    // Buscar usuário por username
    public function findByUsername($username)
    {
        $sql = "SELECT id, username, email, created_at FROM users WHERE username = :username";
        $params = [':username' => $username];

        return $this->db->fetchOne($sql, $params);
    }

    // Buscar usuário por email
    public function findByEmail($email)
    {
        $sql = "SELECT id, username, email, created_at FROM users WHERE email = :email";
        $params = [':email' => $email];

        return $this->db->fetchOne($sql, $params);
    }

    // Atualizar dados do usuário
    public function update($id, $data)
    {
        $allowedFields = ['username', 'email'];
        $setFields = [];
        $params = [':id' => $id];

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
        $sql = "UPDATE users SET {$setClause} WHERE id = :id";

        return $this->db->update($sql, $params) > 0;
    }

    // Atualizar senha
    public function updatePassword($id, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $params = [
            ':id' => $id,
            ':password' => $hashedPassword
        ];

        return $this->db->update($sql, $params) > 0;
    }

    // Excluir usuário
    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $params = [':id' => $id];

        return $this->db->delete($sql, $params) > 0;
    }
}
