<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$filtro_status = $_GET['status'] ?? 'todos';
$filtro_prioridade = $_GET['prioridade'] ?? 'todos';

// Buscar tarefas do usuário
try {
    $query = "SELECT * FROM tarefas WHERE usuario_id = :usuario_id";
    $params = [':usuario_id' => $_SESSION['usuario_id']];

    if ($filtro_status !== 'todos') {
        $query .= " AND status = :status";
        $params[':status'] = $filtro_status;
    }

    if ($filtro_prioridade !== 'todos') {
        $query .= " AND prioridade = :prioridade";
        $params[':prioridade'] = $filtro_prioridade;
    }

    $query .= " ORDER BY data_venimento ASC, prioridade DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $tarefas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar tarefas: " . $e->getMessage());
}

// Adicionar nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = [];
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $prioridade = $_POST['prioridade'];
    $data_venimento = $_POST['data_venimento'];

    if (empty($titulo)) {
        $erros[] = "Título é obrigatório";
    }

    if (empty($erros)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO tarefas (usuario_id, titulo, descricao, prioridade, data_venimento) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([
                $_SESSION['usuario_id'],
                $titulo,
                $descricao,
                $prioridade,
                $data_venimento ?: null
            ]);
            header('Location: dashboard.php');
            exit();
        } catch (PDOException $e) {
            $erros[] = "Erro ao criar tarefa: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento de Tarefas</title>
    
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        
        .container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            padding: 0.75rem;
        }
        
        .task-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .priority-badge {
            padding: 0.35em 0.65em;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .bg-danger { background-color: #dc3545!important; }
        .bg-warning { background-color: #ffc107!important; color: #000; }
        .bg-success { background-color: #198754!important; }
        
        .btn {
            border-radius: 6px;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }
        
        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: white;
        }
        
        .status-concluida {
            color: #198754;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .status-concluida::before {
            content: "✓";
            font-size: 1.1em;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
                margin-top: 1rem;
            }
            
            .row.g-3 {
                gap: 1rem!important;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Todo App</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Olá, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn btn-light">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Adicionar Tarefa</h2>
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="titulo" placeholder="Título" required>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="descricao" placeholder="Descrição" rows="2"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <select class="form-select" name="prioridade" required>
                                <option value="media">Prioridade Média</option>
                                <option value="alta">Alta Prioridade</option>
                                <option value="baixa">Baixa Prioridade</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="date" class="form-control" name="data_venimento">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Adicionar Tarefa</button>
                </form>
            </div>
            
            <div class="col-md-6">
                <h2>Filtrar Tarefas</h2>
                <form class="row g-3">
                    <div class="col-md-6">
                        <select class="form-select" name="status">
                            <option value="todos">Todos os Status</option>
                            <option value="pendente">Pendentes</option>
                            <option value="concluida">Concluídas</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" name="prioridade">
                            <option value="todos">Todas as Prioridades</option>
                            <option value="alta">Alta</option>
                            <option value="media">Média</option>
                            <option value="baixa">Baixa</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-secondary">Aplicar Filtros</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($tarefas as $tarefa): ?>
                <div class="col">
                    <div class="card task-card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title"><?php echo htmlspecialchars($tarefa['titulo']); ?></h5>
                                <span class="badge priority-badge bg-<?php 
                                    echo match($tarefa['prioridade']) {
                                        'alta' => 'danger',
                                        'media' => 'warning',
                                        'baixa' => 'success'
                                    };
                                ?>">
                                    <?php echo ucfirst($tarefa['prioridade']); ?>
                                </span>
                            </div>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($tarefa['descricao'])); ?></p>
                            <div class="mt-auto">
                                <?php if ($tarefa['data_venimento']): ?>
                                    <small class="text-muted">Vencimento: <?php echo date('d/m/Y', strtotime($tarefa['data_venimento'])); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <form method="POST" action="atualizar_status.php" class="d-inline">
                                    <input type="hidden" name="tarefa_id" value="<?php echo $tarefa['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-<?php echo $tarefa['status'] === 'concluida' ? 'success' : 'outline-secondary'; ?>">
                                        <?php echo $tarefa['status'] === 'concluida' ? 'Concluída' : 'Marcar como concluída'; ?>
                                    </button>
                                </form>
                                <a href="editar_tarefa.php?id=<?php echo $tarefa['id']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>