// Versão aprimorada do header.js

document.addEventListener('DOMContentLoaded', function() {
    // Preloader
    const preloader = document.getElementById('preloader');
    if (preloader) {
        // Esconder preloader após o carregamento da página
        window.addEventListener('load', function() {
            setTimeout(function() {
                preloader.classList.add('hidden');
                // Remover completamente após a animação terminar
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 500);
            }, 500); // Pequeno delay para garantir que tudo carregou
        });
    }

    // Header scroll effect
    window.addEventListener('scroll', function() {
        const header = document.querySelector('header');
        if (window.scrollY > 10) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Mobile menu toggle
    const menuToggle = document.createElement('div');
    menuToggle.classList.add('menu-toggle');
    menuToggle.setAttribute('aria-label', 'Menu de navegação');
    menuToggle.setAttribute('role', 'button');
    menuToggle.setAttribute('tabindex', '0');
    menuToggle.innerHTML = '<span></span><span></span><span></span>';
    
    const headerContent = document.querySelector('.header-content');
    const nav = document.querySelector('nav');
    
    if (headerContent && nav) {
        headerContent.insertBefore(menuToggle, nav);
        
        // Create overlay
        const overlay = document.createElement('div');
        overlay.classList.add('menu-overlay');
        document.body.appendChild(overlay);
        
        // Função para toggle do menu
        function toggleMenu() {
            menuToggle.classList.toggle('active');
            nav.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Definir aria-expanded
            const isExpanded = menuToggle.classList.contains('active');
            menuToggle.setAttribute('aria-expanded', isExpanded);
            
            // Impedir rolagem quando menu estiver aberto
            document.body.style.overflow = isExpanded ? 'hidden' : '';
        }
        
        // Event listeners para o menu móvel
        menuToggle.addEventListener('click', toggleMenu);
        menuToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleMenu();
            }
        });
        
        overlay.addEventListener('click', function() {
            toggleMenu();
        });
    }
    
    // Mobile dropdown toggle
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const link = dropdown.querySelector('a');
        
        if (link) {
            // Em dispositivos móveis
            if (window.innerWidth <= 768) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Toggle do dropdown
                    dropdown.classList.toggle('active');
                    
                    // Atualizar aria-expanded
                    const isExpanded = dropdown.classList.contains('active');
                    link.setAttribute('aria-expanded', isExpanded);
                });
            } else {
                // Em desktop, apenas configurar aria attributes
                link.setAttribute('aria-expanded', 'false');
                
                // Atualizar aria-expanded quando hover
                dropdown.addEventListener('mouseenter', function() {
                    link.setAttribute('aria-expanded', 'true');
                });
                
                dropdown.addEventListener('mouseleave', function() {
                    link.setAttribute('aria-expanded', 'false');
                });
            }
        }
    });
    
    // Fechar alerta quando clicar no botão de fechar
    const alertCloseButtons = document.querySelectorAll('.alert-close');
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            
            // Adicionar classe para animar a saída
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            
            // Remover o alerta após a animação
            setTimeout(function() {
                alert.remove();
            }, 300);
        });
    });
    
    // Adicionar classe active ao link da página atual
    const currentPage = window.location.pathname;
    const navLinks = document.querySelectorAll('nav ul li a');
    
    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        if (linkPath && currentPage.endsWith(linkPath) && linkPath !== BASE_URL) {
            link.classList.add('active');
        }
    });
    
    // Resize handler
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            if (menuToggle) menuToggle.classList.remove('active');
            if (nav) nav.classList.remove('active');
            
            const overlay = document.querySelector('.menu-overlay');
            if (overlay) overlay.classList.remove('active');
            
            document.body.style.overflow = '';
            
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
});







document.addEventListener('DOMContentLoaded', function() {
    // Gerenciamento do dropdown de usuário
    const userDropdown = document.querySelector('.user-dropdown');
    if (userDropdown) {
        const dropdownToggle = userDropdown.querySelector('.dropdown-toggle');
        
        // Toggle dropdown ao clicar
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            userDropdown.classList.toggle('active');
            
            // Atualiza atributo aria-expanded
            const isExpanded = userDropdown.classList.contains('active');
            dropdownToggle.setAttribute('aria-expanded', isExpanded);
        });
        
        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
                dropdownToggle.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Suporte a navegação por teclado
        dropdownToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                dropdownToggle.click();
            }
        });
        
        // Fechar dropdown com Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && userDropdown.classList.contains('active')) {
                userDropdown.classList.remove('active');
                dropdownToggle.setAttribute('aria-expanded', 'false');
                dropdownToggle.focus();
            }
        });
    }
    
    // Confirmação de logout
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = this.href;
            }
        });
    }
    
    // Efeitos de feedback visual
    const navLinks = document.querySelectorAll('.nav-item a, .dropdown-item a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            this.classList.add('clicked');
            setTimeout(() => {
                this.classList.remove('clicked');
            }, 300);
        });
    });
});