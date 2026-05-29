<?php
// informacao sql
$db_host = "localhost";
$db_user = "root"; 
$db_pass = "";
$db_name = "lab_seguranca";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

$erro = "";

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["username"];
    $senha = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE username = '$usuario' AND password = '$senha'";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        header("location: portal.php");
    } else { 
        $erro = "Falha na autenticação. O usuário <b>" . $usuario . "</b> ou a senha estão incorretos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center py-4 bg-dark">
    <main class="form-signin w-100 m-auto" style="max-width: 400px; margin-top: 10vh !important;">
        <div class="card bg-secondary text-light border-danger">
            <div class="card-header bg-danger text-white text-center">
                <h4>Acesso ao Sistema</h4>
            </div>
            <div class="card-body p-4">
                <?php if($erro) echo "<div class='alert alert-warning'>$erro</div>"; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuário</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button class="btn btn-danger w-100" type="submit">Entrar</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>