<?php
// classes/User.php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Registrar um novo usuário.
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @return int ID do usuário registrado
     * @throws Exception
     */
    public function register($username, $email, $password)
    {
        // Validação dos campos
        if (empty($username) || empty($email) || empty($password)) {
            throw new Exception("Todos os campos são obrigatórios.");
        }

        // Verificar se o usuário já existe
        if ($this->findByUsername($username)) {
            throw new Exception("Nome de usuário já está sendo utilizado.");
        }

        // Verificar se o email já existe
        if ($this->findByEmail($email)) {
            throw new Exception("E-mail já está sendo utilizado.");
        }

        // Hash da senha
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Inserir usuário no banco de dados
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

    /**
     * Autenticar usuário.
     *
     * @param string $username
     * @param string $password
     * @return array|false Dados do usuário ou false se a autenticação falhar
     */
    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            return false;
        }

        $sql = "SELECT * FROM users WHERE username = :username";
        $params = [':username' => $username];

        $user = $this->db->fetchOne($sql, $params);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        return $user;
    }

    /**
     * Buscar usuário por ID.
     *
     * @param int $id
     * @return array|false Dados do usuário ou false se não encontrado
     */
    public function findById($id)
    {
        $sql = "SELECT id, username, email, created_at FROM users WHERE id = :id";
        $params = [':id' => $id];

        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Buscar usuário por username.
     *
     * @param string $username
     * @return array|false Dados do usuário ou false se não encontrado
     */
    public function findByUsername($username)
    {
        $sql = "SELECT id, username, email, created_at FROM users WHERE username = :username";
        $params = [':username' => $username];

        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Buscar usuário por email.
     *
     * @param string $email
     * @return array|false Dados do usuário ou false se não encontrado
     */
    public function findByEmail($email)
    {
        $sql = "SELECT id, username, email, created_at FROM users WHERE email = :email";
        $params = [':email' => $email];

        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Atualizar dados do usuário.
     *
     * @param int $id
     * @param array $data Dados para atualizar (username, email)
     * @return bool True se a atualização for bem-sucedida
     * @throws Exception
     */
    public function update($id, $data)
    {
        if (empty($data)) {
            throw new Exception("Nenhum dado fornecido para atualização.");
        }

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
            throw new Exception("Nenhum campo válido fornecido para atualização.");
        }

        $setClause = implode(', ', $setFields);
        $sql = "UPDATE users SET {$setClause} WHERE id = :id";

        return $this->db->update($sql, $params) > 0;
    }

    /**
     * Atualizar senha do usuário.
     *
     * @param int $id
     * @param string $newPassword
     * @return bool True se a atualização for bem-sucedida
     * @throws Exception
     */
    public function updatePassword($id, $newPassword)
    {
        if (empty($newPassword)) {
            throw new Exception("A nova senha não pode estar vazia.");
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $params = [
            ':id' => $id,
            ':password' => $hashedPassword
        ];

        return $this->db->update($sql, $params) > 0;
    }

    /**
     * Excluir usuário.
     *
     * @param int $id
     * @return bool True se a exclusão for bem-sucedida
     * @throws Exception
     */
    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $params = [':id' => $id];

        return $this->db->delete($sql, $params) > 0;
    }
}
