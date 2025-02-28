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
    <!-- Breadcrumbs para melhor navegação -->
    <div class="breadcrumbs">
        <ol>
            <li><a href="<?php echo BASE_URL; ?>dashboard.php">Dashboard</a></li>
            <li>Minhas Tarefas</li>
        </ol>
    </div>

    <div class="page-header">
        <h1>Minhas Tarefas</h1>
        <p class="page-description">Gerencie suas tarefas, organize por prioridade e acompanhe seu progresso</p>
    </div>

    <div class="dashboard-actions">
        <button id="new-task-btn" class="btn btn-primary pulse-animation">
            <i class="fas fa-plus"></i> Nova Tarefa
        </button>

        <div class="view-toggle">
            <button id="grid-view-btn" class="view-btn active" title="Visualização em Grade">
                <i class="fas fa-th-large"></i>
            </button>
            <button id="list-view-btn" class="view-btn" title="Visualização em Lista">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>

    <div class="task-filters-wrapper">
        <div class="task-filters">
            <div class="filter-group">
                <label for="status-filter">Status:</label>
                <div class="select-wrapper">
                    <select id="status-filter" aria-label="Filtrar por status">
                        <option value="todos">Todos</option>
                        <option value="pendente">Pendente</option>
                        <option value="em_andamento">Em Andamento</option>
                        <option value="concluido">Concluído</option>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>

            <div class="filter-group">
                <label for="priority-filter">Prioridade:</label>
                <div class="select-wrapper">
                    <select id="priority-filter" aria-label="Filtrar por prioridade">
                        <option value="todas">Todas</option>
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta</option>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>

            <div class="filter-group search-group">
                <div class="search-input-wrapper">
                    <input type="text" id="search-filter" placeholder="Buscar tarefas..." aria-label="Buscar tarefas">
                    <button id="search-btn" aria-label="Buscar"><i class="fas fa-search"></i></button>
                    <button id="clear-search" class="clear-search" aria-label="Limpar busca"><i class="fas fa-times"></i></button>
                </div>
            </div>
        </div>

        <div class="applied-filters" id="applied-filters">
            <!-- Filtros aplicados serão mostrados aqui -->
        </div>
    </div>

    <div class="tasks-stats">
        <div class="stat-card" tabindex="0">
            <div class="stat-icon"><i class="fas fa-tasks"></i></div>
            <div class="stat-info">
                <div class="stat-title">Total</div>
                <div class="stat-value" id="stat-total">0</div>
            </div>
            <div class="stat-progress">
                <div class="progress-bar" style="width: 100%"></div>
            </div>
        </div>

        <div class="stat-card" tabindex="0">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <div class="stat-title">Pendentes</div>
                <div class="stat-value" id="stat-pendente">0</div>
            </div>
            <div class="stat-progress">
                <div class="progress-bar bg-warning" id="pendente-progress"></div>
            </div>
        </div>

        <div class="stat-card" tabindex="0">
            <div class="stat-icon"><i class="fas fa-spinner"></i></div>
            <div class="stat-info">
                <div class="stat-title">Em Andamento</div>
                <div class="stat-value" id="stat-em_andamento">0</div>
            </div>
            <div class="stat-progress">
                <div class="progress-bar bg-info" id="em_andamento-progress"></div>
            </div>
        </div>

        <div class="stat-card" tabindex="0">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-title">Concluídas</div>
                <div class="stat-value" id="stat-concluido">0</div>
            </div>
            <div class="stat-progress">
                <div class="progress-bar bg-success" id="concluido-progress"></div>
            </div>
        </div>
    </div>

    <!-- Mensagem para lista vazia -->
    <div id="empty-state" class="empty-state hidden">
        <img src="<?php echo BASE_URL; ?>assets/images/empty-tasks.svg" alt="Nenhuma tarefa encontrada" class="empty-image">
        <h3>Nenhuma tarefa encontrada</h3>
        <p>Você não tem tarefas ou nenhuma corresponde aos filtros aplicados.</p>
        <button id="create-first-task" class="btn btn-primary">
            <i class="fas fa-plus"></i> Criar primeira tarefa
        </button>
    </div>

    <!-- Componente de carregamento melhorado -->
    <div id="loading-container" class="loading-container">
        <div class="loading-spinner">
            <div class="spinner-border" role="status">
                <span class="sr-only">Carregando...</span>
            </div>
            <p>Carregando suas tarefas...</p>
        </div>
    </div>

    <!-- Lista de tarefas com design melhorado -->
    <div class="tasks-list" id="tasks-list"></div>

    <!-- Paginação -->
    <div class="pagination-container" id="pagination-container">
        <div class="pagination-info">Mostrando <span id="showing-count">0</span> de <span id="total-count">0</span> tarefas</div>
        <div class="pagination-controls" id="pagination-controls">
            <!-- Controles de paginação serão inseridos via JavaScript -->
        </div>
    </div>
</div>

<!-- Template para card de tarefa (usado pelo JavaScript) -->
<template id="task-card-template">
    <div class="task-card" data-id="">
        <div class="task-header">
            <div class="task-priority"></div>
            <div class="task-actions">
                <button class="task-action edit-task" aria-label="Editar tarefa">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="task-action delete-task" aria-label="Excluir tarefa">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
        <h3 class="task-title"></h3>
        <p class="task-description"></p>
        <div class="task-footer">
            <div class="task-status"></div>
            <div class="task-date"></div>
        </div>
    </div>
</template>

<!-- Modal para Nova/Editar Tarefa -->
<div class="modal" id="task-modal" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Nova Tarefa</h3>
            <button class="close-modal" aria-label="Fechar">&times;</button>
        </div>

        <div class="modal-body">
            <form id="task-form">
                <input type="hidden" id="task-id">

                <div class="form-group">
                    <label for="task-title">Título</label>
                    <input type="text" id="task-title" name="title" required autocomplete="off" maxlength="100">
                    <div class="input-feedback" id="title-feedback"></div>
                </div>

                <div class="form-group">
                    <label for="task-description">Descrição</label>
                    <textarea id="task-description" name="description" rows="4" maxlength="500"></textarea>
                    <div class="char-counter"><span id="desc-char-count">0</span>/500</div>
                </div>

                <div class="form-group form-row">
                    <div class="form-col">
                        <label for="task-priority">Prioridade</label>
                        <div class="select-wrapper">
                            <select id="task-priority" name="priority">
                                <option value="baixa">Baixa</option>
                                <option value="media" selected>Média</option>
                                <option value="alta">Alta</option>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <div class="form-col status-group">
                        <label for="task-status">Status</label>
                        <div class="select-wrapper">
                            <select id="task-status" name="status">
                                <option value="pendente">Pendente</option>
                                <option value="em_andamento">Em Andamento</option>
                                <option value="concluido">Concluído</option>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação para Exclusão -->
<div class="modal" id="confirm-modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar Exclusão</h3>
            <button class="close-modal" aria-label="Fechar">&times;</button>
        </div>

        <div class="modal-body">
            <div class="confirmation-message">
                <i class="fas fa-exclamation-triangle text-danger"></i>
                <div>
                    <p>Tem certeza que deseja excluir esta tarefa?</p>
                    <p class="task-to-delete"></p>
                    <p class="text-danger">Esta ação não pode ser desfeita.</p>
                </div>
            </div>

            <div class="modal-buttons">
                <button id="confirm-delete" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Sim, excluir
                </button>
                <button class="btn btn-secondary close-modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast para notificações -->
<div class="toast-container" id="toast-container"></div>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>