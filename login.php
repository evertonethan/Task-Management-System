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

<link rel="stylesheet" href="css/login.css">

<div class="auth-container">
    <div class="auth-card">
        <h2>Login</h2>

        <form id="login-form" method="post">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </div>

            <div id="login-message"></div>
        </form>

        <div class="auth-links">
            <p>Não tem uma conta? <a href="register.php">Cadastre-se</a></p>
            <p><small><a href="login-simple.php">Versão alternativa de login</a></small></p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('login-form');
        const messageDiv = document.getElementById('login-message');

        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            // Validações básicas
            if (!username || !password) {
                showMessage('Preencha todos os campos.', 'error');
                return;
            }

            // Enviar dados para a API
            fetch(API_URL + 'auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'login',
                        username,
                        password
                    }),
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('Login realizado com sucesso! Redirecionando...', 'success');
                        setTimeout(() => {
                            window.location.href = BASE_URL + 'tasks.php';
                        }, 1500);
                    } else {
                        showMessage(data.error || 'Nome de usuário ou senha incorretos.', 'error');
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

<style>
    .auth-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem 0;
    }

    .auth-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        width: 100%;
        max-width: 400px;
    }

    .auth-card h2 {
        text-align: center;
        margin-bottom: 1.5rem;
        color: var(--primary-color);
    }

    .auth-links {
        margin-top: 1.5rem;
        text-align: center;
    }
</style>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>