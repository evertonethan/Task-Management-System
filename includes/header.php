<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo APP_NAME; ?></title>

    <!-- Estilos CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">

    <?php if (isset($extraStyles) && !empty($extraStyles)): ?>
        <?php foreach ($extraStyles as $style): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL . 'css/' . $style; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>
    <header class="navbar">
        <div class="container navbar-container">
            <a href="<?php echo BASE_URL; ?>" class="navbar-brand">
                <i class="fas fa-tasks"></i> <?php echo APP_NAME; ?>
            </a>

            <nav class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo BASE_URL; ?>tasks.php" class="nav-link">Minhas Tarefas</a>
                    <a href="<?php echo BASE_URL; ?>profile.php" class="nav-link">Perfil</a>
                    <a href="<?php echo BASE_URL; ?>logout.php" class="nav-link">Sair</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>login.php" class="nav-link">Login</a>
                    <a href="<?php echo BASE_URL; ?>register.php" class="nav-link">Cadastro</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">