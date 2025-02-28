<?php
// login.php
require_once 'config/config.php';

// Se já estiver logado, redirecionar para a página de tarefas
if (isLoggedIn()) {
    redirect('tasks.php');
}

// Definir título da página
$pageTitle = 'Login';

// Incluir cabeçalho
require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Login</h2>

        <form id="login-form" method="post">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </div>

            <div id="login-message"></div>
        </form>

        <div class="auth-links">
            <p>Não tem uma conta? <a href="register.php">Cadastre-se</a></p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('login-form');
        const messageDiv = document.getElementById('login-message');

        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // Validação básica
            if (!username || !password) {
                showMessage('Preencha todos os campos', 'error');
                return;
            }

            // Dados para enviar
            const data = {
                username: username,
                password: password
            };

            // Enviar requisição
            fetch(API_URL + 'auth.php?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data),
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('Login realizado com sucesso! Redirecionando...', 'success');
                        setTimeout(() => {
                            window.location.href = BASE_URL + 'tasks.php';
                        }, 1000);
                    } else {
                        showMessage(data.error || 'Erro ao fazer login', 'error');
                    }
                })
                .catch(error => {
                    showMessage('Erro ao conectar com o servidor', 'error');
                    console.error('Erro:', error);
                });
        });

        function showMessage(message, type) {
            messageDiv.textContent = message;
            messageDiv.className = 'message message-' + type;

            setTimeout(() => {
                messageDiv.textContent = '';
                messageDiv.className = '';
            }, 5000);
        }
    });
</script>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>