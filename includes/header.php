<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo BASE_URL; ?>">
                        <h1><?php echo APP_NAME; ?></h1>
                    </a>
                </div>

                <?php if (isLoggedIn()): ?>
                    <nav>
                        <ul>
                            <li><a href="<?php echo BASE_URL; ?>tasks.php"><i class="fas fa-tasks"></i> Minhas Tarefas</a></li>
                            <li class="dropdown">
                                <a href="#"><i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?> <i class="fas fa-caret-down"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo BASE_URL; ?>profile.php"><i class="fas fa-user-cog"></i> Perfil</a></li>
                                    <li><a href="#" id="logout-btn"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash_type']; ?>">
                    <?php
                    echo $_SESSION['flash_message'];
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                </div>
            <?php endif; ?>