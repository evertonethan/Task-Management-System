// assets/js/footer.js

document.addEventListener('DOMContentLoaded', function() {
    // Adicionar seções ao footer
    const footer = document.querySelector('footer');
    const container = footer.querySelector('.container');
    
    // Verificar se o footer já tem conteúdo (além do copyright)
    if (container.children.length <= 1) {
        // Limpar o container
        container.innerHTML = '';
        
        // Adicionar seção de marca
        const brandSection = document.createElement('div');
        brandSection.classList.add('footer-brand');
        brandSection.innerHTML = `
            <h2 class="footer-logo">${APP_NAME}</h2>
            <p class="footer-description">Sistema de gestão de tarefas simples e eficiente para organizar seu dia a dia de forma produtiva.</p>
            
            <div class="footer-contact">
                <div class="contact-info">
                    <i class="fas fa-envelope"></i>
                    <p>contato@example.com</p>
                </div>
                <div class="contact-info">
                    <i class="fas fa-phone"></i>
                    <p>(00) 12345-6789</p>
                </div>
            </div>
        `;
        container.appendChild(brandSection);
        
        // Adicionar seção de links
        const linksSection = document.createElement('div');
        linksSection.classList.add('footer-links');
        linksSection.innerHTML = `
            <h3>Links Rápidos</h3>
            <ul>
                <li><a href="${BASE_URL}"><i class="fas fa-chevron-right"></i> Início</a></li>
                <li><a href="${BASE_URL}tasks.php"><i class="fas fa-chevron-right"></i> Minhas Tarefas</a></li>
                <li><a href="${BASE_URL}profile.php"><i class="fas fa-chevron-right"></i> Meu Perfil</a></li>
                <li><a href="#"><i class="fas fa-chevron-right"></i> Suporte</a></li>
                <li><a href="#"><i class="fas fa-chevron-right"></i> Termos de Uso</a></li>
            </ul>
        `;
        container.appendChild(linksSection);
        
        // Adicionar seção de social
        const socialSection = document.createElement('div');
        socialSection.classList.add('footer-social');
        socialSection.innerHTML = `
            <h3>Redes Sociais</h3>
            <p>Siga-nos nas redes sociais e fique por dentro das novidades.</p>
            <div class="social-icons">
                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        `;
        container.appendChild(socialSection);
        
        // Adicionar copyright na parte inferior
        const bottomSection = document.createElement('div');
        bottomSection.classList.add('footer-bottom');
        bottomSection.innerHTML = `
            <p>&copy; ${new Date().getFullYear()} ${APP_NAME} - Todos os direitos reservados</p>
        `;
        container.appendChild(bottomSection);
        
        // Marcar o footer como estendido
        footer.classList.add('extended');
    }
    
    // Adicionar botão de voltar ao topo
    const backToTop = document.createElement('a');
    backToTop.href = '#';
    backToTop.classList.add('back-to-top');
    backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
    document.body.appendChild(backToTop);
    
    // Mostrar/esconder botão de voltar ao topo
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });
    
    // Funcionalidade do botão de voltar ao topo
    backToTop.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Verificar se é uma página simples
    if (document.body.classList.contains('simple-page')) {
        footer.classList.add('simple');
        footer.classList.remove('extended');
    }
});