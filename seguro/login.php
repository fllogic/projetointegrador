<?php
session_start();

$db_host = "localhost";
$db_user = "root"; 
$db_pass = "";
$db_name = "lab_seguranca_seguro";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["username"];
    $senha = $_POST["password"];

    // 1. Buscamos APENAS pelo username usando Prepared Statement
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    
    $resultado = $stmt->get_result();
    $erro = false;

    // 2. Se o usuário existir, verificamos o Hash da senha
    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        
        // CORREÇÃO: Compara a senha digitada em texto plano com o Hash do banco
        if (password_verify($senha, $row['password'])) {
            // Senha correta, inicia a sessão
            $_SESSION['logado'] = true;
            $_SESSION['usuario'] = $row['username'];
            header("location: portal.php");
            exit;
        } else {
            // Senha incorreta
            $erro = true;
        }
    } else { 
        // Usuário não existe
        $erro = true;
    }

    // 3. Exibe mensagem de erro genérica (boa prática para não revelar se o erro foi no usuário ou na senha)
    if ($erro) {
        echo ("<div style='color:red; margin: 40px 40px 0 40px;'>Falha na autenticação. O usuário <b>" . htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') . "</b> ou a senha estão incorretos.</div>");
    }
    
    $stmt->close();
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
    </style>
</head>
<body>

<div class="container">
    <h2>Acesso ao Sistema</h2>
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