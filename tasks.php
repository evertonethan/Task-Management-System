<?php
// tasks.php
require_once 'config/config.php';

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    redirect('login.php');
}

// Definir título da página
$pageTitle = 'Minhas Tarefas';

// CSS adicional
$extraStyles = ['tasks.css'];

// JS adicional
$extraScripts = [];

// Incluir cabeçalho
require_once 'includes/header.php';
?>

<div class="tasks-container">
    <div class="tasks-header">
        <h1>Minhas Tarefas</h1>
        <div class="tasks-overview">
            <div class="status-counters">
                <div class="counter" data-status="pendente">
                    <span class="counter-value" id="counter-pendente">0</span>
                    <span class="counter-label">Pendentes</span>
                </div>
                <div class="counter" data-status="em_andamento">
                    <span class="counter-value" id="counter-em_andamento">0</span>
                    <span class="counter-label">Em Andamento</span>
                </div>
                <div class="counter" data-status="concluido">
                    <span class="counter-value" id="counter-concluido">0</span>
                    <span class="counter-label">Concluídas</span>
                </div>
                <div class="counter counter-total">
                    <span class="counter-value" id="counter-total">0</span>
                    <span class="counter-label">Total</span>
                </div>
            </div>
        </div>
    </div>

    <div class="tasks-grid">
        <!-- Coluna do formulário -->
        <div class="tasks-form-column">
            <div class="card">
                <div class="card-header">
                    <h3 id="form-title">Nova Tarefa</h3>
                </div>
                <div class="card-body">
                    <form id="task-form">
                        <div class="form-group">
                            <label for="task-title">Título</label>
                            <input type="text" id="task-title" name="title" class="form-control" required placeholder="Digite o título da tarefa">
                        </div>

                        <div class="form-group">
                            <label for="task-description">Descrição (opcional)</label>
                            <textarea id="task-description" name="description" class="form-control" rows="4" placeholder="Adicione uma descrição..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="task-priority">Prioridade</label>
                            <select id="task-priority" name="priority" class="form-select">
                                <option value="baixa">Baixa</option>
                                <option value="media" selected>Média</option>
                                <option value="alta">Alta</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="submit" id="task-submit" class="btn btn-primary">Criar Tarefa</button>
                            <button type="button" id="cancel-edit" class="btn btn-secondary" style="display: none;">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Coluna das tarefas -->
        <div class="tasks-list-column">
            <div class="tasks-filters">
                <div class="filter-group">
                    <div class="search-bar">
                        <input type="text" id="task-search" class="form-control" placeholder="Pesquisar tarefas...">
                        <button type="button" class="btn-search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="filter-group">
                    <div class="filter-buttons">
                        <a href="#" class="status-filter active" data-status="todos">Todas</a>
                        <a href="#" class="status-filter" data-status="pendente">Pendentes</a>
                        <a href="#" class="status-filter" data-status="em_andamento">Em Andamento</a>
                        <a href="#" class="status-filter" data-status="concluido">Concluídas</a>
                    </div>
                </div>

                <div class="filter-group">
                    <select id="priority-filter" class="form-select">
                        <option value="todas">Todas as prioridades</option>
                        <option value="baixa">Prioridade Baixa</option>
                        <option value="media">Prioridade Média</option>
                        <option value="alta">Prioridade Alta</option>
                    </select>
                </div>

                <div class="filter-group">
                    <select id="sort-options" class="form-select">
                        <option value="created_at-DESC">Mais recentes primeiro</option>
                        <option value="created_at-ASC">Mais antigas primeiro</option>
                        <option value="title-ASC">Título (A-Z)</option>
                        <option value="title-DESC">Título (Z-A)</option>
                        <option value="priority-DESC">Prioridade (Alta → Baixa)</option>
                        <option value="priority-ASC">Prioridade (Baixa → Alta)</option>
                    </select>
                </div>
            </div>

            <div id="tasks-list" class="tasks-list">
                <!-- As tarefas serão carregadas dinamicamente pelo JavaScript -->
                <div class="loading">Carregando tarefas...</div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>