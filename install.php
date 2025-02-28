<?php
// install.php - Script para criação das tabelas e configuração inicial
require_once 'config/config.php';
require_once 'classes/Database.php';

// Definir título da página
$pageTitle = 'Instalação do Sistema';

// Incluir cabeçalho
require_once 'includes/header.php';

// Variáveis para controle
$errorsFound = false;
$successMessages = [];
$errorMessages = [];

// Função para exibir mensagem
function displayMessage($message, $type = 'info')
{
    echo '<div class="message message-' . $type . '">' . $message . '</div>';
}

// Verificar permissões de escrita
$writeableFolders = [
    'uploads' => is_writable('uploads'),
    'temp' => is_writable('temp')
];

foreach ($writeableFolders as $folder => $isWriteable) {
    if (!$isWriteable) {
        $errorsFound = true;
        $errorMessages[] = "A pasta '{$folder}' não tem permissões de escrita.";
    }
}

// Testar conexão ao banco de dados
try {
    $db = Database::getInstance();
    $successMessages[] = "Conexão com o banco de dados estabelecida com sucesso.";

    // Se estiver conectado, criar as tabelas
    if (isset($_POST['create_tables']) && $_POST['create_tables'] == 1) {
        // SQL para criar as tabelas
        $createTablesSQL = [
            'users' => "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            'tasks' => "CREATE TABLE IF NOT EXISTS tasks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(100) NOT NULL,
                description TEXT,
                status ENUM('pendente', 'em_andamento', 'concluido') DEFAULT 'pendente',
                priority ENUM('baixa', 'media', 'alta') DEFAULT 'media',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )"
        ];

        // Criar índices
        $createIndexesSQL = [
            "CREATE INDEX IF NOT EXISTS idx_user_id ON tasks(user_id)",
            "CREATE INDEX IF NOT EXISTS idx_status ON tasks(status)",
            "CREATE INDEX IF NOT EXISTS idx_priority ON tasks(priority)"
        ];

        $allTablesCreated = true;

        // Executar criação de tabelas
        foreach ($createTablesSQL as $table => $sql) {
            try {
                $db->query($sql);
                $successMessages[] = "Tabela '{$table}' criada com sucesso.";
            } catch (Exception $e) {
                $allTablesCreated = false;
                $errorMessages[] = "Erro ao criar tabela '{$table}': " . $e->getMessage();
            }
        }

        // Criar índices se todas as tabelas foram criadas
        if ($allTablesCreated) {
            foreach ($createIndexesSQL as $indexSQL) {
                try {
                    $db->query($indexSQL);
                } catch (Exception $e) {
                    $errorMessages[] = "Aviso: Erro ao criar índice: " . $e->getMessage();
                }
            }

            // Inserir usuário administrador
            if (isset($_POST['create_admin']) && $_POST['create_admin'] == 1) {
                $adminUsername = 'admin';
                $adminEmail = 'admin@example.com';
                $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);

                $checkUserSQL = "SELECT id FROM users WHERE username = :username OR email = :email";
                $checkParams = [
                    ':username' => $adminUsername,
                    ':email' => $adminEmail
                ];

                $existingUser = $db->fetchOne($checkUserSQL, $checkParams);

                if (!$existingUser) {
                    $insertAdminSQL = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
                    $insertParams = [
                        ':username' => $adminUsername,
                        ':email' => $adminEmail,
                        ':password' => $adminPassword
                    ];

                    try {
                        $db->insert($insertAdminSQL, $insertParams);
                        $successMessages[] = "Usuário administrador criado com sucesso. (Login: admin / Senha: admin123)";
                    } catch (Exception $e) {
                        $errorMessages[] = "Erro ao criar usuário administrador: " . $e->getMessage();
                    }
                } else {
                    $successMessages[] = "Usuário administrador já existe.";
                }
            }
        }
    }
} catch (Exception $e) {
    $errorsFound = true;
    $errorMessages[] = "Erro ao conectar ao banco de dados: " . $e->getMessage();
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Instalação do Sistema de Gestão de Tarefas</h2>
        </div>
        <div class="card-body">
            <p>Esta página irá configurar o banco de dados e as tabelas necessárias para o sistema.</p>

            <h3>Verificação de Requisitos</h3>
            <ul>
                <li>PHP Version: <?php echo PHP_VERSION; ?>
                    <span class="badge <?php echo version_compare(PHP_VERSION, '7.4.0', '>=') ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo version_compare(PHP_VERSION, '7.4.0', '>=') ? 'OK' : 'Necessário PHP 7.4 ou superior'; ?>
                    </span>
                </li>
                <li>PDO Extension:
                    <span class="badge <?php echo extension_loaded('pdo') ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo extension_loaded('pdo') ? 'OK' : 'Não encontrada'; ?>
                    </span>
                </li>
                <li>MySQL PDO Extension:
                    <span class="badge <?php echo extension_loaded('pdo_mysql') ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo extension_loaded('pdo_mysql') ? 'OK' : 'Não encontrada'; ?>
                    </span>
                </li>
                <?php foreach ($writeableFolders as $folder => $isWriteable): ?>
                    <li>Pasta <?php echo $folder; ?>:
                        <span class="badge <?php echo $isWriteable ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo $isWriteable ? 'OK' : 'Sem permissão de escrita'; ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h3>Status da Instalação</h3>

            <?php
            // Exibir mensagens de sucesso
            foreach ($successMessages as $message) {
                displayMessage($message, 'success');
            }

            // Exibir mensagens de erro
            foreach ($errorMessages as $message) {
                displayMessage($message, 'error');
            }
            ?>

            <?php if (!$errorsFound): ?>
                <div class="form-container">
                    <form method="post" action="">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="create_tables" value="1" checked>
                                Criar tabelas no banco de dados
                            </label>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="create_admin" value="1" checked>
                                Criar usuário administrador padrão (admin/admin123)
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Instalar Sistema</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="message message-warning">
                    Por favor, corrija os erros acima antes de continuar com a instalação.
                </div>
            <?php endif; ?>

            <div class="alert">
                <strong>Importante:</strong> Após a instalação, remova este arquivo (install.php) do servidor por motivos de segurança.
            </div>
        </div>
    </div>
</div>

<style>
    .badge {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }

    .badge-success {
        color: #fff;
        background-color: #28a745;
    }

    .badge-danger {
        color: #fff;
        background-color: #dc3545;
    }

    .alert {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
    }

    .form-container {
        margin-top: 20px;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
</style>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>