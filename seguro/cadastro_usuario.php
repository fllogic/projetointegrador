<?php
session_start();
require 'conexao.php';

// Controle de Acesso Restrito (Apenas Admin)
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'admin') {
    die("<div style='font-family: sans-serif; text-align: center; margin-top: 50px; color: red;'>
            <h3>Acesso Negado.</h3><p>Apenas administradores podem cadastrar usuários.</p>
            <a href='login.php'>Voltar ao Login</a>
         </div>");
}

$msg = "";
$msgType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $perfil = $_POST['perfil'];

    // Verifica se e-mail é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Formato de e-mail inválido.";
        $msgType = "danger";
    } else {
        // Gera Hash seguro da senha com Bcrypt
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Prepared Statement para inserção segura
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $senha_hash, $perfil);
        
        if ($stmt->execute()) {
            $msg = "Usuário cadastrado com sucesso e liberado para acesso.";
            $msgType = "success";
        } else {
            $msg = "Erro ao cadastrar. Verifique se o e-mail já existe na base de dados.";
            $msgType = "danger";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="portal.php">Cadastro de Usuários</a>
    <div class="d-flex align-items-center text-white">
        <span class="me-3">👤 <b><?php echo htmlspecialchars($_SESSION['nome']); ?></b> (<?php echo $_SESSION['perfil']; ?>)</span>
        <a href="logout.php" class="btn btn-sm btn-outline-light">Sair</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h4 class="text-primary"><i class="bi bi-person-plus"></i> Registrar Novo Usuário</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if($msg): ?>
                        <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $msg; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" required placeholder="Ex: João da Silva">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">E-mail Corporativo</label>
                            <input type="email" name="email" class="form-control" required placeholder="joao@empresa.com">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Senha de Acesso</label>
                            <input type="password" name="senha" class="form-control" required placeholder="Defina uma senha forte">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Perfil de Autorização (RBAC)</label>
                            <select name="perfil" class="form-select">
                                <option value="user">Usuário Comum (Leitura/Edição de Dados)</option>
                                <option value="admin">Administrador (Controle Total do Sistema)</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2">Registrar Usuário</button>
                            <a href="portal.php" class="btn btn-outline-secondary py-2">Voltar ao Portal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>