<?php
session_start();
require 'conexao.php';

// Captura o token da URL
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$erro = "";
$sucesso = "";

// 1. VALIDAÇÃO PRIMÁRIA DO TOKEN
if (empty($token)) {
    die("<div class='container mt-5 text-center' style='font-family: sans-serif;'>
            <h3 class='text-danger'>Acesso Negado.</h3>
            <p>Nenhum token de segurança foi fornecido.</p>
         </div>");
}

// 2. VERIFICAÇÃO DE VALIDADE NO BANCO DE DADOS
// O token precisa existir, o prazo não pode ter expirado e o status 'usado' deve ser 0.
$stmt = $conn->prepare("SELECT email FROM recuperacao_senha WHERE token = ? AND usado = 0 AND data_expiracao > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("<div class='container mt-5 text-center' style='font-family: sans-serif;'>
            <h3 class='text-danger'>Link Inválido ou Expirado.</h3>
            <p>O link de recuperação que você acessou não é mais válido. Por favor, solicite um novo.</p>
            <a href='recuperar_senha.php' class='btn btn-primary mt-3'>Solicitar Novo Link</a>
         </div>");
}

// Token válido: pegamos o e-mail atrelado a ele
$linha = $resultado->fetch_assoc();
$email_usuario = $linha['email'];
$stmt->close();

// 3. PROCESSAMENTO DO FORMULÁRIO DE NOVA SENHA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];

    // Validações básicas da nova senha
    if (strlen($nova_senha) < 6) {
        $erro = "A senha deve conter pelo menos 6 caracteres por segurança.";
    } elseif ($nova_senha !== $confirma_senha) {
        $erro = "As senhas digitadas não coincidem.";
    } else {
        // Criptografa a nova senha
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Atualiza a senha do usuário
        $stmt_update = $conn->prepare("UPDATE usuarios SET senha_hash = ? WHERE email = ?");
        $stmt_update->bind_param("ss", $senha_hash, $email_usuario);
        $stmt_update->execute();
        $stmt_update->close();

        // Invalida o token para não ser usado novamente (Prevenção de Replay Attack)
        $stmt_invalida = $conn->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE token = ?");
        $stmt_invalida->bind_param("s", $token);
        $stmt_invalida->execute();
        $stmt_invalida->close();

        // Grava no log que houve uma redefinição de senha
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt_log = $conn->prepare("INSERT INTO logs_acesso (email_tentado, ip_origem, status_login) VALUES (?, ?, 'Senha Redefinida')");
        $stmt_log->bind_param("ss", $email_usuario, $ip);
        $stmt_log->execute();
        $stmt_log->close();

        $sucesso = "Sua senha foi redefinida com sucesso! Você será redirecionado para o login.";
        
        // Redireciona após 3 segundos
        header("refresh:3;url=login.php");
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Nova Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center py-4">
    
    <main class="form-signin w-100 m-auto" style="max-width: 400px; margin-top: 10vh !important;">
        <div class="card shadow border-0">
            <div class="card-header bg-success text-white text-center py-3">
                <h5 class="mb-0">🔒 Definir Nova Senha</h5>
            </div>
            <div class="card-body p-4">
                
                <p class="text-muted text-center mb-4 small">
                    Autenticado como: <strong><?php echo htmlspecialchars($email_usuario); ?></strong><br>
                    Crie uma nova credencial para acessar o sistema.
                </p>

                <?php if($erro): ?>
                    <div class="alert alert-danger text-center p-2" role="alert">
                        <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

                <?php if($sucesso): ?>
                    <div class="alert alert-success text-center p-2" role="alert">
                        <?php echo $sucesso; ?>
                    </div>
                    <div class="text-center">
                        <div class="spinner-border text-success spinner-border-sm" role="status"></div>
                        <span class="ms-2">Redirecionando...</span>
                    </div>
                <?php else: ?>
                    
                    <form method="POST" action="">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="nova_senha" name="nova_senha" placeholder="Nova Senha" required>
                            <label for="nova_senha">Nova Senha</label>
                        </div>
                        
                        <div class="form-floating mb-4">
                            <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" placeholder="Confirme a Senha" required>
                            <label for="confirma_senha">Confirme a Senha</label>
                        </div>
                        
                        <button class="btn btn-success w-100 py-2 mb-3" type="submit">Redefinir Senha</button>
                    </form>

                <?php endif; ?>

            </div>
        </div>
    </main>

</body>
</html>