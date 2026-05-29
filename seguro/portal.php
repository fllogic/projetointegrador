<?php
session_start();
require 'conexao.php';

// ==========================================
// CONTROLE DE SESSÃO E EXPIRAÇÃO (Segurança)
// ==========================================
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

date_default_timezone_set('America/Sao_Paulo');
if (strtotime($_SESSION['data_expiracao']) < time()) {
    session_destroy();
    die("<h3 style='color:red; font-family: sans-serif; text-align: center; margin-top: 50px;'>Sua sessão expirou por inatividade. <a href='login.php'>Faça login novamente.</a></h3>");
}

// Renova o tempo da sessão a cada interação
$_SESSION['data_expiracao'] = date("Y-m-d H:i:s", strtotime("+15 minutes"));

$isAdmin = ($_SESSION['perfil'] === 'admin');
$acao = isset($_GET['acao']) ? $_GET['acao'] : 'listar';

// ==========================================
// LÓGICA DE PROCESSAMENTO SEGURA (CRUD)
// ==========================================

// 1. EXCLUIR
if ($acao == 'excluir' && isset($_GET['id'])) {
    // Filtra garantindo que o ID seja um número inteiro
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if ($id) {
        // Prepared Statement: O banco trata o '?' estritamente como dado, nunca como comando SQL
        $stmt = $conn->prepare("DELETE FROM clientes_reparo WHERE id = ?");
        $stmt->bind_param("i", $id); // "i" indica Integer
        $stmt->execute();
        $stmt->close();
    }
    header("Location: portal.php"); 
    exit;
}

// 2. SALVAR NOVO
if ($acao == 'salvar_novo' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitização básica removendo espaços extras
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $modelo = trim($_POST['modelo_aparelho']);
    $problema = trim($_POST['problema_relatado']);
    $status = trim($_POST['status_servico']);

    // Prepared Statement
    $stmt = $conn->prepare("INSERT INTO clientes_reparo (nome, email, telefone, modelo_aparelho, problema_relatado, status_servico) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nome, $email, $telefone, $modelo, $problema, $status); // "ssssss" indica 6 Strings
    $stmt->execute();
    $stmt->close();
    
    header("Location: portal.php");
    exit;
}

// 3. SALVAR EDIÇÃO
if ($acao == 'salvar_edicao' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    
    if ($id) {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $telefone = trim($_POST['telefone']);
        $modelo = trim($_POST['modelo_aparelho']);
        $problema = trim($_POST['problema_relatado']);
        $status = trim($_POST['status_servico']);

        // Prepared Statement
        $stmt = $conn->prepare("UPDATE clientes_reparo SET nome=?, email=?, telefone=?, modelo_aparelho=?, problema_relatado=?, status_servico=? WHERE id=?");
        $stmt->bind_param("ssssssi", $nome, $email, $telefone, $modelo, $problema, $status, $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: portal.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="portal.php">Portal Corporativo</a>
    <div class="d-flex align-items-center text-white">
        <span class="me-3">👤 <b><?php echo htmlspecialchars($_SESSION['nome']); ?></b> (<?php echo $_SESSION['perfil']; ?>)</span>
        <a href="logout.php" class="btn btn-sm btn-outline-light">Sair</a>
    </div>
  </div>
</nav>

<div class="container bg-white p-4 shadow-sm rounded">
    <h2 class="mb-4 border-bottom pb-2 text-primary">Gestão de Clientes: Assistência Técnica</h2>

    <?php if($isAdmin): ?>
        <div class="alert alert-info d-flex align-items-center">
            <span class="me-2"></span> 
            <div><a href="cadastro_usuario.php" class="alert-link">Cadastrar Novos Usuários</a>.</div>
        </div>
    <?php endif; ?>

    <?php if ($acao == 'listar'): ?>
        <a href="?acao=adicionar" class="btn btn-primary mb-3">Novo Cliente</a>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle border">
                <thead class="table-light">
                    <tr>
                        <th>ID</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Aparelho</th><th>Problema</th><th>Status</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM clientes_reparo";
                    $resultado = $conn->query($sql);
                    if ($resultado && $resultado->num_rows > 0) {
                        while($row = $resultado->fetch_assoc()) {
                            // Definindo a cor do badge com base no status
                            $status = $row["status_servico"];
                            $badge = "bg-secondary";
                            if($status == "Concluído") $badge = "bg-success";
                            elseif($status == "Em andamento" || $status == "Avaliação técnica") $badge = "bg-warning text-dark";
                            elseif($status == "Sem conserto") $badge = "bg-danger";

                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . htmlspecialchars($row["nome"]) . "</td>"; 
                            echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["telefone"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["modelo_aparelho"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["problema_relatado"]) . "</td>";
                            echo "<td><span class='badge $badge'>" . htmlspecialchars($status) . "</span></td>";
                            
                            echo "<td>
                                    <a href='?acao=editar&id=" . $row["id"] . "' class='btn btn-sm btn-primary'>Editar</a>";
                            if($isAdmin) {
                                echo " <a href='?acao=excluir&id=" . $row["id"] . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Confirma a exclusão?')\">Excluir</a>";
                            }
                            echo "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>Nenhum cliente.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($acao == 'adicionar'): ?>
        <div class="card shadow-sm border-0" style="max-width: 600px;">
            <div class="card-body">
                <h5 class="card-title text-primary mb-3">Registrar Cliente</h5>
                <form action="?acao=salvar_novo" method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3"><label class="form-label">Nome</label><input type="text" name="nome" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">E-mail</label><input type="email" name="email" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Telefone</label><input type="text" name="telefone" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Aparelho</label><input type="text" name="modelo_aparelho" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Status</label>
                            <select name="status_servico" class="form-select">
                                <option>Avaliação técnica</option><option>Orçamento enviado</option>
                                <option>Aguardando aprovação</option><option>Aguardando peça</option>
                                <option>Em andamento</option><option>Concluído</option><option>Sem conserto</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3"><label class="form-label">Problema Relatado</label><textarea name="problema_relatado" class="form-control" rows="2" required></textarea></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Registro</button>
                    <a href="?acao=listar" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>