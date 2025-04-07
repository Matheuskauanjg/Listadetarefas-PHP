<?php
session_start();

// Destrói todas as variáveis de sessão
$_SESSION = array();

// Destrói a sessão
session_destroy();

// Redireciona para login com mensagem de sucesso
$_SESSION['mensagem'] = "Logout realizado com sucesso!";
header('Location: login.php');
exit();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0;url=login.php">
</head>
<body>
    <!-- Feedback visual temporário caso o redirecionamento falhe -->
    <div class="container mt-5">
        <div class="alert alert-info">
            Logout realizado com sucesso. Redirecionando...
        </div>
    </div>
</body>
</html>