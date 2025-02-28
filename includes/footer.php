</main>

<footer class="footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos os direitos reservados.</p>
    </div>
</footer>

<!-- Scripts JavaScript -->
<script>
    // Configurações globais para JavaScript
    const BASE_URL = '<?php echo BASE_URL; ?>';
    const API_URL = '<?php echo API_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>js/main.js"></script>

<?php if (isset($extraScripts) && !empty($extraScripts)): ?>
    <?php foreach ($extraScripts as $script): ?>
        <script src="<?php echo BASE_URL . 'js/' . $script; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>

</html>