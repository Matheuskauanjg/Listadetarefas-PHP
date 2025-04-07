<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tarefa_id'])) {
    try {
        // Verifica se a tarefa pertence ao usuário
        $stmt = $pdo->prepare('SELECT status FROM tarefas WHERE id = ? AND usuario_id = ?');
        $stmt->execute([$_POST['tarefa_id'], $_SESSION['usuario_id']]);
        $tarefa = $stmt->fetch();

        if ($tarefa) {
            $novo_status = $tarefa['status'] === 'concluida' ? 'pendente' : 'concluida';
            
            $stmt = $pdo->prepare('UPDATE tarefas SET status = ? WHERE id = ?');
            $stmt->execute([$novo_status, $_POST['tarefa_id']]);
        }
    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao atualizar status: " . $e->getMessage();
    }
}

header('Location: dashboard.php');
exit();
?>