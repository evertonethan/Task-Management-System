// js/main.js
document.addEventListener('DOMContentLoaded', function () {
    // Variáveis globais
    const API_BASE_URL = API_URL || window.location.origin + '/api/';
    let currentFilter = {
        status: 'todos',
        priority: 'todas',
        search: '',
        order_by: 'created_at',
        order_direction: 'DESC'
    };

    // Elementos DOM
    const tasksList = document.getElementById('tasks-list');
    const taskForm = document.getElementById('task-form');
    const taskSearch = document.getElementById('task-search');
    const statusFilters = document.querySelectorAll('.status-filter');
    const priorityFilter = document.getElementById('priority-filter');
    const sortOptions = document.getElementById('sort-options');
    const counters = {
        pendente: document.getElementById('counter-pendente'),
        em_andamento: document.getElementById('counter-em_andamento'),
        concluido: document.getElementById('counter-concluido'),
        total: document.getElementById('counter-total')
    };

    // Verificar se estamos na página de tarefas
    if (tasksList) {
        // Inicializar carregamento de tarefas
        loadTasks();

        // Event listeners para filtros
        statusFilters.forEach(filter => {
            filter.addEventListener('click', function (e) {
                e.preventDefault();
                // Remover classe ativa de todos os filtros
                statusFilters.forEach(f => f.classList.remove('active'));
                // Adicionar classe ativa ao filtro clicado
                this.classList.add('active');
                // Atualizar filtro
                currentFilter.status = this.dataset.status;
                loadTasks();
            });
        });

        if (priorityFilter) {
            priorityFilter.addEventListener('change', function () {
                currentFilter.priority = this.value;
                loadTasks();
            });
        }

        if (sortOptions) {
            sortOptions.addEventListener('change', function () {
                const [orderBy, orderDirection] = this.value.split('-');
                currentFilter.order_by = orderBy;
                currentFilter.order_direction = orderDirection;
                loadTasks();
            });
        }

        if (taskSearch) {
            // Debounce para a pesquisa
            let searchTimeout;
            taskSearch.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentFilter.search = this.value.trim();
                    loadTasks();
                }, 300);
            });
        }
    }

    // Event listener para o formulário de criação/edição de tarefa
    if (taskForm) {
        taskForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const taskId = this.dataset.taskId || null;
            const formData = {
                title: document.getElementById('task-title').value.trim(),
                description: document.getElementById('task-description').value.trim(),
                priority: document.getElementById('task-priority').value
            };

            if (taskId) {
                // Atualizar tarefa existente
                updateTask(taskId, formData);
            } else {
                // Criar nova tarefa
                createTask(formData);
            }
        });
    }

    // Funções
    function loadTasks() {
        const url = new URL(API_BASE_URL + 'tasks.php');
        
        // Adicionar filtros à URL
        Object.keys(currentFilter).forEach(key => {
            if (currentFilter[key] !== '') {
                url.searchParams.append(key, currentFilter[key]);
            }
        });

        // Mostrar indicador de carregamento
        if (tasksList) {
            tasksList.innerHTML = '<div class="loading">Carregando tarefas...</div>';
        }

        // Fazer a requisição
        fetch(url, {
            method: 'GET',
            credentials: 'include'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao carregar tarefas');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayTasks(data.tasks);
                updateCounters(data.counts);
            } else {
                showMessage('Erro ao carregar tarefas: ' + data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showMessage('Erro ao conectar com o servidor', 'error');
        });
    }

    function displayTasks(tasks) {
        if (!tasksList) return;

        if (tasks.length === 0) {
            tasksList.innerHTML = '<div class="no-tasks">Nenhuma tarefa encontrada.</div>';
            return;
        }

        tasksList.innerHTML = '';
        
        tasks.forEach(task => {
            const taskCard = document.createElement('div');
            taskCard.className = 'task-card';
            taskCard.dataset.id = task.id;

            // Definir classe CSS baseada no status
            const statusClass = {
                'pendente': 'task-pending',
                'em_andamento': 'task-progress',
                'concluido': 'task-completed'
            }[task.status] || '';

            // Definir classe CSS baseada na prioridade
            const priorityClass = {
                'baixa': 'priority-low',
                'media': 'priority-medium',
                'alta': 'priority-high'
            }[task.priority] || '';

            // Formatação da data
            const createdAt = new Date(task.created_at).toLocaleDateString('pt-BR');

            taskCard.innerHTML = `
                <div class="task-header ${statusClass} ${priorityClass}">
                    <div class="task-badges">
                        <span class="badge badge-status">${formatStatus(task.status)}</span>
                        <span class="badge badge-priority">${formatPriority(task.priority)}</span>
                    </div>
                    <div class="task-actions">
                        <button class="btn-icon btn-edit" title="Editar" data-id="${task.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon btn-delete" title="Excluir" data-id="${task.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="task-body">
                    <h3 class="task-title">${escapeHtml(task.title)}</h3>
                    <div class="task-description">${task.description ? escapeHtml(task.description) : '<em>Sem descrição</em>'}</div>
                </div>
                <div class="task-footer">
                    <div class="task-date">Criada em: ${createdAt}</div>
                    <div class="task-status-actions">
                        ${createStatusButtons(task.id, task.status)}
                    </div>
                </div>
            `;

            tasksList.appendChild(taskCard);

            // Adicionar event listeners para os botões de ação
            const editBtn = taskCard.querySelector('.btn-edit');
            const deleteBtn = taskCard.querySelector('.btn-delete');
            const statusBtns = taskCard.querySelectorAll('.btn-status');

            if (editBtn) {
                editBtn.addEventListener('click', () => editTask(task.id));
            }

            if (deleteBtn) {
                deleteBtn.addEventListener('click', () => confirmDeleteTask(task.id, task.title));
            }

            if (statusBtns) {
                statusBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const newStatus = this.dataset.status;
                        updateTaskStatus(task.id, newStatus);
                    });
                });
            }
        });
    }

    function createTask(formData) {
        if (!formData.title) {
            showMessage('O título da tarefa é obrigatório', 'error');
            return;
        }

        fetch(API_BASE_URL + 'tasks.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData),
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Tarefa criada com sucesso!', 'success');
                // Limpar formulário
                taskForm.reset();
                // Recarregar lista de tarefas
                loadTasks();
            } else {
                showMessage('Erro ao criar tarefa: ' + data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showMessage('Erro ao conectar com o servidor', 'error');
        });
    }

    function updateTask(taskId, formData) {
        if (!formData.title) {
            showMessage('O título da tarefa é obrigatório', 'error');
            return;
        }

        fetch(API_BASE_URL + 'tasks.php?id=' + taskId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData),
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Tarefa atualizada com sucesso!', 'success');
                
                // Resetar formulário e voltar ao modo de criação
                taskForm.reset();
                taskForm.dataset.taskId = '';
                document.getElementById('form-title').textContent = 'Nova Tarefa';
                document.getElementById('task-submit').textContent = 'Criar Tarefa';
                
                // Ocultar botão cancelar, se houver
                const cancelBtn = document.getElementById('cancel-edit');
                if (cancelBtn) cancelBtn.style.display = 'none';
                
                // Recarregar lista de tarefas
                loadTasks();
            } else {
                showMessage('Erro ao atualizar tarefa: ' + data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showMessage('Erro ao conectar com o servidor', 'error');
        });
    }

    function editTask(taskId) {
        fetch(API_BASE_URL + 'tasks.php?id=' + taskId, {
            method: 'GET',
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.task) {
                const task = data.task;
                
                // Preencher formulário com dados da tarefa
                document.getElementById('task-title').value = task.title;
                document.getElementById('task-description').value = task.description || '';
                document.getElementById('task-priority').value = task.priority;
                
                // Atualizar formulário para modo de edição
                taskForm.dataset.taskId = task.id;
                document.getElementById('form-title').textContent = 'Editar Tarefa';
                document.getElementById('task-submit').textContent = 'Atualizar Tarefa';
                
                // Mostrar botão cancelar, se houver
                const cancelBtn = document.getElementById('cancel-edit');
                if (cancelBtn) {
                    cancelBtn.style.display = 'inline-block';
                    cancelBtn.addEventListener('click', resetForm);
                }
                
                // Scroll para o formulário
                taskForm.scrollIntoView({ behavior: 'smooth' });
            } else {
                showMessage('Erro ao carregar dados da tarefa', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showMessage('Erro ao conectar com o servidor', 'error');
        });
    }

    function updateTaskStatus(taskId, newStatus) {
        fetch(API_BASE_URL + 'tasks.php?id=' + taskId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: newStatus }),
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Status atualizado com sucesso!', 'success');
                loadTasks();
            } else {
                showMessage('Erro ao atualizar status: ' + data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showMessage('Erro ao conectar com o servidor', 'error');
        });
    }

    function confirmDeleteTask(taskId, taskTitle) {
        if (confirm(`Tem certeza que deseja excluir a tarefa "${taskTitle}"?`)) {
            deleteTask(taskId);
        }
    }

    function deleteTask(taskId) {
        fetch(API_BASE_URL + 'tasks.php?id=' + taskId, {
            method: 'DELETE',
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Tarefa excluída com sucesso!', 'success');
                loadTasks();
            } else {
                showMessage('Erro ao excluir tarefa: ' + data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showMessage('Erro ao conectar com o servidor', 'error');
        });
    }

    function updateCounters(counts) {
        if (!counts) return;
        
        // Atualizar contadores de status
        Object.keys(counts).forEach(status => {
            const counter = counters[status];
            if (counter) {
                counter.textContent = counts[status];
            }
        });
    }

    function resetForm() {
        if (!taskForm) return;
        
        taskForm.reset();
        taskForm.dataset.taskId = '';
        document.getElementById('form-title').textContent = 'Nova Tarefa';
        document.getElementById('task-submit').textContent = 'Criar Tarefa';
        
        const cancelBtn = document.getElementById('cancel-edit');
        if (cancelBtn) cancelBtn.style.display = 'none';
    }

    // Funções Utilitárias
    function createStatusButtons(taskId, currentStatus) {
        const statusOptions = {
            'pendente': { next: 'em_andamento', label: 'Iniciar' },
            'em_andamento': { next: 'concluido', label: 'Concluir' },
            'concluido': { next: 'pendente', label: 'Reabrir' }
        };
        
        const nextStatus = statusOptions[currentStatus].next;
        const buttonLabel = statusOptions[currentStatus].label;
        
        return `<button class="btn btn-sm btn-status" data-id="${taskId}" data-status="${nextStatus}">${buttonLabel}</button>`;
    }

    function formatStatus(status) {
        const labels = {
            'pendente': 'Pendente',
            'em_andamento': 'Em Andamento',
            'concluido': 'Concluído'
        };
        return labels[status] || status;
    }

    function formatPriority(priority) {
        const labels = {
            'baixa': 'Baixa',
            'media': 'Média',
            'alta': 'Alta'
        };
        return labels[priority] || priority;
    }

    function showMessage(message, type = 'info') {
        // Verificar se já existe um elemento de mensagem
        let messageElement = document.getElementById('message-container');
        
        // Se não existir, criar um
        if (!messageElement) {
            messageElement = document.createElement('div');
            messageElement.id = 'message-container';
            document.body.appendChild(messageElement);
        }
        
        // Criar elemento da mensagem
        const alert = document.createElement('div');
        alert.className = `message message-${type}`;
        alert.textContent = message;
        
        // Adicionar ao container
        messageElement.appendChild(alert);
        
        // Remover após 5 segundos
        setTimeout(() => {
            alert.classList.add('fade-out');
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});