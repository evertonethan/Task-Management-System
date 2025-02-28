<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestão de Tarefas - Organize seu dia de forma eficiente e produtiva">
    <meta name="theme-color" content="#3498db">

    <!-- Título dinâmico da página -->
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>assets/img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>assets/img/apple-touch-icon.png">

    <!-- Estilos CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/header.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/footer.css">

    <!-- Fonte personalizada -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Estilos específicos da página, se existirem -->
    <?php if (isset($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL . $style; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body class="<?php echo isset($bodyClass) ? $bodyClass : ''; ?>">
    <!-- Preloader -->
    <div id="preloader">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>

    <!-- Skip to main content - Acessibilidade -->
    <a href="#main-content" class="skip-to-content">Pular para o conteúdo</a>

    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo BASE_URL; ?>" aria-label="Ir para a página inicial">
                        <!-- Logo em SVG ou imagem + texto -->
                        <div class="logo-img">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h1><?php echo APP_NAME; ?></h1>
                    </a>
                </div>

                <!-- Botão do menu mobile (será adicionado via JavaScript) -->
                <!-- <div class="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div> -->

                <?php if (isLoggedIn()): ?>
                    <nav class="main-nav" aria-label="Navegação principal">
                        <ul class="nav-list">
                            <li class="nav-item">
                                <a href="<?php echo BASE_URL; ?>tasks.php"
                                    <?php echo (strpos($_SERVER['REQUEST_URI'], 'tasks.php') !== false) ? 'class="active"' : ''; ?>>
                                    <i class="fas fa-tasks"></i>
                                    <span>Minhas Tarefas</span>
                                </a>
                            </li>
                            <li class="nav-item user-dropdown">
                                <button class="dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                                    <div class="user-avatar">
                                        <?php if (isset($_SESSION['user_avatar']) && !empty($_SESSION['user_avatar'])): ?>
                                            <img src="<?php echo BASE_URL; ?>uploads/avatars/<?php echo $_SESSION['user_avatar']; ?>" alt="Avatar do usuário">
                                        <?php else: ?>
                                            <span class="avatar-initials"><?php echo substr($_SESSION['username'], 0, 1); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="username"><?php echo $_SESSION['username']; ?></span>
                                    <i class="fas fa-caret-down" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu" aria-label="Submenu de usuário">
                                    <li class="dropdown-item">
                                        <a href="<?php echo BASE_URL; ?>profile.php">
                                            <i class="fas fa-user-cog"></i> Perfil
                                        </a>
                                    </li>
                                    <li class="dropdown-item">
                                        <a href="<?php echo BASE_URL; ?>logout.php" id="logout-btn">
                                            <i class="fas fa-sign-out-alt"></i> Sair
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                <?php else: ?>
                    <nav class="main-nav" aria-label="Navegação principal">
                        <ul class="auth-buttons">
                            <li class="nav-item">
                                <a href="<?php echo BASE_URL; ?>login.php" class="btn btn-outline">
                                    <i class="fas fa-sign-in-alt"></i> Entrar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo BASE_URL; ?>register.php" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Cadastrar
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main id="main-content">
        <div class="container">
            <?php if (isset($showBreadcrumbs) && $showBreadcrumbs): ?>
                <!-- Breadcrumbs para melhor navegação -->
                <nav aria-label="Breadcrumb" class="breadcrumbs">
                    <ol>
                        <li><a href="<?php echo BASE_URL; ?>">Início</a></li>
                        <?php if (isset($breadcrumbs)): ?>
                            <?php foreach ($breadcrumbs as $label => $url): ?>
                                <li><a href="<?php echo $url; ?>"><?php echo $label; ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li aria-current="page"><?php echo $pageTitle; ?></li>
                    </ol>
                </nav>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash_type']; ?>" role="alert">
                    <div class="alert-icon">
                        <?php if ($_SESSION['flash_type'] == 'success'): ?>
                            <i class="fas fa-check-circle"></i>
                        <?php elseif ($_SESSION['flash_type'] == 'error' || $_SESSION['flash_type'] == 'danger'): ?>
                            <i class="fas fa-exclamation-circle"></i>
                        <?php elseif ($_SESSION['flash_type'] == 'warning'): ?>
                            <i class="fas fa-exclamation-triangle"></i>
                        <?php elseif ($_SESSION['flash_type'] == 'info'): ?>
                            <i class="fas fa-info-circle"></i>
                        <?php endif; ?>
                    </div>
                    <div class="alert-content">
                        <?php echo $_SESSION['flash_message']; ?>
                    </div>
                    <button type="button" class="alert-close" aria-label="Fechar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']);
                ?>
            <?php endif; ?>

            <?php if (isset($pageHeading)): ?>
                <div class="page-header">
                    <h1><?php echo $pageHeading; ?></h1>
                    <?php if (isset($pageDescription)): ?>
                        <p class="page-description"><?php echo $pageDescription; ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>