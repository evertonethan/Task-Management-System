<?php
// check_connection.php
// Arquivo para verificar e diagnosticar problemas de conexão

// Configurar cabeçalhos para evitar cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

// Exibir versão do PHP
echo '<h2>Informações do Sistema</h2>';
echo '<p><strong>Versão PHP:</strong> ' . phpversion() . '</p>';

// Verificar extensões necessárias
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'session'];
$missing_extensions = [];

echo '<h3>Verificação de Extensões PHP</h3>';
echo '<ul>';
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo '<li style="color: green;">✓ ' . $ext . ' - OK</li>';
    } else {
        echo '<li style="color: red;">✗ ' . $ext . ' - FALTANDO</li>';
        $missing_extensions[] = $ext;
    }
}
echo '</ul>';

if ($missing_extensions) {
    echo '<p style="color: red;"><strong>ALERTA:</strong> Você precisa habilitar as extensões faltantes no seu php.ini.</p>';
}

// Tentar conectar ao banco de dados
echo '<h3>Teste de Conexão com Banco de Dados</h3>';

// Incluir arquivo de configuração apenas se existir
if (file_exists('config/db.php') && file_exists('config/config.php')) {
    require_once 'config/config.php';

    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        echo '<p style="color: green;">✓ Conexão com o banco de dados bem-sucedida!</p>';

        // Verificar tabelas
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        echo '<p><strong>Tabelas encontradas:</strong> ' . implode(', ', $tables) . '</p>';

        // Verificar usuários
        $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo '<p><strong>Número de usuários:</strong> ' . $userCount . '</p>';
    } catch (PDOException $e) {
        echo '<p style="color: red;">✗ Erro na conexão com o banco de dados: ' . $e->getMessage() . '</p>';

        echo '<h4>Sugestões de solução:</h4>';
        echo '<ul>';
        echo '<li>Verifique se o servidor MySQL está em execução</li>';
        echo '<li>Verifique se as credenciais em config/db.php estão corretas</li>';
        echo '<li>Verifique se o banco de dados "' . DB_NAME . '" existe</li>';
        echo '<li>Certifique-se de que o usuário "' . DB_USER . '" tem permissões para acessar o banco</li>';
        echo '</ul>';
    }
} else {
    echo '<p style="color: red;">✗ Arquivos de configuração não encontrados. Verifique se config/db.php e config/config.php existem.</p>';
}

// Verificar configuração do servidor
echo '<h3>Configuração do Servidor</h3>';
echo '<p><strong>SERVER_SOFTWARE:</strong> ' . $_SERVER['SERVER_SOFTWARE'] . '</p>';
echo '<p><strong>DOCUMENT_ROOT:</strong> ' . $_SERVER['DOCUMENT_ROOT'] . '</p>';
echo '<p><strong>Diretório atual:</strong> ' . dirname(__FILE__) . '</p>';

// Verificar permissões de arquivos
echo '<h3>Permissões de Arquivos</h3>';
$key_dirs = ['config', 'classes', 'api', 'includes'];
echo '<ul>';
foreach ($key_dirs as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? 'Gravável' : 'Somente leitura';
        $perm = substr(sprintf('%o', fileperms($dir)), -4);
        echo '<li>' . $dir . ' - ' . $writable . ' (Permissões: ' . $perm . ')</li>';
    } else {
        echo '<li style="color: red;">' . $dir . ' - Diretório não encontrado</li>';
    }
}
echo '</ul>';

// Verificar URLs configuradas
if (defined('BASE_URL') && defined('API_URL')) {
    echo '<h3>URLs Configuradas</h3>';
    echo '<p><strong>BASE_URL:</strong> ' . BASE_URL . '</p>';
    echo '<p><strong>API_URL:</strong> ' . API_URL . '</p>';

    // Verificar ambiente
    echo '<p><strong>Ambiente:</strong> ' . (defined('IS_PRODUCTION') && IS_PRODUCTION ? 'Produção' : 'Desenvolvimento') . '</p>';
} else {
    echo '<p style="color: red;">Constantes BASE_URL e API_URL não definidas.</p>';
}

// Verificar .htaccess
echo '<h3>Status do .htaccess</h3>';
if (file_exists('.htaccess')) {
    echo '<p style="color: green;">✓ Arquivo .htaccess encontrado</p>';
} else {
    echo '<p style="color: red;">✗ Arquivo .htaccess não encontrado!</p>';
}

// Verificar mod_rewrite (indireto)
echo '<h3>Teste de mod_rewrite</h3>';
echo '<p>Para testar se o mod_rewrite está funcionando, tente acessar uma URL sem a extensão .php, como <a href="login" target="_blank">login</a> (em vez de login.php).</p>';

// Diagnóstico de erro comum
echo '<h2>Diagnóstico de "Erro ao conectar com o servidor"</h2>';
echo '<p>Este erro geralmente ocorre devido a um dos seguintes problemas:</p>';
echo '<ol>';
echo '<li>APIs incorretamente configuradas em config/config.php</li>';
echo '<li>Problemas de CORS (Cross-Origin Resource Sharing)</li>';
echo '<li>Servidor não está processando solicitações PHP corretamente</li>';
echo '<li>mod_rewrite não está habilitado ou configurado corretamente</li>';
echo '<li>Problemas de conexão com o banco de dados</li>';
echo '</ol>';

echo '<h3>Teste das APIs</h3>';
echo '<p>Faça uma solicitação direta para as APIs para verificar se elas respondem:</p>';
echo '<ul>';
echo '<li><a href="api/auth.php" target="_blank">api/auth.php</a> - Deve retornar uma mensagem de erro de método não permitido (o que significa que está funcionando)</li>';
echo '<li><a href="api/tasks.php" target="_blank">api/tasks.php</a> - Deve retornar uma mensagem de não autenticado (o que significa que está funcionando)</li>';
echo '</ul>';

echo '<hr>';
echo '<p><strong>Gerado em:</strong> ' . date('Y-m-d H:i:s') . '</p>';
