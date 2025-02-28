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
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #2980b9;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .links {
            text-align: center;
            margin-top: 15px;
        }

        .links a {
            color: #3498db;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Login Simplificado</h1>
        <p><small>Esta é uma versão alternativa do login em caso de problemas com o JavaScript.</small></p>

        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <button type="submit">Entrar</button>
            </div>
        </form>

        <div class="links">
            <p>Não tem uma conta? <a href="register.php">Cadastre-se</a></p>
            <p><a href="login.php">Voltar para o login normal</a></p>
        </div>
    </div>
</body>

</html>