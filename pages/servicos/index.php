<?php
session_start();
require_once '../../config/database.php';
require_once '../../auth.php';

// Buscar ordens de serviço com informações do cliente
$stmt = $pdo->query("SELECT os.*, c.nome as cliente_nome 
                     FROM ordem_servico os 
                     JOIN clientes c ON os.cliente_id = c.id 
                     ORDER BY os.data_entrada DESC");
$ordens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordens de Serviço - WR Eletrônica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">
                <img src="../../assets/img/logo.png" alt="WR Eletrônica" class="logo-img">
                WR Eletrônica
            </a>
            <div class="ms-auto">
                <a href="../../index.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-home"></i> Início
                </a>
                <span class="text-light me-3">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                </span>
                <a href="../../logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-tools"></i> Ordens de Serviço</h4>
                <a href="create.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nova Ordem
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Aparelho</th>
                                <th>Data Entrada</th>
                                <th>Prazo</th>
                                <th>Status</th>
                                <th>Valor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ordens as $ordem): ?>
                                <tr>
                                    <td><?php echo $ordem['id']; ?></td>
                                    <td><?php echo htmlspecialchars($ordem['cliente_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($ordem['tipo_aparelho']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($ordem['data_entrada'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($ordem['data_previsao'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $ordem['status'] == 'Aguardando' ? 'warning' : 
                                                ($ordem['status'] == 'Em Andamento' ? 'info' : 
                                                    ($ordem['status'] == 'Concluído' ? 'success' : 'secondary')); 
                                        ?>">
                                            <?php echo htmlspecialchars($ordem['status']); ?>
                                        </span>
                                    </td>
                                    <td>R$ <?php echo number_format($ordem['valor'], 2, ',', '.'); ?></td>
                                    <td>
                                        <a href="view.php?id=<?php echo $ordem['id']; ?>" class="btn btn-sm btn-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?php echo $ordem['id']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="pdf.php?id=<?php echo $ordem['id']; ?>" class="btn btn-sm btn-secondary" title="Gerar PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
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