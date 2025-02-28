<?php
// profile.php
require_once 'config/config.php';

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    redirect('login.php');
}

// Carregar classes necessárias
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Task.php';

// Instanciar classes
$user = new User();
$task = new Task();

// Obter dados do usuário
$userData = $user->findById($_SESSION['user_id']);

// Obter estatísticas de tarefas
$taskStats = $task->countByStatus($_SESSION['user_id']);

// Definir título da página
$pageTitle = 'Meu Perfil';

// Incluir cabeçalho
require_once 'includes/header.php';
?>

<div class="profile-container">
    <h1 class="page-title">Meu Perfil</h1>

    <div class="profile-grid">
        <!-- Informações do Usuário -->
        <div class="profile-card user-info">
            <div class="card-header">
                <h2><i class="fas fa-user"></i> Informações Pessoais</h2>
                <button id="edit-profile-btn" class="btn-icon">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
            <div class="card-body">
                <form id="profile-form" style="display: none;">
                    <div class="form-group">
                        <label for="username">Nome de Usuário</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        <button type="button" id="cancel-edit-profile" class="btn btn-secondary">Cancelar</button>
                    </div>
                </form>

                <div id="profile-info">
                    <div class="info-group">
                        <label>Nome de Usuário:</label>
                        <p><?php echo htmlspecialchars($userData['username']); ?></p>
                    </div>
                    <div class="info-group">
                        <label>E-mail:</label>
                        <p><?php echo htmlspecialchars($userData['email']); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Membro desde:</label>
                        <p><?php echo date('d/m/Y', strtotime($userData['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alterar Senha -->
        <div class="profile-card password-change">
            <div class="card-header">
                <h2><i class="fas fa-key"></i> Alterar Senha</h2>
            </div>
            <div class="card-body">
                <form id="password-form">
                    <div class="form-group">
                        <label for="current-password">Senha Atual</label>
                        <input type="password" id="current-password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="new-password">Nova Senha</label>
                        <input type="password" id="new-password" name="new_password" class="form-control" required>
                        <small>Mínimo de 6 caracteres</small>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirmar Nova Senha</label>
                        <input type="password" id="confirm-password" name="confirm_password" class="form-control" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Alterar Senha</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="profile-card statistics">
            <div class="card-header">
                <h2><i class="fas fa-chart-bar"></i> Estatísticas</h2>
            </div>
            <div class="card-body">
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $taskStats['total']; ?></div>
                        <div class="stat-label">Total de Tarefas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $taskStats['pendente']; ?></div>
                        <div class="stat-label">Pendentes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $taskStats['em_andamento']; ?></div>
                        <div class="stat-label">Em Andamento</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $taskStats['concluido']; ?></div>
                        <div class="stat-label">Concluídas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">
                            <?php
                            echo ($taskStats['total'] > 0)
                                ? round(($taskStats['concluido'] / $taskStats['total']) * 100)
                                : 0;
                            ?>%
                        </div>
                        <div class="stat-label">Taxa de Conclusão</div>
                    </div>
                </div>

                <div class="progress-container">
                    <div class="progress-label">Progresso Geral</div>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo ($taskStats['total'] > 0) ? ($taskStats['concluido'] / $taskStats['total']) * 100 : 0; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Editar informações do perfil
        const editProfileBtn = document.getElementById('edit-profile-btn');
        const cancelEditProfileBtn = document.getElementById('cancel-edit-profile');
        const profileForm = document.getElementById('profile-form');
        const profileInfo = document.getElementById('profile-info');

        editProfileBtn.addEventListener('click', function() {
            profileInfo.style.display = 'none';
            profileForm.style.display = 'block';
        });

        cancelEditProfileBtn.addEventListener('click', function() {
            profileInfo.style.display = 'block';
            profileForm.style.display = 'none';
        });

        // Enviar formulário de perfil
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();

            // Validação simples
            if (!username || !email) {
                showMessage('Todos os campos são obrigatórios.', 'error');
                return;
            }

            // Enviar para a API
            fetch(API_URL + 'profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'update_profile',
                        username: username,
                        email: email
                    }),
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('Perfil atualizado com sucesso!', 'success');
                        // Atualizar exibição
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showMessage(data.error || 'Erro ao atualizar perfil.', 'error');
                    }
                })
                .catch(error => {
                    showMessage('Erro ao conectar com o servidor.', 'error');
                    console.error('Erro:', error);
                });
        });

        // Enviar formulário de alteração de senha
        const passwordForm = document.getElementById('password-form');

        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const currentPassword = document.getElementById('current-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            // Validação
            if (!currentPassword || !newPassword || !confirmPassword) {
                showMessage('Todos os campos são obrigatórios.', 'error');
                return;
            }

            if (newPassword.length < 6) {
                showMessage('A nova senha deve ter pelo menos 6 caracteres.', 'error');
                return;
            }

            if (newPassword !== confirmPassword) {
                showMessage('As senhas não coincidem.', 'error');
                return;
            }

            // Enviar para a API
            fetch(API_URL + 'profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'change_password',
                        current_password: currentPassword,
                        new_password: newPassword
                    }),
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('Senha alterada com sucesso!', 'success');
                        passwordForm.reset();
                    } else {
                        showMessage(data.error || 'Erro ao alterar senha.', 'error');
                    }
                })
                .catch(error => {
                    showMessage('Erro ao conectar com o servidor.', 'error');
                    console.error('Erro:', error);
                });
        });

        // Função para exibir mensagens
        function showMessage(message, type) {
            // Verificar se já existe um elemento de mensagem
            let messageElement = document.getElementById('message-container');

            // Se não existir, criar um
            if (!messageElement) {
                messageElement = document.createElement('div');
                messageElement.id = 'message-container';
                document.body.appendChild(messageElement);
            }

            // Criar elemento da mensagem
            const alert = document.createElement('div');
            alert.className = `message message-${type}`;
            alert.textContent = message;

            // Adicionar ao container
            messageElement.appendChild(alert);

            // Remover após 5 segundos
            setTimeout(() => {
                alert.classList.add('fade-out');
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        }
    });
</script>

<style>
    .profile-container {
        padding: 2rem 0;
    }

    .page-title {
        margin-bottom: 2rem;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .profile-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h2 {
        margin: 0;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .info-group {
        margin-bottom: 1rem;
    }

    .info-group label {
        font-weight: bold;
        margin-bottom: 0.25rem;
        color: var(--grey-color);
    }

    .info-group p {
        margin: 0;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-item {
        text-align: center;
        padding: 1rem;
        background-color: var(--light-color);
        border-radius: 8px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
    }

    .stat-label {
        font-size: 0.9rem;
        color: var(--grey-color);
    }

    .progress-container {
        margin-top: 1.5rem;
    }

    .progress-label {
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .progress {
        height: 10px;
        background-color: var(--grey-light);
        border-radius: 5px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background-color: var(--primary-color);
        transition: width 0.3s ease;
    }

    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>