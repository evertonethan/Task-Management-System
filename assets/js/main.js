// assets/js/main.js

// Funções utilitárias gerais para o sistema
// Função para prevenção do problema de rotas duplas e #
function cleanUrl(url) {
    // Remover qualquer # do final
    url = url.replace(/#$/, '');
    
    // Corrigir barras duplas (exceto após protocolo)
    url = url.replace(/(https?:\/\/)|(\/)+/g, "$1$2");
    
    return url;
}

// Adicionar método para todos os links e botões de navegação
document.addEventListener('DOMContentLoaded', function() {
    // Interceptar todos os links para corrigir URLs
    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Ignorar links externos, âncoras puras e javascript:
            if (href.includes('://') || href.startsWith('#') || href.startsWith('javascript:')) {
                return;
            }
            
            e.preventDefault();
            const cleanedUrl = cleanUrl(href);
            window.location.href = cleanedUrl;
        });
    });
    
    // Logout
    const logoutBtn = document.getElementById('logout-btn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Redirecionar para a página de logout
            window.location.href = cleanUrl(BASE_URL + 'logout.php');
        });
    }
    
    // Fechar alertas automaticamente
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
});

// Função para formatação de data
function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    
    return new Date(dateString).toLocaleDateString('pt-BR', options);
}

// Função para formatar status com texto amigável
function formatStatus(status) {
    const statusMap = {
        'pendente': 'Pendente',
        'em_andamento': 'Em Andamento',
        'concluido': 'Concluído'
    };
    
    return statusMap[status] || status;
}

// Função para formatar prioridade com texto amigável
function formatPriority(priority) {
    const priorityMap = {
        'baixa': 'Baixa',
        'media': 'Média',
        'alta': 'Alta'
    };
    
    return priorityMap[priority] || priority;
}

// Função para exibir mensagem de notificação
function showNotification(message, type = 'success') {
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Adicionar ao corpo do documento
    document.body.appendChild(notification);
    
    // Mostrar e depois esconder
    setTimeout(() => {
        notification.classList.add('show');
        
        setTimeout(() => {
            notification.classList.remove('show');
            
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }, 100);
}

// Função para abrir modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        
        // Impedir rolagem do body
        document.body.style.overflow = 'hidden';
    }
}

// Função para fechar modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        
        // Restaurar rolagem do body
        document.body.style.overflow = '';
    }
}

// Configurar eventos de fechar para todos os modais
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(modal => {
        // Fechar ao clicar no botão de fechar
        const closeButtons = modal.querySelectorAll('.close-modal');
        
        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
        
        // Fechar ao clicar fora do modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
});