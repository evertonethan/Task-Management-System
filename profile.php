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

// Calcular a taxa de conclusão
$completionRate = ($taskStats['total'] > 0) ? ($taskStats['concluido'] / $taskStats['total']) * 100 : 0;
$completionRateFormatted = round($completionRate);

// Definir título da página
$pageTitle = 'Meu Perfil';

// Incluir cabeçalho (verifica se o arquivo existe)
$header_file = 'includes/header.php';
if (file_exists($header_file)) {
    require_once $header_file;
} else {
    // Cabeçalho básico caso o arquivo não exista
    echo '<!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . $pageTitle . ' - ' . APP_NAME . '</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            :root {
                --primary-color: #007bff;
                --primary-color-dark: #0056b3;
                --primary-color-rgb: 0, 123, 255;
                --secondary-color: #6c757d;
                --success-color: #28a745;
                --danger-color: #dc3545;
                --warning-color: #ffc107;
                --info-color: #17a2b8;
                --light-color: #f8f9fa;
                --dark-color: #343a40;
            }
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f5f5f5;
                color: #333;
            }
            .container {
                width: 100%;
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 15px;
            }
            .navbar {
                background-color: var(--primary-color);
                color: white;
                padding: 1rem 0;
            }
            .navbar a {
                color: white;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
    <nav class="navbar">
        <div class="container">
            <a href="' . BASE_URL . '">' . APP_NAME . '</a>
            <div>
                <a href="logout.php">Sair</a>
            </div>
        </div>
    </nav>';
}
?>

<div class="profile-container">
    <h1 class="page-title">Meu Perfil</h1>

    <div class="profile-grid">
        <!-- Informações do Usuário -->
        <div class="profile-card user-info">
            <div class="card-header">
                <h2><i class="fas fa-user"></i> Informações Pessoais</h2>
                <button id="edit-profile-btn" class="btn-icon" title="Editar perfil">
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
                        <div class="stat-value"><?php echo $completionRateFormatted; ?>%</div>
                        <div class="stat-label">Taxa de Conclusão</div>
                    </div>
                </div>

                <div class="progress-container">
                    <div class="progress-label" data-value="<?php echo $completionRateFormatted; ?>%">Progresso Geral</div>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $completionRate; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Definir as constantes com base nos valores do PHP
        const API_URL = '<?php echo API_URL; ?>';
        const BASE_URL = '<?php echo BASE_URL; ?>';

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
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'update_profile',
                        username: username,
                        email: email
                    }),
                    credentials: 'include'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Resposta da rede não foi ok: ' + response.status);
                    }
                    return response.json();
                })
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
                    console.error('Erro detalhado:', error);

                    // Verificar se a API existe
                    fetch(API_URL + 'profile.php', {
                            method: 'HEAD'
                        })
                        .then(response => {
                            if (!response.ok) {
                                showMessage('Erro: API de perfil não encontrada. Verifique se o arquivo ' + API_URL + 'profile.php existe.', 'error');
                            } else {
                                showMessage('Erro ao conectar com o servidor. Verifique se a API está respondendo corretamente.', 'error');
                            }
                        })
                        .catch(() => {
                            showMessage('Erro ao conectar com a API. Verifique se o arquivo existe em: ' + API_URL + 'profile.php', 'error');
                        });
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
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'change_password',
                        current_password: currentPassword,
                        new_password: newPassword
                    }),
                    credentials: 'include'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Resposta da rede não foi ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMessage('Senha alterada com sucesso!', 'success');
                        passwordForm.reset();
                    } else {
                        showMessage(data.error || 'Erro ao alterar senha.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro detalhado:', error);
                    showMessage('Erro ao conectar com o servidor.', 'error');
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

<link rel="stylesheet" href="css/profile.css">

<?php
// Incluir rodapé (verifica se o arquivo existe)
$footer_file = 'includes/footer.php';
if (file_exists($footer_file)) {
    require_once $footer_file;
} else {
    // Rodapé básico caso o arquivo não exista
    echo '</body></html>';
}
?>