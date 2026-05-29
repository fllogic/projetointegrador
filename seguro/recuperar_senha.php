<?php
session_start();
require 'conexao.php';

// Inicializa controle de taxa (Rate Limiting)
if (!isset($_SESSION['tentativas_recuperacao'])) {
    $_SESSION['tentativas_recuperacao'] = 0;
}

$msg = "";
$msgType = "info";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bloqueio após 3 tentativas para evitar SPAM e abuso
    if ($_SESSION['tentativas_recuperacao'] >= 3) {
        die("<div style='font-family: sans-serif; text-align: center; margin-top: 50px; color: red;'>
                <h3>Limite de solicitações atingido.</h3>
                <p>Por questões de segurança, aguarde antes de tentar novamente.</p>
             </div>");
    }

    $email = trim($_POST['email']);
    $_SESSION['tentativas_recuperacao']++;

    // Verifica se o email existe usando Prepared Statements
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        // Gera token criptograficamente seguro
        $token = bin2hex(random_bytes(32));
        date_default_timezone_set('America/Sao_Paulo');
        $expira = date("Y-m-d H:i:s", strtotime("+30 minutes")); // Token válido por 30 min

        $stmt_token = $conn->prepare("INSERT INTO recuperacao_senha (email, token, data_expiracao) VALUES (?, ?, ?)");
        $stmt_token->bind_param("sss", $email, $token, $expira);
        $stmt_token->execute();

        // Para o laboratório, exibimos o link na tela simulando a caixa de entrada do e-mail.
        $msg = "<strong>[SIMULAÇÃO DE CAIXA DE E-MAIL]</strong><br> Um link de recuperação foi gerado: <br>$token<br> 
                <a href='reset.php?token=$token' class='btn btn-sm btn-success mt-2'>Criar Nova Senha</a>";
        $msgType = "success";
    } else {
        // Prevenção de Enumeração de E-mail: A mensagem de sucesso genérica é exibida mesmo se o e-mail não existir.
        $msg = "Se o e-mail informado constar em nossa base de dados, um link de recuperação foi enviado para a sua caixa de entrada.";
        $msgType = "success";
    }
    
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recuperação de Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center py-4">
    
    <main class="form-signin w-100 m-auto" style="max-width: 450px; margin-top: 10vh !important;">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white text-center py-3">
                <h5 class="mb-0"> Recuperação de Senha</h5>
            </div>
            <div class="card-body p-4">
                
                <p class="text-muted text-center mb-4">
                    Informe seu e-mail. Enviaremos um link temporário para que você possa redefinir sua credencial com segurança.
                </p>

                <?php if($msg): ?>
                    <div class="alert alert-<?php echo $msgType; ?> text-center" role="alert">
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="nome@exemplo.com" required>
                        <label for="email">E-mail corporativo</label>
                    </div>
                    
                    <button class="btn btn-primary w-100 py-2 mb-3" type="submit">Solicitar Link de Recuperação</button>
                    
                    <div class="text-center">
                        <a href="login.php" class="text-decoration-none text-secondary">Voltar para a tela de Login</a>
                    </div>
                </form>

            </div>
        </div>
    </main>

</body>
</html>