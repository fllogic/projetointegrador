<?php
session_start();
require 'conexao.php';

if (isset($_SESSION['id_usuario'])) {
    // Log opcional de logout
    $email = $_SESSION['nome']; // ou email
    $ip = $_SERVER['REMOTE_ADDR'];
    $conn->query("INSERT INTO logs_acesso (email_tentado, ip_origem, status_login) VALUES ('$email', '$ip', 'Logout')");
}

// Apaga os dados
$_SESSION = array();

// Destrói a sessão
session_destroy();

header("Location: login.php");
exit;
?>