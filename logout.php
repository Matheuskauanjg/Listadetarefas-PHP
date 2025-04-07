<?php
session_start();

$_SESSION = array();

session_destroy();

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
    <div class="container mt-5">
        <div class="alert alert-info">
            Logout realizado com sucesso. Redirecionando...
        </div>
    </div>
</body>
</html>
