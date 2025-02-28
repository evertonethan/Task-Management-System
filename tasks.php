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

// Incluir cabeçalho (verifica se o arquivo existe)
$header_file = 'includes/header.php';
if (file_exists($header_file)) {
    require_once $header_file;
} else {
    // Cabeçalho básico caso o arquivo não exista
    echo '<!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . $pageTitle . ' - ' . APP_NAME . '</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            :root {
                --primary-color: #007bff;
                --primary-color-dark: #0056b3;
                --primary-color-rgb: 0, 123, 255;
                --secondary-color: #6c757d;
                --success-color: #28a745;
                --danger-color: #dc3545;
                --warning-color: #ffc107;
                --info-color: #17a2b8;
                --light-color: #f8f9fa;
                --dark-color: #343a40;
            }
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f5f5f5;
                color: #333;
            }
            .container {
                width: 100%;
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 15px;
            }
            .navbar {
                background-color: var(--primary-color);
                color: white;
                padding: 1rem 0;
            }
            .navbar a {
                color: white;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
    <nav class="navbar">
        <div class="container">
            <a href="' . BASE_URL . '">' . APP_NAME . '</a>
            <div>
                <a href="logout.php">Sair</a>
            </div>
        </div>
    </nav>';
}
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
                        <input type="hidden" id="task-id" value="">

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

                        <div class="form-group">
                            <label for="task-due-date">Data de vencimento (opcional)</label>
                            <input type="date" id="task-due-date" name="due_date" class="form-control">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Definir as constantes com base nos valores do PHP
        const API_URL = '<?php echo API_URL; ?>';
        const BASE_URL = '<?php echo BASE_URL; ?>';
        const USER_ID = <?php echo $_SESSION['user_id'] ?? 0; ?>;

        // Elementos do DOM
        const tasksList = document.getElementById('tasks-list');
        const taskForm = document.getElementById('task-form');
        const taskTitle = document.getElementById('task-title');
        const taskDescription = document.getElementById('task-description');
        const taskPriority = document.getElementById('task-priority');
        const taskDueDate = document.getElementById('task-due-date');
        const taskId = document.getElementById('task-id');
        const formTitle = document.getElementById('form-title');
        const submitBtn = document.getElementById('task-submit');
        const cancelBtn = document.getElementById('cancel-edit');

        // Filtros e pesquisa
        const searchInput = document.getElementById('task-search');
        const statusFilters = document.querySelectorAll('.status-filter');
        const priorityFilter = document.getElementById('priority-filter');
        const sortOptions = document.getElementById('sort-options');

        // Contadores
        const counterPendente = document.getElementById('counter-pendente');
        const counterEmAndamento = document.getElementById('counter-em_andamento');
        const counterConcluido = document.getElementById('counter-concluido');
        const counterTotal = document.getElementById('counter-total');

        let currentTasks = [];
        let currentFilter = 'todos';
        let currentPriority = 'todas';
        let currentSort = 'created_at-DESC';
        let searchTerm = '';

        // Carregar tarefas
        function loadTasks() {
            tasksList.innerHTML = '<div class="loading">Carregando tarefas...</div>';

            fetch(API_URL + 'tasks.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'list',
                        user_id: USER_ID
                    }),
                    credentials: 'include'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Falha ao carregar tarefas: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        currentTasks = data.tasks || [];
                        updateCounters(currentTasks);
                        filterAndDisplayTasks();
                    } else {
                        tasksList.innerHTML = '<div class="error-message">Erro ao carregar tarefas: ' + (data.error || 'Erro desconhecido') + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    tasksList.innerHTML = '<div class="error-message">Erro ao conectar com o servidor. Por favor, tente novamente.</div>';

                    // Verificar se a API existe
                    fetch(API_URL + 'tasks.php', {
                            method: 'HEAD'
                        })
                        .then(response => {
                            if (!response.ok) {
                                tasksList.innerHTML = '<div class="error-message">Erro: API de tarefas não encontrada. Verifique se o arquivo ' + API_URL + 'tasks.php existe.</div>';
                            }
                        })
                        .catch(() => {
                            tasksList.innerHTML = '<div class="error-message">Erro ao conectar com a API de tarefas. Verifique sua conexão.</div>';
                        });
                });
        }

        // Filtrar e exibir tarefas
        function filterAndDisplayTasks() {
            // Aplicar filtros
            let filteredTasks = currentTasks;

            // Filtro de status
            if (currentFilter !== 'todos') {
                filteredTasks = filteredTasks.filter(task => task.status === currentFilter);
            }

            // Filtro de prioridade
            if (currentPriority !== 'todas') {
                filteredTasks = filteredTasks.filter(task => task.priority === currentPriority);
            }

            // Pesquisa
            if (searchTerm) {
                const term = searchTerm.toLowerCase();
                filteredTasks = filteredTasks.filter(task =>
                    task.title.toLowerCase().includes(term) ||
                    (task.description && task.description.toLowerCase().includes(term))
                );
            }

            // Ordenação
            const [sortField, sortOrder] = currentSort.split('-');
            filteredTasks.sort((a, b) => {
                let compA = a[sortField];
                let compB = b[sortField];

                // Tratamento especial para prioridade
                if (sortField === 'priority') {
                    const priorityOrder = {
                        'baixa': 1,
                        'media': 2,
                        'alta': 3
                    };
                    compA = priorityOrder[compA];
                    compB = priorityOrder[compB];
                }

                if (compA < compB) return sortOrder === 'ASC' ? -1 : 1;
                if (compA > compB) return sortOrder === 'ASC' ? 1 : -1;
                return 0;
            });

            // Exibir tarefas
            if (filteredTasks.length === 0) {
                tasksList.innerHTML = '<div class="no-tasks">Nenhuma tarefa encontrada.</div>';
                return;
            }

            let tasksHTML = '';
            filteredTasks.forEach(task => {
                const priorityClass = `priority-${task.priority}`;
                const statusClass = `status-${task.status}`;

                let statusText = '';
                switch (task.status) {
                    case 'pendente':
                        statusText = 'Pendente';
                        break;
                    case 'em_andamento':
                        statusText = 'Em Andamento';
                        break;
                    case 'concluido':
                        statusText = 'Concluída';
                        break;
                }

                let priorityText = '';
                switch (task.priority) {
                    case 'baixa':
                        priorityText = 'Baixa';
                        break;
                    case 'media':
                        priorityText = 'Média';
                        break;
                    case 'alta':
                        priorityText = 'Alta';
                        break;
                }

                // Formatar data de vencimento
                let dueDateStr = '';
                if (task.due_date) {
                    const dueDate = new Date(task.due_date);
                    dueDateStr = dueDate.toLocaleDateString('pt-BR');
                }

                tasksHTML += `
            <div class="task-card ${statusClass} ${priorityClass}" data-id="${task.id}">
                <div class="task-header">
                    <h3 class="task-title">${task.title}</h3>
                    <div class="task-actions">
                        <button class="btn-edit" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn-delete" title="Excluir"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div class="task-body">
                    <p class="task-description">${task.description || 'Sem descrição'}</p>
                </div>
                <div class="task-footer">
                    <div class="task-meta">
                        <span class="task-priority" title="Prioridade">${priorityText}</span>
                        ${dueDateStr ? `<span class="task-due-date" title="Data de vencimento"><i class="fas fa-calendar-alt"></i> ${dueDateStr}</span>` : ''}
                        <span class="task-date" title="Data de criação">${new Date(task.created_at).toLocaleDateString('pt-BR')}</span>
                    </div>
                    <div class="task-status-actions">
                        <select class="task-status-select">
                            <option value="pendente" ${task.status === 'pendente' ? 'selected' : ''}>Pendente</option>
                            <option value="em_andamento" ${task.status === 'em_andamento' ? 'selected' : ''}>Em Andamento</option>
                            <option value="concluido" ${task.status === 'concluido' ? 'selected' : ''}>Concluída</option>
                        </select>
                    </div>
                </div>
            </div>`;
            });

            tasksList.innerHTML = tasksHTML;

            // Adicionar event listeners aos botões de cada tarefa
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', handleEditClick);
            });

            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', handleDeleteClick);
            });

            document.querySelectorAll('.task-status-select').forEach(select => {
                select.addEventListener('change', handleStatusChange);
            });
        }

        // Atualizar contadores
        function updateCounters(tasks) {
            const counts = {
                pendente: 0,
                em_andamento: 0,
                concluido: 0,
                total: tasks.length
            };

            tasks.forEach(task => {
                counts[task.status]++;
            });

            counterPendente.textContent = counts.pendente;
            counterEmAndamento.textContent = counts.em_andamento;
            counterConcluido.textContent = counts.concluido;
            counterTotal.textContent = counts.total;
        }

        // Manipuladores de eventos
        function handleSubmit(e) {
            e.preventDefault();

            const id = taskId.value;
            const title = taskTitle.value.trim();
            const description = taskDescription.value.trim();
            const priority = taskPriority.value;
            const due_date = taskDueDate.value || null;

            if (!title) {
                alert('O título da tarefa é obrigatório.');
                return;
            }

            const isEditing = !!id;
            const action = isEditing ? 'update' : 'create';
            const apiData = {
                action,
                user_id: USER_ID,
                title,
                description,
                priority,
                due_date
            };

            if (isEditing) {
                apiData.id = id;
            }

            // Desabilitar botão durante o envio
            submitBtn.disabled = true;
            submitBtn.textContent = isEditing ? 'Atualizando...' : 'Criando...';

            fetch(API_URL + 'tasks.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(apiData),
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resetForm();
                        loadTasks();
                    } else {
                        alert('Erro: ' + (data.error || 'Ocorreu um erro.'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao conectar com o servidor.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = isEditing ? 'Atualizar Tarefa' : 'Criar Tarefa';
                });
        }

        function handleEditClick(e) {
            const taskCard = e.currentTarget.closest('.task-card');
            const id = taskCard.dataset.id;
            const task = currentTasks.find(t => t.id == id);

            if (!task) return;

            // Preencher o formulário com os dados da tarefa
            taskId.value = task.id;
            taskTitle.value = task.title;
            taskDescription.value = task.description || '';
            taskPriority.value = task.priority;
            taskDueDate.value = task.due_date || '';

            // Atualizar a interface do formulário
            formTitle.textContent = 'Editar Tarefa';
            submitBtn.textContent = 'Atualizar Tarefa';
            cancelBtn.style.display = 'inline-block';

            // Rolar até o formulário
            taskForm.scrollIntoView({
                behavior: 'smooth'
            });
        }

        function handleDeleteClick(e) {
            if (!confirm('Tem certeza que deseja excluir esta tarefa?')) return;

            const taskCard = e.currentTarget.closest('.task-card');
            const id = taskCard.dataset.id;

            fetch(API_URL + 'tasks.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        id: id,
                        user_id: USER_ID
                    }),
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadTasks();
                    } else {
                        alert('Erro ao excluir: ' + (data.error || 'Ocorreu um erro.'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao conectar com o servidor.');
                });
        }

        function handleStatusChange(e) {
            const taskCard = e.currentTarget.closest('.task-card');
            const id = taskCard.dataset.id;
            const newStatus = e.currentTarget.value;

            fetch(API_URL + 'tasks.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'update_status',
                        id: id,
                        status: newStatus,
                        user_id: USER_ID
                    }),
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadTasks();
                    } else {
                        alert('Erro ao atualizar status: ' + (data.error || 'Ocorreu um erro.'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao conectar com o servidor.');
                });
        }

        function resetForm() {
            taskId.value = '';
            taskForm.reset();
            formTitle.textContent = 'Nova Tarefa';
            submitBtn.textContent = 'Criar Tarefa';
            cancelBtn.style.display = 'none';
        }

        // Event listeners
        taskForm.addEventListener('submit', handleSubmit);

        cancelBtn.addEventListener('click', resetForm);

        searchInput.addEventListener('input', function() {
            searchTerm = this.value.trim();
            filterAndDisplayTasks();
        });

        statusFilters.forEach(filter => {
            filter.addEventListener('click', function(e) {
                e.preventDefault();

                statusFilters.forEach(f => f.classList.remove('active'));
                this.classList.add('active');

                currentFilter = this.dataset.status;
                filterAndDisplayTasks();
            });
        });

        priorityFilter.addEventListener('change', function() {
            currentPriority = this.value;
            filterAndDisplayTasks();
        });

        sortOptions.addEventListener('change', function() {
            currentSort = this.value;
            filterAndDisplayTasks();
        });

        // Inicializar
        loadTasks();
    });
</script>

<style>
    /* Estilos globais */
    .tasks-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem;
    }

    .tasks-header {
        margin-bottom: 2rem;
    }

    .tasks-header h1 {
        margin: 0 0 1rem 0;
        color: var(--primary-color);
    }

    /* Grid de tarefas */
    .tasks-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .tasks-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Contadores de status */
    .status-counters {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .counter {
        background: white;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        min-width: 100px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .counter[data-status="pendente"] {
        border-left: 4px solid #ffc107;
    }

    .counter[data-status="em_andamento"] {
        border-left: 4px solid #17a2b8;
    }

    .counter[data-status="concluido"] {
        border-left: 4px solid #28a745;
    }

    .counter-total {
        border-left: 4px solid #6c757d;
    }

    .counter-value {
        display: block;
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 0.25rem;
    }

    .counter-label {
        font-size: 0.875rem;
        color: #6c757d;
    }

    /* Formulário */
    .card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-header {
        background: #f8f9fa;
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .card-header h3 {
        margin: 0;
        color: var(--primary-color);
    }

    .card-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb), 0.2);
    }

    .form-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .btn {
        cursor: pointer;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        font-size: 1rem;
        transition: all 0.3s;
        border: none;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-color-dark);
    }

    .btn-secondary {
        background: var(--secondary-color);
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    /* Filtros */
    .tasks-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        align-items: center;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .search-bar {
        position: relative;
        width: 100%;
    }

    .btn-search {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
    }

    .filter-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .status-filter {
        padding: 0.5rem 1rem;
        border-radius: 4px;
        color: #495057;
        text-decoration: none;
        background: #f8f9fa;
        transition: all 0.3s;
    }

    .status-filter:hover {
        background: #e9ecef;
    }

    .status-filter.active {
        background: var(--primary-color);
        color: white;
    }

    /* Lista de tarefas */
    .tasks-list {
        margin-top: 1rem;
    }

    .task-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 1rem;
        overflow: hidden;
        border-left: 4px solid #ccc;
    }

    .task-card.status-pendente {
        border-left-color: #ffc107;
    }

    .task-card.status-em_andamento {
        border-left-color: #17a2b8;
    }

    .task-card.status-concluido {
        border-left-color: #28a745;
    }

    .task-card.priority-baixa {
        border-right: 4px solid #28a745;
    }

    .task-card.priority-media {
        border-right: 4px solid #ffc107;
    }

    .task-card.priority-alta {
        border-right: 4px solid #dc3545;
    }

    .task-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .task-title {
        margin: 0;
        font-size: 1.1rem;
    }

    .task-actions {
        display: flex;
        gap: 0.5rem;
    }

    /* Continuação dos estilos CSS */
    .btn-edit,
    .btn-delete {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem;
        color: #6c757d;
        transition: color 0.3s;
    }

    .btn-edit:hover {
        color: var(--primary-color);
    }

    .btn-delete:hover {
        color: var(--danger-color);
    }

    .task-body {
        padding: 1rem;
    }

    .task-description {
        margin: 0;
        color: #6c757d;
        line-height: 1.5;
    }

    .task-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        background: #f8f9fa;
        border-top: 1px solid #f0f0f0;
    }

    .task-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .task-priority {
        font-weight: 500;
    }

    .priority-alta {
        color: var(--danger-color);
    }

    .priority-media {
        color: var(--warning-color);
    }

    .priority-baixa {
        color: var(--success-color);
    }

    .task-status-select {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }

    /* Estados de carregamento e erro */
    .loading,
    .error-message,
    .no-tasks {
        padding: 2rem;
        text-align: center;
        background: white;
        border-radius: 8px;
        margin: 1rem 0;
    }

    .loading {
        color: var(--info-color);
    }

    .error-message {
        color: var(--danger-color);
    }

    .no-tasks {
        color: #6c757d;
    }

    /* Responsividade */
    @media (max-width: 576px) {
        .tasks-filters {
            flex-direction: column;
        }

        .filter-group {
            width: 100%;
        }

        .task-footer {
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .task-meta {
            flex-wrap: wrap;
        }
    }