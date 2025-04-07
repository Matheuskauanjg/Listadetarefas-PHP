<?php
session_start();
require 'config.php';

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $credencial = trim($_POST['credencial']);
    $senha = $_POST['senha'];

    if (empty($credencial) || empty($senha)) {
        $erros[] = "Preencha todos os campos";
    } else {
        try {
            $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE username = ? OR email = ?');
            $stmt->execute([$credencial, $credencial]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['username'] = $usuario['username'];
                header('Location: dashboard.php');
                exit();
            } else {
                $erros[] = "Credenciais inválidas";
            }
        } catch (PDOException $e) {
            $erros[] = "Erro ao autenticar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <div>
        <div>
            <h2>Login</h2>

            <?php if (!empty($erros)): ?>
                <div style="color: red;">
                    <?php foreach ($erros as $erro): ?>
                        <p><?php echo htmlspecialchars($erro); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['mensagem'])): ?>
                <div style="color: green;">
                    <?php echo htmlspecialchars($_SESSION['mensagem']); ?>
                    <?php unset($_SESSION['mensagem']); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div>
                    <label>Usuário ou E-mail</label>
                    <input type="text" name="credencial" required>
                </div>

                <div>
                    <label>Senha</label>
                    <input type="password" name="senha" required>
                </div>

                <button type="submit">Entrar</button>
            </form>

            <div>
                <a href="register.php">Não tem conta? Registre-se</a>
            </div>
            <div>
                <a href="recuperar_senha.php">Esqueceu a senha?</a>
            </div>
        </div>
    </div>
</body>
</html>