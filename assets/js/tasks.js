// assets/js/tasks.js

document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const tasksListContainer = document.getElementById('tasks-list');
    const statusFilter = document.getElementById('status-filter');
    const priorityFilter = document.getElementById('priority-filter');
    const searchFilter = document.getElementById('search-filter');
    const searchBtn = document.getElementById('search-btn');
    const newTaskBtn = document.getElementById('new-task-btn');
    const taskForm = document.getElementById('task-form');
    const taskModal = document.getElementById('task-modal');
    const modalTitle = document.getElementById('modal-title');
    const confirmModal = document.getElementById('confirm-modal');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    
    // Variáveis de estado
    let tasks = [];
    let currentTaskId = null;
    let filters = {
        status: 'todos',
        priority: 'todas',
        search: '',
        order_by: 'created_at',
        order_direction: 'DESC'
    };
    
    // Inicialização
    loadTasks();
    
    // Event Listeners
    statusFilter.addEventListener('change', handleFilterChange);
    priorityFilter.addEventListener('change', handleFilterChange);
    searchBtn.addEventListener('click', handleSearch);
    searchFilter.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            handleSearch();
        }
    });
    
    newTaskBtn.addEventListener('click', openNewTaskModal);
    taskForm.addEventListener('submit', handleTaskSubmit);
    
    // Função para carregar tarefas
    function loadTasks() {
        // Mostrar spinner de carregamento
        tasksListContainer.innerHTML = `
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i> Carregando tarefas...
            </div>
        `;
        
        // Construir URL com filtros
        let url = API_URL + 'tasks.php?';
        
        // Adicionar filtros à URL
        if (filters.status !== 'todos') url += `status=${filters.status}&`;
        if (filters.priority !== 'todas') url += `priority=${filters.priority}&`;
        if (filters.search) url += `search=${encodeURIComponent(filters.search)}&`;
        url += `order_by=${filters.order_by}&order_direction=${filters.order_direction}`;
        
        // Buscar tarefas
        fetch(url, {
            method: 'GET',
            credentials: 'include'
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 0) {
                    throw new Error('Erro de conexão com o servidor');
                } else if (response.status === 404) {
                    throw new Error('API não encontrada. Verifique a configuração de API_URL');
                } else if (response.status === 401) {
                    // Redirecionamento em caso de sessão expirada
                    window.location.href = BASE_URL + 'login.php?session_expired=true';
                    throw new Error('Sessão expirada. Por favor, faça login novamente.');
                } else {
                    throw new Error('Erro de servidor: ' + response.status);
                }
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                tasks = data.tasks || [];
                updateTasksStats(data.counts);
                renderTasks();
            } else {
                showError('Erro ao carregar tarefas: ' + (data.error || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro ao carregar tarefas:', error);
            
            if (error.message.includes('Failed to fetch') || 
                error.message.includes('NetworkError') || 
                error.message.includes('conexão')) {
                showError(`Erro ao conectar com o servidor. Verifique sua conexão de internet ou se o servidor está online.<br><br>
                           <button class="btn btn-primary" onclick="window.location.reload()">
                               <i class="fas fa-sync"></i> Tentar novamente
                           </button>`);
            } else {
                showError(error.message || 'Erro ao carregar tarefas');
            }
        });
    }
    
    // Função para atualizar estatísticas
    function updateTasksStats(counts) {
        document.getElementById('stat-total').textContent = counts.total || 0;
        document.getElementById('stat-pendente').textContent = counts.pendente || 0;
        document.getElementById('stat-em_andamento').textContent = counts.em_andamento || 0;
        document.getElementById('stat-concluido').textContent = counts.concluido || 0;
    }
    
    // Função para renderizar tarefas
    function renderTasks() {
        if (!tasks.length) {
            tasksListContainer.innerHTML = `
                <div class="empty-tasks">
                    <i class="fas fa-tasks"></i>
                    <p>Nenhuma tarefa encontrada</p>
                    <button class="btn btn-primary" onclick="document.getElementById('new-task-btn').click()">
                        Criar nova tarefa
                    </button>
                </div>
            `;
            return;
        }
        
        let html = '';
        
        tasks.forEach(task => {
            html += `
                <div class="task-card priority-${task.priority}">
                    <div class="task-header">
                        <h3 class="task-title">${task.title}</h3>
                        <div class="task-actions">
                            <button class="edit-btn" data-id="${task.id}" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="delete-btn" data-id="${task.id}" data-title="${task.title}" title="Excluir">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    
                    ${task.description ? `<div class="task-description">${task.description}</div>` : ''}
                    
                    <div class="task-meta">
                        <div class="task-meta-item">
                            <i class="fas fa-calendar-alt"></i> ${formatDate(task.created_at)}
                        </div>
                        <div class="task-meta-item">
                            <i class="fas fa-flag"></i> Prioridade: ${formatPriority(task.priority)}
                        </div>
                        <div class="task-meta-item">
                            <span class="task-status status-${task.status}">${formatStatus(task.status)}</span>
                        </div>
                        <div class="task-meta-item task-actions-status">
                            ${generateStatusButtons(task)}
                        </div>
                    </div>
                </div>
            `;
        });
        
        tasksListContainer.innerHTML = html;
        
        // Adicionar event listeners para botões
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', handleEditTask);
        });
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', handleDeleteTask);
        });
        
        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.addEventListener('click', handleStatusChange);
        });
    }
    
    // Função para gerar botões de status
    function generateStatusButtons(task) {
        let buttons = '';
        
        switch (task.status) {
            case 'pendente':
                buttons = `
                    <button class="btn btn-primary status-btn" data-id="${task.id}" data-status="em_andamento">
                        <i class="fas fa-play"></i> Iniciar
                    </button>
                `;
                break;
                
            case 'em_andamento':
                buttons = `
                    <button class="btn btn-success status-btn" data-id="${task.id}" data-status="concluido">
                        <i class="fas fa-check"></i> Concluir
                    </button>
                `;
                break;
                
            case 'concluido':
                buttons = `
                    <button class="btn btn-secondary status-btn" data-id="${task.id}" data-status="pendente">
                        <i class="fas fa-redo"></i> Reabrir
                    </button>
                `;
                break;
        }
        
        return buttons;
    }
    
    // Handler para mudança de filtros
    function handleFilterChange() {
        filters.status = statusFilter.value;
        filters.priority = priorityFilter.value;
        loadTasks();
    }
    
    // Handler para busca
    function handleSearch() {
        filters.search = searchFilter.value;
        loadTasks();
    }
    
    // Função para abrir modal de nova tarefa
    function openNewTaskModal() {
        // Resetar formulário
        taskForm.reset();
        
        // Esconder campo de status para novas tarefas
        document.querySelector('.status-group').style.display = 'none';
        
        // Atualizar título do modal
        modalTitle.textContent = 'Nova Tarefa';
        
        // Limpar ID atual
        currentTaskId = null;
        
        // Abrir modal
        openModal('task-modal');
    }
    
    // Função para abrir modal de edição
    function handleEditTask(e) {
        const taskId = e.currentTarget.dataset.id;
        const task = tasks.find(t => t.id == taskId);
        
        if (!task) {
            showNotification('Tarefa não encontrada', 'error');
            return;
        }
        
        // Preencher formulário
        document.getElementById('task-id').value = task.id;
        document.getElementById('task-title').value = task.title;
        document.getElementById('task-description').value = task.description || '';
        document.getElementById('task-priority').value = task.priority;
        document.getElementById('task-status').value = task.status;
        
        // Mostrar campo de status para edição
        document.querySelector('.status-group').style.display = 'block';
        
        // Atualizar título do modal
        modalTitle.textContent = 'Editar Tarefa';
        
        // Salvar ID atual
        currentTaskId = task.id;
        
        // Abrir modal
        openModal('task-modal');
    }
    
    // Função para confirmar exclusão
    function handleDeleteTask(e) {
        const taskId = e.currentTarget.dataset.id;
        const taskTitle = e.currentTarget.dataset.title;
        
        // Preencher informações no modal de confirmação
        confirmModal.querySelector('.task-to-delete').textContent = taskTitle;
        
        // Salvar ID para exclusão
        currentTaskId = taskId;
        
        // Abrir modal de confirmação
        openModal('confirm-modal');
        
        // Adicionar event listener para botão de confirmar
        confirmDeleteBtn.onclick = confirmDelete;
    }
    
    // Função para confirmar exclusão
    function confirmDelete() {
        if (!currentTaskId) {
            closeModal('confirm-modal');
            return;
        }
        
        // Fazer requisição para excluir tarefa
        fetch(API_URL + 'tasks.php?id=' + currentTaskId, {
            method: 'DELETE',
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Tarefa excluída com sucesso', 'success');
                loadTasks();
            } else {
                showNotification(data.error || 'Erro ao excluir tarefa', 'error');
            }
            
            closeModal('confirm-modal');
        })
        .catch(error => {
            console.error('Erro ao excluir tarefa:', error);
            showNotification('Erro ao excluir tarefa', 'error');
            closeModal('confirm-modal');
        });
    }
    
    // Função para enviar formulário
    function handleTaskSubmit(e) {
        e.preventDefault();
        
        // Obter dados do formulário
        const formData = new FormData(taskForm);
        const taskData = {
            title: formData.get('title'),
            description: formData.get('description'),
            priority: formData.get('priority')
        };
        
        // Adicionar status se estiver editando
        if (currentTaskId) {
            taskData.status = formData.get('status');
        }
        
        // Validação básica
        if (!taskData.title.trim()) {
            showNotification('O título da tarefa é obrigatório', 'error');
            return;
        }
        
        // Determinar método e URL
        const method = currentTaskId ? 'PUT' : 'POST';
        const url = currentTaskId ? 
            API_URL + 'tasks.php?id=' + currentTaskId : 
            API_URL + 'tasks.php';
        
        // Enviar requisição
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(taskData),
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(
                    currentTaskId ? 'Tarefa atualizada com sucesso' : 'Tarefa criada com sucesso', 
                    'success'
                );
                
                closeModal('task-modal');
                loadTasks();
            } else {
                showNotification(data.error || 'Erro ao salvar tarefa', 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao salvar tarefa:', error);
            showNotification('Erro ao salvar tarefa', 'error');
        });
    }
    
    // Função para mudar status da tarefa
    function handleStatusChange(e) {
        const taskId = e.currentTarget.dataset.id;
        const newStatus = e.currentTarget.dataset.status;
        
        // Dados para enviar
        const data = {
            status: newStatus
        };
        
        // Enviar requisição
        fetch(API_URL + 'tasks.php?id=' + taskId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data),
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Status atualizado com sucesso', 'success');
                loadTasks();
            } else {
                showNotification(data.error || 'Erro ao atualizar status', 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar status:', error);
            showNotification('Erro ao atualizar status', 'error');
        });
    }
    
    // Função para mostrar erro
    function showError(message) {
        tasksListContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> ${message}
            </div>
        `;
    }
});