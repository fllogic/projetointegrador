<?php
// ==========================================
// login.php - Autenticação e Proteção
// ==========================================
require 'config.php';

$mensagem = '';
$max_tentativas = 5;
$tempo_bloqueio = 15; // minutos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $ip = $_SERVER['REMOTE_ADDR'];

    // 1. DEFESA: CHECAGEM DE FORÇA BRUTA (Rate Limiting)
    $stmt = $conn->prepare("SELECT tentativas, ultimo_erro FROM controle_bloqueio WHERE ip = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $resultado_bloqueio = $stmt->get_result();
    $bloqueio = $resultado_bloqueio->fetch_assoc();
    $stmt->close();

    if ($bloqueio && $bloqueio['tentativas'] >= $max_tentativas) {
        $minutos_passados = (time() - strtotime($bloqueio['ultimo_erro'])) / 60;
        if ($minutos_passados < $tempo_bloqueio) {
            die("Múltiplas tentativas falhas. O IP $ip está bloqueado por $tempo_bloqueio minutos.");
        } else {
            // Tempo expirou, libera o IP
            $stmt = $conn->prepare("DELETE FROM controle_bloqueio WHERE ip = ?");
            $stmt->bind_param("s", $ip);
            $stmt->execute();
            $stmt->close();
            $bloqueio = null; 
        }
    }

    // 2. DEFESA: BUSCA O USUÁRIO (Prepared Statement)
    $stmt = $conn->prepare("SELECT id, nome, senha_hash, perfil FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado_user = $stmt->get_result();
    $usuario = $resultado_user->fetch_assoc();
    $stmt->close();

    // 3. DEFESA: VALIDAÇÃO DO HASH E MENSAGEM GENÉRICA
    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        // SUCESSO: Renova o ID da sessão para evitar Session Hijacking
        session_regenerate_id(true); 
        
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['perfil'] = $usuario['perfil'];
        $_SESSION['ultima_atividade'] = time();

        // Limpa registro de falhas deste IP
        $stmt = $conn->prepare("DELETE FROM controle_bloqueio WHERE ip = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $stmt->close();

        registrarAuditoria($conn, $email, 'Sucesso');
        header("Location: painel.php");
        exit;
    } else {
        // FALHA: Mensagem genérica impede Enumeração de Usuários
        $mensagem = "Credenciais inválidas.";
        registrarAuditoria($conn, $email, 'Falha');

        // Incrementa ou cria o bloqueio do IP
        if ($bloqueio) {
            $stmt = $conn->prepare("UPDATE controle_bloqueio SET tentativas = tentativas + 1 WHERE ip = ?");
            $stmt->bind_param("s", $ip);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO controle_bloqueio (ip) VALUES (?)");
            $stmt->bind_param("s", $ip);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Verifica se foi redirecionado por timeout
if (isset($_GET['erro']) && $_GET['erro'] == 'timeout') {
    $mensagem = "Sua sessão expirou por inatividade.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><title>Login Seguro</title></head>
<body style="font-family: sans-serif; margin: 40px;">
    <h2>Acesso ao Sistema</h2>
    <?php if ($mensagem) echo "<p style='color:red;'>$mensagem</p>"; ?>
    <form method="POST" action="">
        <label>E-mail:</label><br>
        <input type="email" name="email" required><br><br>
        
        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>
        
        <input type="submit" value="Entrar">
    </form>
    <br>
    <a href="cadastro.php">Criar nova conta</a>
</body>
</html>