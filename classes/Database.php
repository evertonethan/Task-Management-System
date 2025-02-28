<?php
// classes/Database.php
class Database
{
    private static $instance = null;
    private $pdo;

    // Construtor privado para Singleton
    private function __construct()
    {
        try {
            // Verificar se db.php foi incluído e DB_DSN está definido
            if (defined('DB_DSN')) {
                $dsn = DB_DSN;
            } else {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            }

            // Verificar se as opções foram definidas no arquivo de configuração
            if (defined('DB_OPTIONS')) {
                $options = unserialize(DB_OPTIONS);
            } else {
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
            }

            // Adicionar comando init para charset e collation se não estiver presente
            if (!isset($options[PDO::MYSQL_ATTR_INIT_COMMAND]) && defined('DB_COLLATION')) {
                $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES " . DB_CHARSET . " COLLATE " . DB_COLLATION;
            }

            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Erro na conexão com o banco de dados: ' . $e->getMessage());
        }
    }

    // Método para obter a instância única do banco de dados
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Getter para o objeto PDO
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Executa uma consulta com parâmetros
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parâmetros para a consulta
     * @return PDOStatement
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception('Erro na consulta: ' . $e->getMessage());
        }
    }

    /**
     * Busca todos os resultados
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parâmetros para a consulta
     * @return array
     */
    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Busca um único resultado
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parâmetros para a consulta
     * @return array|false
     */
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : false;
    }

    /**
     * Executa uma inserção e retorna o ID inserido
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parâmetros para a consulta
     * @return int
     */
    public function insert($sql, $params = [])
    {
        $this->query($sql, $params);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Executa uma atualização e retorna o número de linhas afetadas
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parâmetros para a consulta
     * @return int
     */
    public function update($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Executa uma exclusão e retorna o número de linhas afetadas
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parâmetros para a consulta
     * @return int
     */
    public function delete($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
}
