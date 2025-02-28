<?php
// 404.php
require_once 'config/config.php';

// Definir título da página
$pageTitle = 'Página não encontrada';

// Incluir cabeçalho
require_once 'includes/header.php';
?>

<div class="error-container">
    <div class="error-content">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>404</h1>
        <h2>Página não encontrada</h2>
        <p>Desculpe, a página que você está procurando não existe ou foi movida.</p>
        <div class="error-actions">
            <a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Voltar para a Página Inicial</a>
        </div>
    </div>
</div>

<style>
    .error-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        text-align: center;
    }

    .error-content {
        max-width: 500px;
        padding: 2rem;
    }

    .error-icon {
        font-size: 3rem;
        color: var(--warning-color);
        margin-bottom: 1rem;
    }

    .error-container h1 {
        font-size: 6rem;
        margin: 0;
        color: var(--primary-color);
    }

    .error-container h2 {
        margin-bottom: 1rem;
    }

    .error-container p {
        margin-bottom: 2rem;
        color: var(--grey-color);
    }

    .error-actions {
        margin-top: 2rem;
    }
</style>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>