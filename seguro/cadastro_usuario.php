<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'admin') {
    die("Acesso Negado. Apenas administradores podem cadastrar usuários.");
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $perfil = $_POST['perfil'];

    // Verifica se e-mail é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "E-mail inválido.";
    } else {
        // Gera Hash seguro da senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $senha_hash, $perfil);
        
        if ($stmt->execute()) {
            $msg = "<span style='color:green;'>Usuário cadastrado com sucesso!</span>";
        } else {
            $msg = "<span style='color:red;'>Erro ao cadastrar. E-mail já existe?</span>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Cadastrar Usuário</title></head>
<body style="font-family: sans-serif; margin: 40px;">
    <h2>Cadastrar Novo Usuário (Restrito)</h2>
    <p><?php echo $msg; ?></p>
    <form method="POST">
        Nome: <input type="text" name="nome" required><br><br>
        E-mail: <input type="email" name="email" required><br><br>
        Senha: <input type="password" name="senha" required><br><br>
        Perfil: 
        <select name="perfil">
            <option value="user">Usuário Comum</option>
            <option value="admin">Administrador</option>
        </select><br><br>
        <button type="submit">Registrar</button>
    </form>
    <br><a href="portal.php">Voltar ao Portal</a>
</body>
</html>