<?php
session_start();
require 'config.php';

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];

    // Validações
    if (empty($username)) {
        $erros[] = "Nome de usuário é obrigatório";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido";
    }

    if (strlen($senha) < 8) {
        $erros[] = "A senha deve ter pelo menos 8 caracteres";
    }

    if ($senha !== $confirma_senha) {
        $erros[] = "As senhas não coincidem";
    }

    // Verifica se usuário/email já existe
    if (empty($erros)) {
        try {
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            if ($stmt->rowCount() > 0) {
                $erros[] = "Usuário ou e-mail já cadastrado";
            }
        } catch (PDOException $e) {
            $erros[] = "Erro ao verificar usuário: " . $e->getMessage();
        }
    }

    // Registra o usuário
    if (empty($erros)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare('INSERT INTO usuarios (username, email, senha) VALUES (?, ?, ?)');
            $stmt->execute([$username, $email, $senha_hash]);
            $_SESSION['mensagem'] = "Registro realizado com sucesso! Faça login.";
            header('Location: login.php');
            exit();
        } catch (PDOException $e) {
            $erros[] = "Erro ao registrar usuário: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>
<body>
    <div>
        <h2>Registro</h2>

        <?php if (!empty($erros)): ?>
            <div style="color: red;">
                <?php foreach ($erros as $erro): ?>
                    <p><?php echo htmlspecialchars($erro); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label>Usuário</label>
                <input type="text" name="username" required>
            </div>

            <div>
                <label>E-mail</label>
                <input type="email" name="email" required>
            </div>

            <div>
                <label>Senha</label>
                <input type="password" name="senha" required>
            </div>

            <div>
                <label>Confirme a Senha</label>
                <input type="password" name="confirmar_senha" required>
            </div>

            <button type="submit">Registrar</button>
        </form>

        <div>
            <a href="login.php">Já tem conta? Faça login</a>
        </div>
    </div>
</body>
</html>