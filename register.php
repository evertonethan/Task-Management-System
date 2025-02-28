<?php
// register.php
require_once 'config/config.php';

// Se já estiver logado, redirecionar para a página de tarefas
if (isLoggedIn()) {
    redirect('tasks.php');
}

// Definir título da página
$pageTitle = 'Cadastro';

// Incluir cabeçalho
require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Cadastro</h2>

        <form id="register-form" method="post">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" required>
                <small>Mínimo de 3 caracteres</small>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required>
                <small>Mínimo de 6 caracteres</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Senha</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
            </div>

            <div id="register-message"></div>
        </form>

        <div class="auth-links">
            <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const registerForm = document.getElementById('register-form');
        const messageDiv = document.getElementById('register-message');

        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('confirm_password').value.trim();

            // Validações
            if (!username || !email || !password || !confirmPassword) {
                showMessage('Todos os campos são obrigatórios.', 'error');
                return;
            }

            if (username.length < 3) {
                showMessage('O nome de usuário deve ter pelo menos 3 caracteres.', 'error');
                return;
            }

            if (password.length < 6) {
                showMessage('A senha deve ter pelo menos 6 caracteres.', 'error');
                return;
            }

            if (password !== confirmPassword) {
                showMessage('As senhas não coincidem.', 'error');
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showMessage('Digite um e-mail válido.', 'error');
                return;
            }

            // Enviar dados para a API
            fetch(API_URL + 'auth.php?action=register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username,
                        email,
                        password
                    }),
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('Cadastro realizado com sucesso! Redirecionando...', 'success');
                        setTimeout(() => {
                            window.location.href = BASE_URL + 'tasks.php';
                        }, 1500);
                    } else {
                        showMessage(data.error || 'Erro ao realizar cadastro.', 'error');
                    }
                })
                .catch(error => {
                    showMessage('Erro ao conectar com o servidor.', 'error');
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