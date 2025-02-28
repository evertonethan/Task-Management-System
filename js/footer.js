// assets/js/footer.js

document.addEventListener('DOMContentLoaded', function() {
    // Botão de voltar ao topo
    const backToTopButton = document.getElementById('back-to-top');
    
    // Mostrar/esconder botão de voltar ao topo
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('show');
        } else {
            backToTopButton.classList.remove('show');
        }
    });
    
    // Funcionalidade do botão de voltar ao topo
    backToTopButton.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Gerenciamento de newsletter
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = document.getElementById('newsletter-email');
            const messageDiv = document.getElementById('newsletter-message');
            
            if (!emailInput.value.trim()) {
                showNewsletterMessage('Por favor, informe seu email', 'error');
                return;
            }
            
            // Simulação de inscrição na newsletter
            // Em um ambiente real, você enviaria uma requisição para o servidor
            setTimeout(function() {
                showNewsletterMessage('Inscrição realizada com sucesso!', 'success');
                emailInput.value = '';
            }, 1000);
        });
        
        function showNewsletterMessage(message, type) {
            const messageDiv = document.getElementById('newsletter-message');
            messageDiv.textContent = message;
            messageDiv.className = 'newsletter-message ' + type;
            
            // Limpar mensagem após 5 segundos
            setTimeout(function() {
                messageDiv.textContent = '';
                messageDiv.className = 'newsletter-message';
            }, 5000);
        }
    }

    // Gerenciamento de estatísticas no rodapé
    if (typeof USER_ID !== 'undefined') {
        updateFooterStats();
    }
    
    function updateFooterStats() {
        // Em um ambiente real, você buscaria esses dados do servidor
        // Aqui estamos apenas simulando
        
        // Supondo que você tenha um endpoint para obter estatísticas
        const statElements = document.querySelectorAll('.footer-stats .stat-value');
        
        if (statElements.length >= 2) {
            // Simular carregamento
            setTimeout(function() {
                // Isso seria substituído por dados reais da API
                const mockStats = {
                    total: Math.floor(Math.random() * 20) + 5,
                    completed: Math.floor(Math.random() * 10) + 1
                };
                
                statElements[0].textContent = mockStats.total;
                statElements[1].textContent = mockStats.completed;
            }, 1000);
        }
    }

    // Cookie consent
    const cookieConsent = document.getElementById('cookie-consent');
    const acceptCookiesBtn = document.getElementById('accept-cookies');
    
    // Verificar se o usuário já aceitou os cookies
    if (!localStorage.getItem('cookies-accepted')) {
        // Exibir banner após 2 segundos
        setTimeout(function() {
            cookieConsent.classList.add('show');
        }, 2000);
    }
    
    // Quando o usuário aceitar os cookies
    if (acceptCookiesBtn) {
        acceptCookiesBtn.addEventListener('click', function() {
            localStorage.setItem('cookies-accepted', 'true');
            cookieConsent.classList.remove('show');
            
            // Ocultar com animação
            cookieConsent.style.opacity = '0';
            cookieConsent.style.transform = 'translateY(20px)';
            
            setTimeout(function() {
                cookieConsent.style.display = 'none';
            }, 500);
        });
    }

    // Animação dos links do rodapé
    const footerLinks = document.querySelectorAll('.footer-links a');
    footerLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'translateX(5px)';
                setTimeout(() => {
                    icon.style.transform = 'translateX(0)';
                }, 300);
            }
        });
    });
});