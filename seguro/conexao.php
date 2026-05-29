<?php
$db_host = "localhost";
$db_user = "root"; 
$db_pass = "";
$db_name = "lab_seguro";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Falha na conexão."); // Sem revelar detalhes do erro (Information Disclosure)
}
?>