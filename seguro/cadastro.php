<?php
// ==========================================
// cadastro.php - Criação Segura de Usuários
// ==========================================
require 'config.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha_plana = $_POST['senha'];
    $perfil = 'padrao'; // Hardcoded para simplificar

    // 🚨 DEFESA: Gera o Hash seguro da senha usando Bcrypt
    $senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);

    try {
        // 🚨 DEFESA: Prepared Statement previne SQL Injection na inserção
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $senha_hash, $perfil);
        $stmt->execute();
        $stmt->close();
        
        $mensagem = "<p style='color:green'>Usuário criado com sucesso! <a href='login.php'>Faça login</a>.</p>";
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { // Código de erro do MySQL para entrada duplicada (UNIQUE)
            $mensagem = "<p style='color:red'>Este e-mail já está cadastrado.</p>";
        } else {
            $mensagem = "<p style='color:red'>Erro ao criar usuário.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><title>Cadastro Seguro</title></head>
<body style="font-family: sans-serif; margin: 40px;">
    <h2>Cadastro de Novo Usuário</h2>
    <?php echo $mensagem; ?>
    <form method="POST" action="">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>
        
        <label>E-mail:</label><br>
        <input type="email" name="email" required><br><br>
        
        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>
        
        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>