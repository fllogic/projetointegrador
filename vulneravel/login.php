<?php
// informacao sql
$db_host = "localhost";
$db_user = "root"; 
$db_pass = "";
$db_name = "lab_seguranca";
// conexao sql
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // VULNERABILIDADE: Recebendo os dados sem qualquer tipo de filtro ou sanitização
    $usuario = $_POST["username"];
    $senha = $_POST["password"];

    // VULNERABILIDADE: Comparação direta de strings em texto plano
    $sql = "SELECT * FROM usuarios WHERE username = '$usuario' AND password = '$senha'";
    
    $resultado = $conn->query($sql);

    // Se retornar 1 ou mais linhas, o login é válido
    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        header("location: portal.php");
    } else { 
        // VULNERABILIDADE: O input do usuário é inserido diretamente na resposta HTML.
        echo ("Falha na autenticação. O usuário <b>" . $usuario . "</b> ou a senha estão incorretos.");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        .container { border: 1px solid #ccc; padding: 20px; max-width: 300px; }
        .erro { color: red; }
        .sucesso { color: green; }
    </style>
</head>
<body>

<div class="container">
    <h2>Acesso ao Sistema</h2>

    <!-- VULNERABILIDADE: O formulário envia dados em texto plano -->
    <form method="POST" action="">
        <label for="username">Usuário:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Senha:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Entrar">
    </form>
</div>

</body>
</html>