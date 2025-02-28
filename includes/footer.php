</div>
</main>

<!-- Seção de newsletter antes do rodapé (opcional) -->
<?php if (isset($showNewsletter) && $showNewsletter): ?>
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h3>Fique atualizado</h3>
                    <p>Receba dicas de produtividade e novidades do sistema diretamente no seu email.</p>
                </div>
                <form class="newsletter-form" id="newsletter-form">
                    <div class="form-group">
                        <input type="email" id="newsletter-email" placeholder="Seu melhor email" required>
                        <button type="submit" class="btn-newsletter">Inscrever-se <i class="fas fa-paper-plane"></i></button>
                    </div>
                    <div class="newsletter-message" id="newsletter-message"></div>
                </form>
            </div>
        </div>
    </section>
<?php endif; ?>

<footer>
    <div class="container">
        <div class="footer-content">
            <!-- Seção de informações da marca -->
            <div class="footer-brand">
                <div class="footer-logo">
                    <i class="fas fa-check-circle"></i>
                    <h2><?php echo APP_NAME; ?></h2>
                </div>
                <p class="footer-description">
                    Sistema de gestão de tarefas simples e eficiente para organizar seu dia a dia de forma produtiva.
                </p>
                <div class="footer-contact">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <p>contato@example.com</p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <p>(00) 12345-6789</p>
                    </div>
                </div>
            </div>

            <!-- Seção de links rápidos -->
            <div class="footer-links">
                <h3>Links Rápidos</h3>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>"><i class="fas fa-chevron-right"></i> Início</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="<?php echo BASE_URL; ?>tasks.php"><i class="fas fa-chevron-right"></i> Minhas Tarefas</a></li>
                        <li><a href="<?php echo BASE_URL; ?>profile.php"><i class="fas fa-chevron-right"></i> Meu Perfil</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>login.php"><i class="fas fa-chevron-right"></i> Entrar</a></li>
                        <li><a href="<?php echo BASE_URL; ?>register.php"><i class="fas fa-chevron-right"></i> Cadastrar</a></li>
                    <?php endif; ?>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Sobre Nós</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Políticas de Privacidade</a></li>
                </ul>
            </div>

            <!-- Seção de estatísticas (visível apenas para usuários logados) -->
            <?php if (isLoggedIn()): ?>
                <div class="footer-stats">
                    <h3>Suas Estatísticas</h3>
                    <div class="stats-container">
                        <?php
                        // Esta seção pode ser preenchida dinamicamente
                        // com estatísticas do usuário se você tiver os dados disponíveis
                        ?>
                        <div class="stat-item">
                            <span class="stat-icon"><i class="fas fa-tasks"></i></span>
                            <div class="stat-info">
                                <span class="stat-value">--</span>
                                <span class="stat-label">Tarefas Totais</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
                            <div class="stat-info">
                                <span class="stat-value">--</span>
                                <span class="stat-label">Tarefas Concluídas</span>
                            </div>
                        </div>
                        <a href="<?php echo BASE_URL; ?>tasks.php" class="btn-view-more">Ver mais <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Seção de incentivo para cadastro (visível apenas para não logados) -->
                <div class="footer-cta">
                    <h3>Comece a organizar suas tarefas</h3>
                    <p>Crie uma conta gratuitamente e comece a gerenciar suas tarefas de forma eficiente.</p>
                    <a href="<?php echo BASE_URL; ?>register.php" class="btn-register">Cadastre-se Agora <i class="fas fa-arrow-right"></i></a>
                </div>
            <?php endif; ?>

            <!-- Seção de redes sociais -->
            <div class="footer-social">
                <h3>Redes Sociais</h3>
                <p>Siga-nos nas redes sociais e fique por dentro das novidades.</p>
                <div class="social-icons">
                    <a href="#" title="Facebook" aria-label="Siga-nos no Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Twitter" aria-label="Siga-nos no Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="Instagram" aria-label="Siga-nos no Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="LinkedIn" aria-label="Siga-nos no LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>

        <!-- Linha de divisão -->
        <div class="footer-divider"></div>

        <!-- Área de copyright e links de suporte -->
        <div class="footer-bottom">
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> - Todos os direitos reservados</p>
            </div>
            <div class="footer-legal">
                <ul>
                    <li><a href="#">Termos de Uso</a></li>
                    <li><a href="#">Privacidade</a></li>
                    <li><a href="#">Suporte</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<!-- Botão de voltar ao topo -->
<a href="#" id="back-to-top" class="back-to-top" aria-label="Voltar ao topo da página">
    <i class="fas fa-arrow-up"></i>
</a>

<!-- Modal de cookie consent (opcional) -->
<div id="cookie-consent" class="cookie-consent">
    <div class="cookie-content">
        <p><i class="fas fa-cookie-bite"></i> Este site usa cookies para melhorar sua experiência. Ao continuar navegando, você concorda com nossa <a href="#">política de privacidade</a>.</p>
        <button id="accept-cookies" class="btn-accept">Aceitar</button>
    </div>
</div>

<!-- Scripts globais -->
<script>
    // Variáveis globais para uso nos scripts
    const BASE_URL = '<?php echo BASE_URL; ?>';
    const API_URL = '<?php echo API_URL; ?>';
    <?php if (isLoggedIn()): ?>
        const USER_ID = <?php echo $_SESSION['user_id']; ?>;
        const USERNAME = '<?php echo $_SESSION['username']; ?>';
    <?php endif; ?>
</script>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/footer.js"></script>

<?php if (isset($extraScripts)): ?>
    <?php foreach ($extraScripts as $script): ?>
        <script src="<?php echo BASE_URL . $script; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Scripts condicionais -->
<?php if (isset($includeDarkMode) && $includeDarkMode): ?>
    <script src="<?php echo BASE_URL; ?>assets/js/dark-mode.js"></script>
<?php endif; ?>

<script src="<?php echo BASE_URL; ?>assets/js/header.js"></script>
</body>

</html>