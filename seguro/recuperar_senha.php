<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['tentativas_recuperacao'])) {
    $_SESSION['tentativas_recuperacao'] = 0;
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['tentativas_recuperacao'] >= 3) {
        die("Limite de solicitações atingido.");
    }

    $email = trim($_POST['email']);
    $_SESSION['tentativas_recuperacao']++;

    // Verifica se o email existe (sem revelar ao usuário)
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        // Gera token (Aula 6 LPI)
        $token = bin2hex(random_bytes(32));
        date_default_timezone_set('America/Sao_Paulo');
        $expira = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        $stmt_token = $conn->prepare("INSERT INTO recuperacao_senha (email, token, data_expiracao) VALUES (?, ?, ?)");
        $stmt_token->bind_param("sss", $email, $token, $expira);
        $stmt_token->execute();

        // Para o laboratório, exibimos o link. Em produção, envia-se via função mail().
        $msg = "<span style='color:green;'>Simulação de E-mail: <br>Clique no link para resetar: <a href='reset.php?token=$token'>Resetar Senha</a></span>";
    } else {
        // Mensagem idêntica mesmo se falhar (prevenção de enumeração)
        $msg = "<span style='color:green;'>Se o e-mail constar em nossa base, um link de recuperação foi enviado.</span>";
    }
}
?>
<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; margin: 40px;">
    <h2>Recuperação de Senha</h2>
    <p><?php echo $msg; ?></p>
    <form method="POST">
        E-mail cadastrado: <input type="email" name="email" required>
        <button type="submit">Solicitar Link</button>
    </form>
    <br><a href="login.php">Voltar</a>
</body>
</html>