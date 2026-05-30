<?php
session_start();
require 'conexao.php';

// Limite de tentativas via Sessão 
if (!isset($_SESSION['tentativas'])) {
    $_SESSION['tentativas'] = 0;
}

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_SESSION['tentativas'] >= 3) {
        die("<h3 style='color:red;'>Sistema bloqueado. Muitas tentativas falhas. Tente novamente mais tarde.</h3>");
    }

    $email = trim($_POST["email"]);
    $senha = $_POST["password"];
    $ip = $_SERVER['REMOTE_ADDR'];

    // Uso de Prepared Statement 
    $stmt = $conn->prepare("SELECT id, nome, senha_hash, perfil, ativo FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // Validação com bcrypt
        if (password_verify($senha, $usuario['senha_hash']) && $usuario['ativo'] == 1) {
            
            // SUCESSO
            $_SESSION['tentativas'] = 0; // Reseta tentativas
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['perfil'] = $usuario['perfil'];
            
            // Controle de expiração (15 minutos)
            date_default_timezone_set('America/Sao_Paulo');
            $_SESSION['data_expiracao'] = date("Y-m-d H:i:s", strtotime("+15 minutes"));

            // Grava Log de Sucesso
            $log_stmt = $conn->prepare("INSERT INTO logs_acesso (email_tentado, ip_origem, status_login) VALUES (?, ?, 'Sucesso')");
            $log_stmt->bind_param("ss", $email, $ip);
            $log_stmt->execute();

            header("Location: portal.php");
            exit;
        }
    }
    
    // FALHA (Mensagem genérica para não confirmar existência do e-mail)
    $_SESSION['tentativas']++;
    
    // Grava Log de Falha
    $log_stmt = $conn->prepare("INSERT INTO logs_acesso (email_tentado, ip_origem, status_login) VALUES (?, ?, 'Falha')");
    $log_stmt->bind_param("ss", $email, $ip);
    $log_stmt->execute();

    $erro = "Falha na autenticação. E-mail ou senha incorretos.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center py-4">
    <main class="form-signin w-100 m-auto" style="max-width: 400px; margin-top: 10vh !important;">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0"> Acesso ao Sistema</h4>
            </div>
            <div class="card-body p-4">
                <?php if($erro) echo "<div class='alert alert-danger'>$erro</div>"; ?>
                
                <form method="POST" action="">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="nome@exemplo.com" required>
                        <label for="email">E-mail corporativo</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Senha" required>
                        <label for="password">Senha</label>
                    </div>
                    <button class="btn btn-primary w-100 py-2" type="submit">Autenticar</button>
                </form>
                <div class="text-center mt-3">
                    <a href="recuperar_senha.php" class="text-decoration-none small">Esqueci minha senha</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>