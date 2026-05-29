<?php
// Configurações do banco de dados 
$db_host = "localhost";
$db_user = "root"; 
$db_pass = "";     
$db_name = "lab_seguranca";

// Criando a conexão
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// CORREÇÃO DO ERRO: Capturando a ação da URL. Se não existir, o padrão é 'listar'
$acao = isset($_GET['acao']) ? $_GET['acao'] : 'listar';

// ==========================================
// LÓGICA DE PROCESSAMENTO (CRUD VULNERÁVEL)
// ==========================================

// 1. EXCLUIR VULNERÁVEL (SQL Injection direto no GET)
if ($acao == 'excluir' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM clientes_reparo WHERE id = $id";
    $conn->query($sql);
    header("Location: portal.php");
    exit;
}

// 2. SALVAR NOVO VULNERÁVEL (SQL Injection direto no POST)
if ($acao == 'salvar_novo' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $modelo = $_POST['modelo_aparelho'];
    $problema = $_POST['problema_relatado'];
    $status = $_POST['status_servico'];

    $sql = "INSERT INTO clientes_reparo (nome, email, telefone, modelo_aparelho, problema_relatado, status_servico) 
            VALUES ('$nome', '$email', '$telefone', '$modelo', '$problema', '$status')";
    $conn->query($sql);
    header("Location: portal.php");
    exit;
}

// 3. SALVAR EDIÇÃO VULNERÁVEL (SQL Injection direto no POST)
if ($acao == 'salvar_edicao' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $modelo = $_POST['modelo_aparelho'];
    $problema = $_POST['problema_relatado'];
    $status = $_POST['status_servico'];

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
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Painel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-danger mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="portal.php">Painel Administrativo</a>
    <div class="d-flex">
        <a href="login.php" class="btn btn-outline-light">Sair</a>
    </div>
  </div>
</nav>

<div class="container">
    <h2 class="mb-4">Gestão de Clientes: Assistência Técnica</h2>

    <?php if ($acao == 'listar'): ?>
        <a href="?acao=adicionar" class="btn btn-danger mb-3">Adicionar Novo Cliente</a>
        <div class="table-responsive">
            <table class="table table-dark table-striped table-bordered border-secondary">
                <thead>
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
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            // A vulnerabilidade de XSS é mantida ao não usar htmlspecialchars na exibição
                            echo "<td>" . $row["nome"] . "</td>"; 
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td>" . $row["telefone"] . "</td>";
                            echo "<td>" . $row["modelo_aparelho"] . "</td>";
                            echo "<td>" . $row["problema_relatado"] . "</td>";
                            echo "<td><span class='badge bg-warning text-dark'>" . $row["status_servico"] . "</span></td>";
                            echo "<td>
                                    <a href='?acao=editar&id=" . $row["id"] . "' class='btn btn-sm btn-outline-info mb-1'>Editar</a>
                                    <a href='?acao=excluir&id=" . $row["id"] . "' class='btn btn-sm btn-outline-danger mb-1' onclick=\"return confirm('Excluir?')\">Excluir</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>Nenhum cliente encontrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($acao == 'adicionar'): ?>
        <div class="card bg-secondary border-danger" style="max-width: 600px;">
            <div class="card-body">
                <form action="?acao=salvar_novo" method="POST">
                    <div class="mb-2"><label class="form-label">Nome</label><input type="text" name="nome" class="form-control" required></div>
                    <div class="mb-2"><label class="form-label">E-mail</label><input type="email" name="email" class="form-control"></div>
                    <div class="mb-2"><label class="form-label">Telefone</label><input type="text" name="telefone" class="form-control"></div>
                    <div class="mb-2"><label class="form-label">Aparelho</label><input type="text" name="modelo_aparelho" class="form-control" required></div>
                    <div class="mb-2"><label class="form-label">Problema</label><input type="text" name="problema_relatado" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Status</label>
                        <select name="status_servico" class="form-select">
                            <option>Avaliação técnica</option>
                            <option>Em andamento</option>
                            <option>Concluído</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger">Salvar</button>
                    <a href="?acao=listar" class="btn btn-outline-light">Cancelar</a>
                </form>
            </div>
        </div>

    <?php elseif ($acao == 'editar' && isset($_GET['id'])): 
        $id = $_GET['id'];
        $sql = "SELECT * FROM clientes_reparo WHERE id = $id";
        $resultado = $conn->query($sql);
        $cliente = $resultado->fetch_assoc();
    ?>
        <div class="card bg-secondary border-danger" style="max-width: 600px;">
            <div class="card-body">
                <form action="?acao=salvar_edicao" method="POST">
                    <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
                    <div class="mb-2"><label class="form-label">Nome</label><input type="text" name="nome" class="form-control" value="<?php echo $cliente['nome']; ?>" required></div>
                    <div class="mb-2"><label class="form-label">E-mail</label><input type="email" name="email" class="form-control" value="<?php echo $cliente['email']; ?>"></div>
                    <div class="mb-2"><label class="form-label">Telefone</label><input type="text" name="telefone" class="form-control" value="<?php echo $cliente['telefone']; ?>"></div>
                    <div class="mb-2"><label class="form-label">Aparelho</label><input type="text" name="modelo_aparelho" class="form-control" value="<?php echo $cliente['modelo_aparelho']; ?>" required></div>
                    <div class="mb-2"><label class="form-label">Problema</label><input type="text" name="problema_relatado" class="form-control" value="<?php echo $cliente['problema_relatado']; ?>" required></div>
                    <div class="mb-3"><label class="form-label">Status</label>
                        <select name="status_servico" class="form-select">
                            <option value="Avaliação técnica" <?php if($cliente['status_servico'] == 'Avaliação técnica') echo 'selected'; ?>>Avaliação técnica</option>
                            <option value="Em andamento" <?php if($cliente['status_servico'] == 'Em andamento') echo 'selected'; ?>>Em andamento</option>
                            <option value="Concluído" <?php if($cliente['status_servico'] == 'Concluído') echo 'selected'; ?>>Concluído</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning">Atualizar</button>
                    <a href="?acao=listar" class="btn btn-outline-light">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>