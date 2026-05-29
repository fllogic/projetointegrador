<?php
#-----Criador de senhas para introdução direta no banco de dados.
$senha = "123456";
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);
echo $senha_hash;