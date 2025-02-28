<?php
// register.php
require_once 'config/config.php';

// Se já estiver logado, redirecionar para a página de tarefas
if (isLoggedIn()) {
    redirect('tasks.php');
}

// Definir título da página
$pageTitle = 'Cadastro';

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
        <style>
            :root {
                --primary-color: #007bff;
                --primary-color-dark: #0056b3;
                --primary-color-rgb: 0, 123, 255;
            }
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f5f5f5;
            }
        </style>
    </head>
    <body>';
}
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Cadastro</h2>

        <form id="register-form" method="post">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" class="form-control" required>
                <small>Mínimo de 3 caracteres</small>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <small>Mínimo de 6 caracteres</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Senha</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
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
        // Debugging: Exibir valores das constantes
        console.log('Base URL:', '<?php echo BASE_URL; ?>');
        console.log('API URL:', '<?php echo API_URL; ?>');

        const BASE_URL = '<?php echo BASE_URL; ?>';
        const API_URL = '<?php echo API_URL; ?>';

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

            // Exibir mensagem de processamento
            showMessage('Processando solicitação...', 'info');

            // Enviar dados para a API
            fetch(API_URL + 'auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'register',
                        username: username,
                        email: email,
                        password: password
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
                        showMessage('Cadastro realizado com sucesso! Redirecionando...', 'success');
                        setTimeout(() => {
                            window.location.href = BASE_URL + 'login.php';
                        }, 1500);
                    } else {
                        showMessage(data.error || 'Erro ao realizar cadastro.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro detalhado:', error);

                    // Verificar se a API existe
                    showMessage('Erro ao conectar com o servidor. Verificando API...', 'error');

                    // Verificação mais detalhada da existência da API
                    const apiCheckUrl = API_URL + 'auth.php';
                    console.log('Tentando verificar API em:', apiCheckUrl);

                    fetch(apiCheckUrl, {
                            method: 'HEAD',
                            cache: 'no-cache'
                        })
                        .then(response => {
                            if (!response.ok) {
                                showMessage('Erro: API não encontrada (Status ' + response.status + '). Verifique se o arquivo ' + apiCheckUrl + ' existe.', 'error');
                            } else {
                                showMessage('API encontrada, mas ocorreu um erro na comunicação. Verifique o console para mais detalhes.', 'error');
                            }
                        })
                        .catch(checkError => {
                            console.error('Erro ao verificar API:', checkError);
                            showMessage('Erro ao conectar com a API. Verifique se o arquivo existe em: ' + apiCheckUrl, 'error');
                        });
                });
        });

        function showMessage(message, type) {
            messageDiv.textContent = message;
            messageDiv.className = 'message message-' + type;

            // Não limpar mensagens de erro automaticamente
            if (type !== 'error') {
                setTimeout(() => {
                    messageDiv.textContent = '';
                    messageDiv.className = '';
                }, 5000);
            }
        }
    });
</script>

<style>
    .auth-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
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

    /* Estilos para mensagens */
    .message {
        padding: 0.75rem;
        margin: 1rem 0;
        border-radius: 4px;
        text-align: center;
    }

    .message-error {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ef9a9a;
    }

    .message-success {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
    }

    .message-info {
        background-color: #e3f2fd;
        color: #1565c0;
        border: 1px solid #90caf9;
    }

    /* Melhorias no formulário */
    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 2px rgba(var(--primary-color-rgb), 0.2);
    }

    .btn {
        cursor: pointer;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background: var(--primary-color-dark, #0056b3);
    }

    .btn-block {
        display: block;
        width: 100%;
    }

    small {
        display: block;
        margin-top: 0.25rem;
        color: #6c757d;
    }
</style>

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