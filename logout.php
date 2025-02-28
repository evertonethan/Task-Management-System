<?php
// logout.php
require_once 'config/config.php';

// Destroi a sessão
session_start();
$_SESSION = array();
session_destroy();

// Redireciona para a página de login
redirect('login.php');
