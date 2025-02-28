<?php
// debug.php - Use este arquivo para depurar a configuração
// Acesse através de http://seusite.com/debug.php

// Incluir configurações
require_once 'config/config.php';
require_once 'classes/Database.php';

// Função para verificar se um arquivo existe
function checkFile($path)
{
    if (file_exists($path)) {
        echo "<span style='color:green'>✓</span> Arquivo existe: $path<br>";
    } else {
        echo "<span style='color:red'>✗</span> Arquivo NÃO existe: $path<br>";
    }
}

// Função para verificar se um diretório existe
function checkDir($path)
{
    if (is_dir($path)) {
        echo "<span style='color:green'>✓</span> Diretório existe: $path<br>";
    } else {
        echo "<span style='color:red'>✗</span> Diretório NÃO existe: $path<br>";
    }
}

// Definir cabeçalho para texto plano
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depuração do Sistema</title>
    <style>
        body {
            font-family: monospace;
            line-height: 1.5;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .section {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        h2 {
            margin-top: 0;
            background: #f5f5f5;
            padding: 10px;
        }

        pre {
            background: #f9f9f9;
            padding: 10px;
            overflow: auto;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <h1>Depuração do Sistema de Gestão de Tarefas</h1>

    <div class="section">
        <h2>Verificação da Estrutura de Diretórios e Arquivos</h2>
        <?php
        // Verificar diretórios importantes
        checkDir('config');
        checkDir('api');
        checkDir('classes');
        checkDir('includes');

        // Verificar arquivos importantes
        checkFile('config/config.php');
        checkFile('config/db.php');
        checkFile('api/auth.php');
        checkFile('classes/Database.php');
        checkFile('classes/User.php');
        checkFile('login.php');
        ?>
    </div>

    <div class="section">
        <h2>Configurações do Sistema</h2>
        <pre>
APP_NAME: <?php echo defined('APP_NAME') ? APP_NAME : 'Não definido'; ?>

APP_VERSION: <?php echo defined('APP_VERSION') ? APP_VERSION : 'Não definido'; ?>

IS_DEVELOPMENT: <?php echo defined('IS_DEVELOPMENT') ? (IS_DEVELOPMENT ? 'true' : 'false') : 'Não definido'; ?>

BASE_URL: <?php echo defined('BASE_URL') ? BASE_URL : 'Não definido'; ?>

API_URL: <?php echo defined('API_URL') ? API_URL : 'Não definido'; ?>

DB_HOST: <?php echo defined('DB_HOST') ? DB_HOST : 'Não definido'; ?>

DB_NAME: <?php echo defined('DB_NAME') ? DB_NAME : 'Não definido'; ?>

Script Path: <?php echo $_SERVER['SCRIPT_NAME']; ?>

Diretório Base: <?php echo dirname(dirname($_SERVER['SCRIPT_NAME'])); ?>
        </pre>
    </div>

    <div class="section">
        <h2>Teste de Conectividade da API</h2>
        <div id="api-test-result">Testando...</div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const apiResult = document.getElementById('api-test-result');
                const apiUrl = '<?php echo API_URL; ?>auth.php';

                apiResult.innerHTML = `Tentando conexão com: ${apiUrl}...`;

                fetch(apiUrl, {
                        method: 'HEAD',
                        cache: 'no-cache'
                    })
                    .then(response => {
                        if (response.ok) {
                            apiResult.innerHTML = `<span style='color:green'>Sucesso!</span> API encontrada em ${apiUrl}`;
                        } else {
                            apiResult.innerHTML = `<span style='color:red'>Erro!</span> API retornou status ${response.status} em ${apiUrl}`;
                        }
                    })
                    .catch(error => {
                        apiResult.innerHTML = `<span style='color:red'>Erro!</span> Não foi possível conectar à API: ${error.message}`;
                    });
            });
        </script>
    </div>

    <div class="section">
        <h2>Teste de Conexão com o Banco de Dados</h2>
        <?php
        try {
            // Tentar criar uma instância do Database
            $db = Database::getInstance();
            echo "<span style='color:green'>✓</span> Conexão com o banco de dados estabelecida com sucesso!<br>";

            // Tentar executar uma consulta simples
            $result = $db->fetchAll("SHOW TABLES");
            echo "<span style='color:green'>✓</span> Consulta executada com sucesso.<br>";

            // Exibir tabelas encontradas
            echo "<strong>Tabelas no banco de dados:</strong><br>";
            if (count($result) > 0) {
                echo "<ul>";
                foreach ($result as $row) {
                    echo "<li>" . reset($row) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "Nenhuma tabela encontrada.";
            }
        } catch (Exception $e) {
            echo "<span style='color:red'>✗</span> Erro ao conectar com o banco de dados: " . $e->getMessage() . "<br>";
        }
        ?>
    </div>
</body>

</html>