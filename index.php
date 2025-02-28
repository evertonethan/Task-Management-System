<?php
// index.php
require_once 'config/config.php';

// Se já estiver logado, redirecionar para a página de tarefas
if (isLoggedIn()) {
    redirect('tasks.php');
}

// Definir título da página
$pageTitle = 'Início';

// Incluir cabeçalho
require_once 'includes/header.php';
?>

<div class="landing-page">
    <div class="hero">
        <h1>Bem-vindo ao <?php echo APP_NAME; ?></h1>
        <p>Gerencie suas tarefas de forma simples e eficiente</p>

        <div class="cta-buttons">
            <a href="login.php" class="btn btn-primary">Entrar</a>
            <a href="register.php" class="btn btn-secondary">Cadastrar</a>
        </div>
    </div>

    <div class="features">
        <div class="feature-card">
            <i class="fas fa-tasks"></i>
            <h3>Organização</h3>
            <p>Organize suas tarefas por status e prioridade</p>
        </div>

        <div class="feature-card">
            <i class="fas fa-filter"></i>
            <h3>Filtros</h3>
            <p>Filtre suas tarefas para encontrar exatamente o que precisa</p>
        </div>

        <div class="feature-card">
            <i class="fas fa-mobile-alt"></i>
            <h3>Responsivo</h3>
            <p>Acesse de qualquer dispositivo, a qualquer momento</p>
        </div>
    </div>
</div>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>