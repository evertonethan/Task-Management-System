</div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> - Todos os direitos reservados</p>
    </div>
</footer>

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

<?php if (isset($extraScripts)): ?>
    <?php foreach ($extraScripts as $script): ?>
        <script src="<?php echo BASE_URL . $script; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<script src="<?php echo BASE_URL; ?>assets/js/header.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/footer.js"></script>
</body>

</html>