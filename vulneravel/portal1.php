<?php
// Configurações do banco de dados (As mesmas do arquivo de login)
$db_host = "localhost";
$db_user = "root"; 
$db_pass = "";     
$db_name = "lab_seguranca";

// Criando a conexão
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verificando a conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Query para buscar todos os usuários
$sql = "SELECT id, username, password FROM usuarios";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - Lista de Usuários</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        table { width: 100%; border-collapse: collapse; max-width: 600px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .alerta { background-color: #ffdddd; border-left: 6px solid #f44336; padding: 10px; margin-bottom: 20px; max-width: 580px;}
    </style>
</head>
<body>

    <h2>Tabela do Banco de Dados: <code>usuarios</code></h2>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Senha (Texto Plano)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Verifica se existem registros e os lista em formato de tabela HTML
            if ($resultado && $resultado->num_rows > 0) {
                while($row = $resultado->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    // htmlspecialchars evita XSS caso o nome de usuário contenha tags HTML
                    echo "<td>" . htmlspecialchars($row["username"]) . "</td>"; 
                    echo "<td>" . htmlspecialchars($row["password"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Nenhum usuário encontrado no banco de dados.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <br>
    <a href="login.php">Voltar para a página de Login</a>

</body>
</html>

<?php
// Fecha a conexão com o banco
$conn->close();
?>