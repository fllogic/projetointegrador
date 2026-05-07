<?php
// ==========================================
// config.php - Configurações e Conexão Segura
// ==========================================

// 1. BLINDAGEM DE SESSÃO (Deve vir ANTES do session_start)
// Impede que scripts no lado do cliente (XSS) leiam o cookie de sessão
ini_set('session.cookie_httponly', 1); 
// Aceita apenas cookies gerados pelo servidor (Mitiga Session Fixation)
ini_set('session.use_only_cookies', 1);
// Impede o envio do cookie em requisições cross-site (Mitiga CSRF)
ini_set('session.cookie_samesite', 'Strict'); 

session_start();

// 2. CONEXÃO COM O BANCO DE DADOS (MySQLi)
$host = 'localhost';
$db   = 'lab_defesa';
$user = 'root';
$pass = '';

// Força o MySQLi a lançar exceções em vez de warnings na tela
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Em produção, o erro real vai para um arquivo de log local do servidor
    error_log("Erro de BD: " . $e->getMessage());
    die("Erro interno de conexão. Tente novamente mais tarde.");
}

// 3. FUNÇÃO: CONTROLE DE TEMPO DE SESSÃO
function verificarSessao() {
    $timeout = 1800; // 30 minutos
    if (isset($_SESSION['ultima_atividade']) && (time() - $_SESSION['ultima_atividade']) > $timeout) {
        session_unset();
        session_destroy();
        header("Location: login.php?erro=timeout");
        exit;
    }
    $_SESSION['ultima_atividade'] = time(); // Renova o tempo
}

// 4. FUNÇÃO: AUDITORIA DE ACESSOS (Prepared Statements no MySQLi)
function registrarAuditoria($conn, $email, $status) {
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $conn->prepare("INSERT INTO log_auditoria (email_tentado, ip_origem, status_login) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $ip, $status);
    $stmt->execute();
    $stmt->close();
}
?>