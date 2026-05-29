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
    <title>Painel Seguro - Clientes</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; font-size: 14px; }
        th { background-color: #2c3e50; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .acoes a { margin-right: 10px; text-decoration: none; color: blue; }
        .btn { display: inline-block; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-bottom: 20px;}
        .form-container { border: 1px solid #ccc; padding: 20px; max-width: 400px; background: #f9f9f9;}
        .form-container input, .form-container select { width: 100%; padding: 8px; margin: 5px 0 15px 0; }
        .status { font-weight: bold; }
        .concluido { color: green; } .andamento { color: orange; } .pendente { color: red; }
        .header-usuario { float: right; background: #eef; padding: 10px; border-radius: 5px; border: 1px solid #ccd; }
    </style>
</head>
<body>

    <div class="header-usuario">
        👤 <b><?php echo htmlspecialchars($_SESSION['nome']); ?></b> (<?php echo $_SESSION['perfil']; ?>)<br>
        <a href="logout.php" style="color: red; text-decoration: none; font-weight: bold;">Sair</a>
    </div>

    <h2>Gestão de Clientes: Assistência Técnica</h2>

    <?php if($isAdmin): ?>
        <p style="background: #e8f4f8; padding: 10px; border-left: 4px solid #17a2b8;">
             <b><a href="cadastro_usuario.php">Cadastrar Novos Usuários</a>
        </p>
    <?php endif; ?>

    <?php 
    // ==========================================
    // EXIBIÇÃO: LISTAR TODOS OS CLIENTES
    // ==========================================
    if ($acao == 'listar'): 
        $sql = "SELECT * FROM clientes_reparo";
        $resultado = $conn->query($sql);
    ?>
        <a href="?acao=adicionar" class="btn">Adicionar Novo Cliente</a>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Aparelho</th><th>Problema</th><th>Status</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultado && $resultado->num_rows > 0) {
                    while($row = $resultado->fetch_assoc()) {
                        
                        $status = $row["status_servico"];
                        $classe_status = ($status == "Concluído") ? "concluido" : (($status == "Em andamento" || $status == "Avaliação técnica") ? "andamento" : "pendente");

                        // htmlspecialchars em TODOS os campos para mitigar ataques de XSS (Cross-Site Scripting)
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["nome"]) . "</td>"; 
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["telefone"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["modelo_aparelho"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["problema_relatado"]) . "</td>";
                        echo "<td class='status " . $classe_status . "'>" . htmlspecialchars($status) . "</td>";
                        
                        echo "<td class='acoes'>";
                        echo "<a href='?acao=editar&id=" . $row["id"] . "'>✏️ Editar</a>";
                        echo "<a href='?acao=excluir&id=" . $row["id"] . "' onclick=\"return confirm('Tem certeza que deseja excluir o cliente ID ".$row["id"]."?')\">🗑️ Excluir</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Nenhum cliente encontrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>

    <?php 
    // ==========================================
    // EXIBIÇÃO: FORMULÁRIO DE ADICIONAR
    // ==========================================
    elseif ($acao == 'adicionar'): 
    ?>
        <div class="form-container">
            <h3>Adicionar Cliente</h3>
            <form action="?acao=salvar_novo" method="POST">
                <label>Nome:</label>
                <input type="text" name="nome" required>

                <label>E-mail:</label>
                <input type="email" name="email">

                <label>Telefone:</label>
                <input type="text" name="telefone">

                <label>Modelo do Aparelho:</label>
                <input type="text" name="modelo_aparelho" required>

                <label>Problema Relatado:</label>
                <input type="text" name="problema_relatado" required>

                <label>Status do Serviço:</label>
                <select name="status_servico">
                    <option value="Avaliação técnica">Avaliação técnica</option>
                    <option value="Orçamento enviado">Orçamento enviado</option>
                    <option value="Aguardando aprovação">Aguardando aprovação</option>
                    <option value="Aguardando peça">Aguardando peça</option>
                    <option value="Em andamento">Em andamento</option>
                    <option value="Concluído">Concluído</option>
                    <option value="Sem conserto">Sem conserto</option>
                </select>

                <input type="submit" value="Salvar Cliente" class="btn" style="width: 100%;">
                <a href="?acao=listar">Cancelar</a>
            </form>
        </div>

    <?php 
    // ==========================================
    // EXIBIÇÃO: FORMULÁRIO DE EDITAR
    // ==========================================
    elseif ($acao == 'editar' && isset($_GET['id'])): 
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if ($id) {
            // Prepared statement para leitura segura na edição
            $stmt = $conn->prepare("SELECT * FROM clientes_reparo WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $cliente = $resultado->fetch_assoc();
            $stmt->close();
        }

        if (isset($cliente) && $cliente):
    ?>
        <div class="form-container">
            <h3>Editar Cliente #<?php echo $cliente['id']; ?></h3>
            <form action="?acao=salvar_edicao" method="POST">
                
                <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">

                <label>Nome:</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>

                <label>E-mail:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>">

                <label>Telefone:</label>
                <input type="text" name="telefone" value="<?php echo htmlspecialchars($cliente['telefone']); ?>">

                <label>Modelo do Aparelho:</label>
                <input type="text" name="modelo_aparelho" value="<?php echo htmlspecialchars($cliente['modelo_aparelho']); ?>" required>

                <label>Problema Relatado:</label>
                <input type="text" name="problema_relatado" value="<?php echo htmlspecialchars($cliente['problema_relatado']); ?>" required>

                <label>Status do Serviço:</label>
                <select name="status_servico">
                    <option value="Avaliação técnica" <?php if($cliente['status_servico'] == 'Avaliação técnica') echo 'selected'; ?>>Avaliação técnica</option>
                    <option value="Orçamento enviado" <?php if($cliente['status_servico'] == 'Orçamento enviado') echo 'selected'; ?>>Orçamento enviado</option>
                    <option value="Aguardando aprovação" <?php if($cliente['status_servico'] == 'Aguardando aprovação') echo 'selected'; ?>>Aguardando aprovação</option>
                    <option value="Aguardando peça" <?php if($cliente['status_servico'] == 'Aguardando peça') echo 'selected'; ?>>Aguardando peça</option>
                    <option value="Em andamento" <?php if($cliente['status_servico'] == 'Em andamento') echo 'selected'; ?>>Em andamento</option>
                    <option value="Concluído" <?php if($cliente['status_servico'] == 'Concluído') echo 'selected'; ?>>Concluído</option>
                    <option value="Sem conserto" <?php if($cliente['status_servico'] == 'Sem conserto') echo 'selected'; ?>>Sem conserto</option>
                </select>

                <input type="submit" value="Atualizar Cliente" class="btn" style="width: 100%;">
                <a href="?acao=listar">Cancelar</a>
            </form>
        </div>
    <?php else: ?>
        <p style="color:red;">Cliente não encontrado.</p>
        <a href="?acao=listar">Voltar</a>
    <?php endif; endif; ?>

</body>
</html>