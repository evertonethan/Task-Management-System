<?php
// tasks.php
require_once 'config/config.php';

// Verificar se o usuário está autenticado
checkAuth();

// Definir título da página
$pageTitle = 'Minhas Tarefas';

// Scripts adicionais para esta página
$extraScripts = ['assets/js/tasks.js'];

// Incluir cabeçalho
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="tasks-container">
    <div class="tasks-header">
        <h2>Minhas Tarefas</h2>
        <button id="new-task-btn" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Tarefa
        </button>
    </div>

    <div class="task-filters">
        <div class="filter-group">
            <label for="status-filter">Status:</label>
            <select id="status-filter">
                <option value="todos">Todos</option>
                <option value="pendente">Pendente</option>
                <option value="em_andamento">Em Andamento</option>
                <option value="concluido">Concluído</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="priority-filter">Prioridade:</label>
            <select id="priority-filter">
                <option value="todas">Todas</option>
                <option value="baixa">Baixa</option>
                <option value="media">Média</option>
                <option value="alta">Alta</option>
            </select>
        </div>

        <div class="filter-group search-group">
            <input type="text" id="search-filter" placeholder="Buscar tarefas...">
            <button id="search-btn"><i class="fas fa-search"></i></button>
        </div>
    </div>

    <div class="tasks-stats">
        <div class="stat-card">
            <div class="stat-title">Total</div>
            <div class="stat-value" id="stat-total">0</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Pendentes</div>
            <div class="stat-value" id="stat-pendente">0</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Em Andamento</div>
            <div class="stat-value" id="stat-em_andamento">0</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Concluídas</div>
            <div class="stat-value" id="stat-concluido">0</div>
        </div>
    </div>

    <div class="tasks-list" id="tasks-list">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i> Carregando tarefas...
        </div>
    </div>
</div>

<!-- Modal para Nova/Editar Tarefa -->
<div class="modal" id="task-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Nova Tarefa</h3>
            <button class="close-modal">&times;</button>
        </div>

        <div class="modal-body">
            <form id="task-form">
                <input type="hidden" id="task-id">

                <div class="form-group">
                    <label for="task-title">Título</label>
                    <input type="text" id="task-title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="task-description">Descrição</label>
                    <textarea id="task-description" name="description" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="task-priority">Prioridade</label>
                    <select id="task-priority" name="priority">
                        <option value="baixa">Baixa</option>
                        <option value="media" selected>Média</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>

                <div class="form-group status-group">
                    <label for="task-status">Status</label>
                    <select id="task-status" name="status">
                        <option value="pendente">Pendente</option>
                        <option value="em_andamento">Em Andamento</option>
                        <option value="concluido">Concluído</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação para Exclusão -->
<div class="modal" id="confirm-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar Exclusão</h3>
            <button class="close-modal">&times;</button>
        </div>

        <div class="modal-body">
            <p>Tem certeza que deseja excluir esta tarefa?</p>
            <p class="task-to-delete"></p>

            <div class="modal-buttons">
                <button id="confirm-delete" class="btn btn-danger">Sim, excluir</button>
                <button class="btn btn-secondary close-modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>