<?php
// index.php
require_once 'config/config.php';

// Redirecionar para a página de tarefas se estiver logado, ou login se não estiver
if (isLoggedIn()) {
    redirect('tasks.php');
} else {
    redirect('login.php');
}
