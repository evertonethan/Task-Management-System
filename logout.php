<?php
// logout.php
require_once 'config/config.php';

// Tentativa 1: Fazer requisição para a API de logout (preferível)
$api_success = false;

try {
    $url = API_URL . 'auth.php?action=logout';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5 segundos de timeout
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode >= 200 && $httpcode < 300) {
        $api_success = true;
    }
} catch (Exception $e) {
    // Falha na comunicação com a API, continuará com o método direto
    error_log("Erro ao comunicar com API de logout: " . $e->getMessage());
}

// Tentativa 2: Método direto (fallback)
// Destruir a sessão local
$_SESSION = array();

// Destruir o cookie da sessão se existir
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir a sessão
session_destroy();

// Limpar variáveis de sessão do localStorage via JavaScript
echo '<script>
    localStorage.removeItem("user_id");
    localStorage.removeItem("username");
    window.location.href = "' . BASE_URL . 'login.php?logout=success";
</script>';

// Caso o JavaScript não funcione, redirecionar via PHP
redirect('login.php?logout=success');
