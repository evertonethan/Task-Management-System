<?php
// login-simple.php - Versão simplificada do login que envia diretamente para o PHP
require_once 'config/config.php';

// Se já estiver logado, redirecionar para a página de tarefas
if (isLoggedIn()) {
    redirect('tasks.php');
}

// Processar formulário
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Incluir classes necessárias
        require_once 'classes/Database.php';
        require_once 'classes/User.php';

        // Fazer login diretamente
        $user = new User();
        try {
            $result = $user->login($username, $password);

            if ($result) {
                // Login bem-sucedido
                $_SESSION['user_id'] = $result['id'];
                $_SESSION['username'] = $result['username'];

                $success_message = 'Login realizado com sucesso! Redirecionando...';

                // Redirecionamento com JavaScript após 2 segundos
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "' . BASE_URL . 'tasks.php";
                    }, 2000);
                </script>';
            } else {
                $error_message = 'Usuário ou senha incorretos.';
            }
        } catch (Exception $e) {
            $error_message = 'Erro ao processar login: ' . $e->getMessage();
        }
    } else {
        $error_message = 'Por favor, preencha todos os campos.';
    }
}

// Definir título da página
$pageTitle = 'Login Simplificado';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo APP_NAME; ?></title>
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --primary-dark: #2980b9;
            --secondary-color: #2ecc71;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
            --bg-color: #f5f7fa;
            --card-bg: #ffffff;
            --text-color: #333333;
            --muted-color: #7f8c8d;
            --border-radius: 8px;
            --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            background-color: var(--bg-color);
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--text-color);
        }

        .container {
            width: 100%;
            max-width: 450px;
            background: var(--card-bg);
            padding: 35px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo i {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        h1 {
            font-size: 26px;
            font-weight: 600;
            color: var(--text-color);
            text-align: center;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: var(--muted-color);
            font-size: 14px;
            margin-bottom: 25px;
        }

        .message {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
        }

        .message i {
            margin-right: 10px;
            font-size: 18px;
        }

        .error {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--error-color);
            border-left: 4px solid var(--error-color);
        }

        .success {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 15px;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        button i {
            margin-right: 8px;
        }

        .links {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .links p {
            margin: 8px 0;
            font-size: 14px;
            color: var(--muted-color);
        }

        .links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
        }

        .links a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .help-text {
            display: block;
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 10px;
        }

        .checkbox-container label {
            margin-bottom: 0;
            font-size: 14px;
            color: var(--muted-color);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive styles */
        @media (max-width: 480px) {
            .container {
                padding: 25px 20px;
            }

            h1 {
                font-size: 22px;
            }

            input[type="text"],
            input[type="password"],
            button {
                padding: 10px;
                font-size: 14px;
            }

            .input-icon {
                left: 12px;
            }

            input[type="text"],
            input[type="password"] {
                padding-left: 35px;
            }
        }

        /* Accessibility improvements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.001s !important;
                transition-duration: 0.001s !important;
            }
        }

        input:focus-visible,
        button:focus-visible,
        a:focus-visible {
            outline: 3px solid var(--primary-color);
            outline-offset: 2px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1><?php echo APP_NAME; ?></h1>
        <p class="subtitle">Versão simplificada do login para acesso sem JavaScript</p>

        <?php if (!empty($error_message)): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="username">Nome de Usuário</label>
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="username" name="username" placeholder="Digite seu nome de usuário"
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
                </div>
                <span class="help-text">* Mínimo de 6 caracteres</span>
            </div>

            <div class="checkbox-container">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Lembrar meus dados</label>
            </div>

            <div class="form-group">
                <button type="submit">
                    <i class="fas fa-sign-in-alt"></i>
                    Entrar
                </button>
            </div>
        </form>

        <div class="links">
            <p>Não tem uma conta? <a href="register.php">Cadastre-se</a></p>
            <p>Problemas com o login? <a href="recover-password.php">Recuperar senha</a></p>
            <p><a href="login.php"><i class="fas fa-arrow-left"></i> Voltar para o login normal</a></p>
        </div>
    </div>
</body>

</html>