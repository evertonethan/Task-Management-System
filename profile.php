<?php
// profile.php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

// Verificar se o usuário está autenticado
checkAuth();

// Instanciar classe de usuário
$userClass = new User();

// Obter ID do usuário da sessão
$userId = $_SESSION['user_id'];

// Variáveis para mensagens de feedback
$successMsg = '';
$errorMsg = '';

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar qual formulário foi enviado
    if (isset($_POST['update_profile'])) {
        // Atualização de perfil
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        // Validar dados
        if (empty($username) || empty($email)) {
            $errorMsg = 'Todos os campos são obrigatórios';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg = 'E-mail inválido';
        } else {
            // Preparar dados para atualização
            $data = [
                'username' => $username,
                'email' => $email
            ];

            try {
                // Atualizar perfil
                $result = $userClass->update($userId, $data);

                if ($result) {
                    $successMsg = 'Perfil atualizado com sucesso';
                    // Atualizar username na sessão
                    $_SESSION['username'] = $username;
                } else {
                    $errorMsg = 'Nenhuma alteração realizada ou erro ao atualizar perfil';
                }
            } catch (Exception $e) {
                $errorMsg = $e->getMessage();
            }
        }
    } elseif (isset($_POST['change_password'])) {
        // Alteração de senha
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        // Validar dados
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $errorMsg = 'Todos os campos são obrigatórios';
        } elseif ($newPassword !== $confirmPassword) {
            $errorMsg = 'As senhas não coincidem';
        } elseif (strlen($newPassword) < 6) {
            $errorMsg = 'A nova senha deve ter pelo menos 6 caracteres';
        } else {
            // Verificar senha atual
            $user = $userClass->login($_SESSION['username'], $currentPassword);

            if ($user) {
                try {
                    // Atualizar senha
                    $result = $userClass->updatePassword($userId, $newPassword);

                    if ($result) {
                        $successMsg = 'Senha alterada com sucesso';
                    } else {
                        $errorMsg = 'Erro ao alterar senha';
                    }
                } catch (Exception $e) {
                    $errorMsg = $e->getMessage();
                }
            } else {
                $errorMsg = 'Senha atual incorreta';
            }
        }
    }
}

// Obter dados do usuário
$user = $userClass->findById($userId);

// Verificar se o usuário existe
if (!$user) {
    redirect('logout.php');
}

// Definir título da página
$pageTitle = 'Meu Perfil';

// Incluir cabeçalho
require_once 'includes/header.php';
?>

<div class="profile-container">
    <div class="card">
        <div class="card-header">
            <h2>Meu Perfil</h2>
        </div>

        <div class="card-body">
            <?php if (!empty($successMsg)): ?>
                <div class="alert alert-success"><?php echo $successMsg; ?></div>
            <?php endif; ?>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
            <?php endif; ?>

            <div class="profile-section">
                <h3>Informações da Conta</h3>

                <form method="post" action="" id="profile-form">
                    <input type="hidden" name="update_profile" value="1">

                    <div class="form-group">
                        <label for="username">Nome de Usuário</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Data de Cadastro</label>
                        <div class="form-static"><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>

            <div class="profile-section">
                <h3>Alterar Senha</h3>

                <form method="post" action="" id="password-form">
                    <input type="hidden" name="change_password" value="1">

                    <div class="form-group">
                        <label for="current_password">Senha Atual</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">Nova Senha</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <small>Mínimo de 6 caracteres</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmar Nova Senha</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Alterar Senha</button>
                    </div>
                </form>
            </div>

            <div class="profile-section">
                <h3>Estatísticas</h3>

                <div class="stats-container">
                    <?php
                    // Criar uma instância da classe Task
                    require_once 'classes/Task.php';
                    $taskClass = new Task();

                    // Obter contagens
                    $counts = $taskClass->countByStatus($userId);
                    ?>

                    <div class="stat-item">
                        <div class="stat-value"><?php echo $counts['total']; ?></div>
                        <div class="stat-label">Total de Tarefas</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-value"><?php echo $counts['pendente']; ?></div>
                        <div class="stat-label">Pendentes</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-value"><?php echo $counts['em_andamento']; ?></div>
                        <div class="stat-label">Em Andamento</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-value"><?php echo $counts['concluido']; ?></div>
                        <div class="stat-label">Concluídas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileForm = document.getElementById('profile-form');
        const passwordForm = document.getElementById('password-form');

        // Validação do formulário de perfil
        profileForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;

            if (!username || !email) {
                e.preventDefault();
                alert('Todos os campos são obrigatórios');
            }
        });

        // Validação do formulário de senha
        passwordForm.addEventListener('submit', function(e) {
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (!currentPassword || !newPassword || !confirmPassword) {
                e.preventDefault();
                alert('Todos os campos são obrigatórios');
                return;
            }

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('As senhas não coincidem');
                return;
            }

            if (newPassword.length < 6) {
                e.preventDefault();
                alert('A nova senha deve ter pelo menos 6 caracteres');
                return;
            }
        });
    });
</script>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>