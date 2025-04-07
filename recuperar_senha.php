<?php
session_start();
require 'config.php';

$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido";
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() === 1) {
                $token = bin2hex(random_bytes(32));
                $expira_em = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $stmt = $pdo->prepare('UPDATE usuarios SET reset_token = ?, reset_expira = ? WHERE email = ?');
                $stmt->execute([$token, $expira_em, $email]);

                $link = "http://localhost/recuperar_senha.php?token=$token";
                $sucesso = true;
            }
        } catch (PDOException $e) {
            $erros[] = "Erro ao processar solicitação: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_senha'])) {
    $token = $_POST['token'];
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];

    if (empty($token)) {
        $erros[] = "Token inválido";
    } elseif ($nova_senha !== $confirma_senha) {
        $erros[] = "As senhas não coincidem";
    } elseif (strlen($nova_senha) < 8) {
        $erros[] = "A senha deve ter pelo menos 8 caracteres";
    } else {
        try {
            $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE reset_token = ? AND reset_expira > NOW()');
            $stmt->execute([$token]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE usuarios SET senha = ?, reset_token = NULL, reset_expira = NULL WHERE id = ?');
                $stmt->execute([$senha_hash, $usuario['id']]);
                
                $_SESSION['mensagem'] = "Senha redefinida com sucesso!";
                header('Location: login.php');
                exit();
            } else {
                $erros[] = "Token inválido ou expirado";
            }
        } catch (PDOException $e) {
            $erros[] = "Erro ao redefinir senha: " . $e->getMessage();
        }
    }
}

$token_valido = false;
if (isset($_GET['token'])) {
    try {
        $stmt = $pdo->prepare('SELECT email FROM usuarios WHERE reset_token = ? AND reset_expira > NOW()');
        $stmt->execute([$_GET['token']]);
        $token_valido = $stmt->rowCount() === 1;
    } catch (PDOException $e) {
        $erros[] = "Erro ao validar token: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
</head>
<body>
    <div>
        <div>
            <?php if (!empty($erros)): ?>
                <div style="color: red;">
                    <?php foreach ($erros as $erro): ?>
                        <p><?php echo htmlspecialchars($erro); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div>
                    <label>E-mail</label>
                    <input type="email" name="email" required>
                </div>
                <button type="submit">Enviar</button>
            </form>
        </div>
    </div>
</body>
</html>
