<?php
// login.php
require_once 'config/config.php';

// Se já estiver logado, redirecionar para a página de tarefas
if (isLoggedIn()) {
    redirect('tasks.php');
}

// Definir título e descrição da página
$pageTitle = 'Login';
$pageDescription = 'Acesse sua conta para gerenciar suas tarefas';
$bodyClass = 'auth-page';

// Incluir cabeçalho
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/login.css">

<div class="auth-container">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Bem-vindo de volta!</h2>
                <p>Entre com suas credenciais para acessar sua conta</p>
            </div>

            <form id="login-form" method="post">
                <div class="form-group">
                    <label for="username">Usuário</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Digite seu nome de usuário" autofocus required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="password-label-wrapper">
                        <label for="password">Senha</label>
                        <a href="#" class="forgot-password" id="forgot-password">Esqueceu a senha?</a>
                    </div>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
                        <button type="button" class="toggle-password" aria-label="Mostrar senha">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group remember-me">
                    <label class="checkbox-container">
                        <input type="checkbox" id="remember" name="remember">
                        <span class="checkmark"></span>
                        Lembrar de mim
                    </label>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block" id="login-button">
                        <span class="btn-text">Entrar</span>
                        <span class="btn-loader">
                            <i class="fas fa-circle-notch fa-spin"></i>
                        </span>
                    </button>
                </div>

                <div id="login-message" class="message" role="alert" aria-live="assertive"></div>
            </form>

            <div class="auth-divider">
                <span>ou</span>
            </div>

            <div class="social-login">
                <button class="btn btn-social btn-google">
                    <i class="fab fa-google"></i>
                    <span>Entrar com Google</span>
                </button>
            </div>

            <div class="auth-links">
                <p>Não tem uma conta? <a href="register.php" class="highlight-link">Cadastre-se</a></p>
                <p><small>Problemas para fazer login? <a href="login-simple.php">Tente a versão simplificada</a></small></p>
            </div>
        </div>

        <div class="auth-info">
            <div class="auth-benefits">
                <h3>Por que usar nosso sistema?</h3>
                <ul class="benefits-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Organize suas tarefas de forma simples e eficiente</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Acesse de qualquer dispositivo, a qualquer momento</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Filtros avançados para encontrar o que precisa</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Interface moderna e intuitiva</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Esqueci a Senha -->
<div id="forgot-password-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Recuperar Senha</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <p>Digite seu e-mail cadastrado para receber instruções de recuperação.</p>
            <form id="recovery-form">
                <div class="form-group">
                    <label for="recovery-email">E-mail</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="recovery-email" name="email" required>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                    <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
                </div>
            </form>
            <div id="recovery-message" class="message"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('login-form');
        const messageDiv = document.getElementById('login-message');
        const loginButton = document.getElementById('login-button');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.querySelector('.toggle-password');
        const forgotPasswordLink = document.getElementById('forgot-password');
        const forgotPasswordModal = document.getElementById('forgot-password-modal');
        const recoveryForm = document.getElementById('recovery-form');
        const recoveryMessage = document.getElementById('recovery-message');

        // Verificar se há parâmetros na URL para mostrar mensagem
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('logout') && urlParams.get('logout') === 'success') {
            showMessage('Você foi desconectado com sucesso.', 'success');
        }
        if (urlParams.has('session_expired') && urlParams.get('session_expired') === 'true') {
            showMessage('Sua sessão expirou. Por favor, faça login novamente.', 'warning');
        }

        // Preencher automaticamente o username se tiver no localStorage
        if (localStorage.getItem('rememberedUser')) {
            const rememberedUser = JSON.parse(localStorage.getItem('rememberedUser'));
            usernameInput.value = rememberedUser.username;
            document.getElementById('remember').checked = true;
        }

        // Mostrar/esconder senha
        if (togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                const eyeIcon = this.querySelector('i');
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        }

        // Abrir modal de esqueci a senha
        if (forgotPasswordLink) {
            forgotPasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                forgotPasswordModal.classList.add('active');
            });
        }

        // Fechar modal
        document.querySelectorAll('.close-modal').forEach(element => {
            element.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal) modal.classList.remove('active');
            });
        });

        // Clicar fora do modal para fechar
        forgotPasswordModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });

        // Formulário de recuperação
        if (recoveryForm) {
            recoveryForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = document.getElementById('recovery-email').value;

                // Simulação - em um ambiente real, enviaria para o servidor
                showRecoveryMessage('Instruções enviadas para ' + email + '. Verifique sua caixa de entrada.', 'success');

                // Limpar campo e fechar modal após alguns segundos
                setTimeout(() => {
                    recoveryForm.reset();
                    forgotPasswordModal.classList.remove('active');
                }, 3000);
            });
        }

        // Submeter formulário de login
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const username = usernameInput.value.trim();
            const password = passwordInput.value;
            const remember = document.getElementById('remember').checked;

            // Validação básica
            if (!username || !password) {
                showMessage('Preencha todos os campos', 'error');
                return;
            }

            // Mostrar indicador de carregamento
            toggleLoading(true);

            // Dados para enviar
            const data = {
                username: username,
                password: password,
                action: 'login'
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
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 0) {
                            throw new Error('Erro de conexão com o servidor');
                        } else if (response.status === 404) {
                            throw new Error('API não encontrada. Verifique a configuração de API_URL');
                        } else if (response.status === 405) {
                            // Tentar novamente com método GET como fallback
                            console.warn('Método POST não permitido. Tentando com GET...');
                            const queryParams = new URLSearchParams({
                                action: 'login',
                                username: data.username,
                                password: data.password
                            });
                            return fetch(API_URL + 'auth.php?' + queryParams, {
                                method: 'GET',
                                credentials: 'include'
                            });
                        } else if (response.status === 400) {
                            // Tentar diferentes formatos se o 400 ocorrer
                            console.warn('Erro 400. Tentando com formato diferente...');

                            // Tentativa 1: Usar FormData
                            const formData = new FormData();
                            formData.append('username', data.username);
                            formData.append('password', data.password);
                            formData.append('action', 'login');

                            return fetch(API_URL + 'auth.php', {
                                method: 'POST',
                                body: formData,
                                credentials: 'include'
                            });
                        } else {
                            throw new Error('Erro de servidor: ' + response.status);
                        }
                    }
                    return response.json();
                })
                .then(data => {
                    toggleLoading(false);

                    if (data.success) {
                        // Salvar no localStorage se "lembrar de mim" estiver marcado
                        if (remember) {
                            localStorage.setItem('rememberedUser', JSON.stringify({
                                username: username
                            }));
                        } else {
                            localStorage.removeItem('rememberedUser');
                        }

                        showMessage('<i class="fas fa-check-circle"></i> Login realizado com sucesso! Redirecionando...', 'success');

                        // Adicionar efeito de sucesso ao formulário
                        loginForm.classList.add('form-success');

                        setTimeout(() => {
                            // Redirecionar para a página de tarefas
                            if (typeof cleanUrl === 'function') {
                                window.location.href = cleanUrl(BASE_URL + 'tasks.php');
                            } else {
                                window.location.href = BASE_URL + 'tasks.php';
                            }
                        }, 1000);
                    } else {
                        loginForm.classList.add('shake');
                        setTimeout(() => loginForm.classList.remove('shake'), 500);

                        showMessage('<i class="fas fa-exclamation-circle"></i> ' + (data.error || 'Credenciais inválidas. Tente novamente.'), 'error');

                        // Focar no campo com erro
                        passwordInput.focus();
                        passwordInput.select();
                    }
                })
                .catch(error => {
                    toggleLoading(false);
                    console.error('Erro:', error);

                    loginForm.classList.add('shake');
                    setTimeout(() => loginForm.classList.remove('shake'), 500);

                    // Verificar se o erro é um problema de CORS ou conexão
                    if (error.message.includes('Failed to fetch') ||
                        error.message.includes('NetworkError') ||
                        error.message.includes('conexão')) {
                        showMessage('<i class="fas fa-wifi"></i> Erro de conexão com o servidor. Verifique sua internet ou tente novamente mais tarde.', 'error');

                        // Adicionar botão de diagnóstico
                        messageDiv.innerHTML += '<div class="debug-actions"><button id="debug-btn" class="btn btn-sm btn-secondary"><i class="fas fa-bug"></i> Diagnosticar Problema</button> <button id="try-simple" class="btn btn-sm btn-secondary"><i class="fas fa-power-off"></i> Versão Básica</button></div>';

                        document.getElementById('debug-btn').addEventListener('click', function() {
                            window.open(API_URL + 'debug.php', '_blank');
                        });

                        document.getElementById('try-simple').addEventListener('click', function() {
                            window.location.href = BASE_URL + 'login-simple.php';
                        });
                    } else {
                        showMessage('<i class="fas fa-exclamation-triangle"></i> ' + (error.message || 'Erro ao processar sua solicitação'), 'error');
                    }
                });
        });

        // Funções auxiliares
        function toggleLoading(isLoading) {
            loginButton.disabled = isLoading;
            loginButton.classList.toggle('loading', isLoading);
        }

        function showMessage(message, type) {
            messageDiv.innerHTML = message;
            messageDiv.className = 'message message-' + type;
            messageDiv.style.display = 'block';

            // Scroll para a mensagem se necessário
            if (messageDiv.getBoundingClientRect().top < 0 ||
                messageDiv.getBoundingClientRect().bottom > window.innerHeight) {
                messageDiv.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            // Não limpe mensagens de erro automaticamente
            if (type !== 'error') {
                setTimeout(() => {
                    messageDiv.style.opacity = '0';
                    setTimeout(() => {
                        messageDiv.innerHTML = '';
                        messageDiv.className = 'message';
                        messageDiv.style.display = 'none';
                        messageDiv.style.opacity = '1';
                    }, 300);
                }, 5000);
            }
        }

        function showRecoveryMessage(message, type) {
            recoveryMessage.textContent = message;
            recoveryMessage.className = 'message message-' + type;
            recoveryMessage.style.display = 'block';

            if (type !== 'error') {
                setTimeout(() => {
                    recoveryMessage.style.opacity = '0';
                    setTimeout(() => {
                        recoveryMessage.textContent = '';
                        recoveryMessage.className = 'message';
                        recoveryMessage.style.display = 'none';
                        recoveryMessage.style.opacity = '1';
                    }, 300);
                }, 5000);
            }
        }
    });
</script>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>