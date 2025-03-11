<?php
require_once '../../config/database.php';

// Buscar todos os serviços com informações dos clientes
// Atualizar a query inicial
$sql = "SELECT s.*, c.nome as client_name, c.telefone as client_phone 
        FROM ordem_servico s 
        JOIN clientes c ON s.cliente_id = c.id 
        ORDER BY s.data_entrada DESC";
$stmt = $pdo->query($sql);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar alteração de status
// Atualizar a query de status
if (isset($_POST['update_status'])) {
    $service_id = $_POST['service_id'];
    $new_status = $_POST['status'];
    
    try {
        $sql = "UPDATE ordem_servico SET status = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_status, $service_id]);
        header("Location: index.php");
        exit();
    } catch(PDOException $e) {
        $error = "Erro ao atualizar status.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Serviços</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">Repair Shop</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-tools"></i> Serviços</h2>
            <a href="create.php" class="btn btn-success">
                <i class="fas fa-plus"></i> Novo Serviço
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Aparelho</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Prazo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($service['client_name']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($service['client_phone']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($service['tipo_aparelho']); ?></td>
                                    <td><?php echo htmlspecialchars($service['descricao']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($service['status']) {
                                                'pending' => 'warning',
                                                'in_progress' => 'info',
                                                'completed' => 'success',
                                                'abandoned' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php 
                                            echo match($service['status']) {
                                                'pending' => 'Pendente',
                                                'in_progress' => 'Em Andamento',
                                                'completed' => 'Concluído',
                                                'abandoned' => 'Abandonado',
                                                default => 'Desconhecido'
                                            };
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($service['deadline'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" 
                                                    data-bs-toggle="dropdown">
                                                Ações
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form method="POST" class="dropdown-item">
                                                        <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                                        <input type="hidden" name="status" value="Em Andamento">
                                                        <button type="submit" name="update_status" class="btn btn-link text-info">
                                                            <i class="fas fa-clock"></i> Em Andamento
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" class="dropdown-item">
                                                        <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                                        <input type="hidden" name="status" value="Concluído">
                                                        <button type="submit" name="update_status" class="btn btn-link text-success">
                                                            <i class="fas fa-check"></i> Concluído
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" class="dropdown-item">
                                                        <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                                        <input type="hidden" name="status" value="Cancelado">
                                                        <button type="submit" name="update_status" class="btn btn-link text-danger">
                                                            <i class="fas fa-times"></i> Abandonado
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a href="edit.php?id=<?php echo $service['id']; ?>" class="dropdown-item">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="pdf.php?id=<?php echo $service['id']; ?>" class="dropdown-item" target="_blank">
                                                        <i class="fas fa-file-pdf"></i> Gerar PDF
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>