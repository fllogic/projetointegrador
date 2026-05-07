<?php
// ==========================================
// painel.php - Área Restrita e Logout
// ==========================================
require 'config.php';

// 1. DEFESA: VERIFICA EXPIRAÇÃO DE SESSÃO
verificarSessao();

// 2. DEFESA: CONTROLE DE ACESSO
// Se a variável 'usuario_id' não existir na sessão, o usuário não passou pelo login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// 3. DEFESA: LOGOUT SEGURO
if (isset($_GET['sair'])) {
    // Esvazia as variáveis
    session_unset();
    // Destrói o arquivo de sessão no servidor
    session_destroy();
    // Invalida o cookie de sessão no navegador do usuário
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><title>Painel Administrativo</title></head>
<body style="font-family: sans-serif; margin: 40px;">
    
    <div style="background-color: #2c3e50; color: white; padding: 15px;">
        <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h2>
        <p>Perfil de Acesso: <strong><?php echo htmlspecialchars($_SESSION['perfil']); ?></strong></p>
    </div>

    <div style="margin-top: 20px;">
        <h3>Menu do Sistema</h3>
        <ul>
            <li><a href="#">Ver dados sensíveis</a> (Simulação)</li>
            <li><a href="#">Relatórios</a> (Simulação)</li>
        </ul>

        <br><br>
        <!-- O link de logout passa um parâmetro via GET para acionar o bloco IF no topo -->
        <a href="?sair=1" style="background: #dc3545; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;">Sair do Sistema (Logout)</a>
    </div>

</body>
</html>