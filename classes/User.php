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
        // Registro de depuração
        error_log("Iniciando registro de usuário: $username / $email");

        try {
            // Verificar se o usuário já existe
            $user = $this->findByUsername($username);
            if ($user) {
                error_log("Erro de registro: Nome de usuário '$username' já existe");
                throw new Exception("Nome de usuário já está sendo utilizado.");
            }

            // Verificar se o email já existe
            $user = $this->findByEmail($email);
            if ($user) {
                error_log("Erro de registro: Email '$email' já existe");
                throw new Exception("E-mail já está sendo utilizado.");
            }

            // Hash da senha
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            error_log("Senha hash criada para usuário: $username");

            // Inserir usuário
            $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $params = [
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword
            ];

            try {
                $userId = $this->db->insert($sql, $params);
                error_log("Usuário registrado com sucesso: $username (ID: $userId)");
                return $userId;
            } catch (Exception $e) {
                error_log("Erro ao inserir usuário no banco: " . $e->getMessage());
                throw new Exception("Erro ao registrar usuário: " . $e->getMessage());
            }
        } catch (Exception $e) {
            error_log("Exceção capturada no método register: " . $e->getMessage());
            throw $e; // Repassar a exceção
        }
    }

    // Autenticar usuário
    public function login($username, $password)
    {
        // Depuração
        error_log("User->login: Tentando login para usuário: $username");

        $sql = "SELECT * FROM users WHERE username = :username";
        $params = [':username' => $username];

        try {
            $user = $this->db->fetchOne($sql, $params);

            // Depuração
            error_log("User->login: Usuário encontrado: " . ($user ? "sim" : "não"));

            if (!$user) {
                error_log("User->login: Usuário não encontrado: $username");
                return false;
            }

            // Depuração - verificar hash da senha (sem expor a senha real)
            error_log("User->login: Comparando senhas para usuário: $username");
            error_log("User->login: Hash armazenado para usuário: " . substr($user['password'], 0, 10) . "...");

            // Verificar a senha
            if (password_verify($password, $user['password'])) {
                error_log("User->login: Senha verificada com sucesso para: $username");
                return $user;
            } else {
                error_log("User->login: Senha incorreta para: $username");
                return false;
            }
        } catch (Exception $e) {
            error_log("User->login: Erro ao tentar login: " . $e->getMessage());
            throw new Exception("Erro ao verificar usuário: " . $e->getMessage());
        }
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
