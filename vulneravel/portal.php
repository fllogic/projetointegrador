<?php
// Configurações do banco de dados 
$db_host = "localhost";
$db_user = "root"; 
$db_pass = "";     
$db_name = "lab_seguranca";

// Criando a conexão
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Verifica qual ação o usuário quer realizar (listar, adicionar, editar, excluir)
// Se não houver ação na URL, o padrão é 'listar'
$acao = isset($_GET['acao']) ? $_GET['acao'] : 'listar';

// ==========================================
// LÓGICA DE PROCESSAMENTO (CRUD)
// ==========================================

// 1. EXCLUIR: Apaga o registro do banco
if ($acao == 'excluir' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // 🚨 VULNERABILIDADE (SQLi): O ID recebido via GET vai direto para a query
    $sql = "DELETE FROM clientes_reparo WHERE id = $id";
    $conn->query($sql);
    header("Location: portal.php"); // Redireciona de volta para a lista
    exit;
}

// 2. SALVAR NOVO: Insere os dados do formulário de adição
if ($acao == 'salvar_novo' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $modelo = $_POST['modelo_aparelho'];
    $problema = $_POST['problema_relatado'];
    $status = $_POST['status_servico'];

    // 🚨 VULNERABILIDADE (SQLi): Variáveis inseridas diretamente
    $sql = "INSERT INTO clientes_reparo (nome, email, telefone, modelo_aparelho, problema_relatado, status_servico) 
            VALUES ('$nome', '$email', '$telefone', '$modelo', '$problema', '$status')";
    $conn->query($sql);
    header("Location: portal.php");
    exit;
}

// 3. SALVAR EDIÇÃO: Atualiza os dados de um cliente existente
if ($acao == 'salvar_edicao' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $modelo = $_POST['modelo_aparelho'];
    $problema = $_POST['problema_relatado'];
    $status = $_POST['status_servico'];

    // 🚨 VULNERABILIDADE (SQLi): Variáveis inseridas diretamente
    $sql = "UPDATE clientes_reparo SET 
            nome='$nome', email='$email', telefone='$telefone', 
            modelo_aparelho='$modelo', problema_relatado='$problema', status_servico='$status' 
            WHERE id=$id";
    $conn->query($sql);
    header("Location: portal.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - Clientes</title>
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
    </style>
</head>
<body>

    <h2>Gestão de Clientes: Assistência Técnica</h2>

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

                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["nome"]) . "</td>"; 
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["telefone"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["modelo_aparelho"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["problema_relatado"]) . "</td>";
                        echo "<td class='status " . $classe_status . "'>" . htmlspecialchars($status) . "</td>";
                        
                        // Botões de Ação na tabela
                        echo "<td class='acoes'>";
                        echo "<a href='?acao=editar&id=" . $row["id"] . "'>✏️ Editar</a>";
                        // Um alerta simples em JavaScript para evitar exclusões acidentais
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
        $id = $_GET['id'];
        $sql = "SELECT * FROM clientes_reparo WHERE id = $id";
        $resultado = $conn->query($sql);
        $cliente = $resultado->fetch_assoc();
    ?>
        <div class="form-container">
            <h3>Editar Cliente #<?php echo $cliente['id']; ?></h3>
            <form action="?acao=salvar_edicao" method="POST">
                <!-- Campo oculto para enviar o ID do cliente que está sendo editado -->
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

    <?php endif; ?>

    <br>
    <a href="login.php">Voltar para Login</a>

</body>
</html>

<?php $conn->close(); ?>