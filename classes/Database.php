<?php
// classes/Database.php
class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            die("Erro de conexão com o banco de dados: " . $e->getMessage());
        }
    }

    // Prevenir clonagem do objeto (Singleton)
    private function __clone() {}

    // Método para obter a instância da conexão
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Obter conexão PDO
    public function getConnection()
    {
        return $this->conn;
    }

    // Método para executar queries com prepared statements
    public function executeQuery($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erro na query: " . $e->getMessage());
            throw new Exception("Ocorreu um erro ao executar a operação. Por favor, tente novamente.");
        }
    }

    // Inserir dados e retornar o ID
    public function insert($sql, $params = [])
    {
        $this->executeQuery($sql, $params);
        return $this->conn->lastInsertId();
    }

    // Atualizar dados e retornar número de linhas afetadas
    public function update($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->rowCount();
    }

    // Deletar dados e retornar número de linhas afetadas
    public function delete($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->rowCount();
    }

    // Buscar uma única linha
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetch();
    }

    // Buscar múltiplas linhas
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetchAll();
    }

    // Contar resultados
    public function count($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->rowCount();
    }

    // Iniciar transação
    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }

    // Confirmar transação
    public function commit()
    {
        return $this->conn->commit();
    }

    // Reverter transação
    public function rollback()
    {
        return $this->conn->rollBack();
    }
}
