/**
 * header.js - Funcionalidades do cabeçalho responsivo
 */

document.addEventListener('DOMContentLoaded', function() {
    // Criar botão de menu para mobile
    const mobileMenuToggle = document.createElement('button');
    mobileMenuToggle.className = 'mobile-menu-toggle';
    mobileMenuToggle.innerHTML = '<i class="fas fa-bars"></i>';
    mobileMenuToggle.setAttribute('aria-label', 'Menu');
    
    const navbarContainer = document.querySelector('.navbar-container');
    const navbarNav = document.querySelector('.navbar-nav');
    
    // Criar overlay para o menu mobile
    const overlay = document.createElement('div');
    overlay.className = 'navbar-overlay';
    document.body.appendChild(overlay);
    
    // Inserir o botão de menu
    navbarContainer.insertBefore(mobileMenuToggle, navbarNav);
    
    // Adicionar evento de clique
    mobileMenuToggle.addEventListener('click', function() {
        navbarNav.classList.toggle('active');
        overlay.classList.toggle('active');
        
        // Atualizar ícone e ariaExpanded
        const icon = mobileMenuToggle.querySelector('i');
        
        if (navbarNav.classList.contains('active')) {
            icon.className = 'fas fa-times';
            mobileMenuToggle.setAttribute('aria-expanded', 'true');
        } else {
            icon.className = 'fas fa-bars';
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
        }
    });
    
    // Fechar menu ao clicar no overlay
    overlay.addEventListener('click', function() {
        navbarNav.classList.remove('active');
        overlay.classList.remove('active');
        mobileMenuToggle.querySelector('i').className = 'fas fa-bars';
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
    });
    
    // Fechar menu ao clicar em qualquer link
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navbarNav.classList.remove('active');
            overlay.classList.remove('active');
            mobileMenuToggle.querySelector('i').className = 'fas fa-bars';
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
        });
    });
    
    // Adicionar "Skip to content" para acessibilidade
    const skipToContent = document.createElement('a');
    skipToContent.className = 'skip-to-content';
    skipToContent.href = '#main-content';
    skipToContent.textContent = 'Pular para o conteúdo';
    document.body.insertBefore(skipToContent, document.body.firstChild);
    
    // Adicionar ID para o conteúdo principal
    const mainContent = document.querySelector('main');
    mainContent.id = 'main-content';
    
    // Detectar scroll para mudar estilo do navbar
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Marcar link ativo com base na URL atual
    const currentLocation = window.location.pathname;
    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        if (currentLocation.includes(linkPath) && linkPath !== '/') {
            link.classList.add('active');
        } else if (currentLocation === '/' && linkPath === '/') {
            link.classList.add('active');
        }
    });
});






/**
 * header.js - Funcionalidades do cabeçalho responsivo
 */

document.addEventListener('DOMContentLoaded', function() {
    // Não precisamos mais do menu mobile, pois todos os links estão sempre visíveis
    
    const navbarNav = document.querySelector('.navbar-nav');
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Adicionar "Skip to content" para acessibilidade
    const skipToContent = document.createElement('a');
    skipToContent.className = 'skip-to-content';
    skipToContent.href = '#main-content';
    skipToContent.textContent = 'Pular para o conteúdo';
    document.body.insertBefore(skipToContent, document.body.firstChild);
    
    // Adicionar ID para o conteúdo principal
    const mainContent = document.querySelector('main');
    mainContent.id = 'main-content';
    
    // Detectar scroll para mudar estilo do navbar
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Marcar link ativo com base na URL atual
    const currentLocation = window.location.pathname;
    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        if (currentLocation.includes(linkPath) && linkPath !== '/') {
            link.classList.add('active');
        } else if (currentLocation === '/' && linkPath === '/') {
            link.classList.add('active');
        }
    });
});