<?php
session_start();
require_once '../../config/database.php';
require_once '../../auth.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Buscar dados do serviço e cliente
try {
    $stmt = $pdo->prepare("SELECT os.*, c.nome, c.telefone, c.endereco, c.email 
                          FROM ordem_servico os 
                          JOIN clientes c ON os.cliente_id = c.id 
                          WHERE os.id = ?");
    $stmt->execute([$id]);
    $ordem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ordem) {
        header("Location: index.php");
        exit();
    }
} catch(PDOException $e) {
    die("Erro ao buscar dados.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Ordem de Serviço - WR Eletrônica</title>
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
                <a href="index.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="../../logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-file-alt"></i> Ordem de Serviço #<?php echo $ordem['id']; ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Dados do Cliente</h5>
                        <hr>
                        <p><strong>Nome:</strong> <?php echo htmlspecialchars($ordem['nome']); ?></p>
                        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($ordem['telefone']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($ordem['email']); ?></p>
                        <p><strong>Endereço:</strong> <?php echo htmlspecialchars($ordem['endereco']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Dados do Aparelho</h5>
                        <hr>
                        <p><strong>Tipo:</strong> <?php echo htmlspecialchars($ordem['tipo_aparelho']); ?></p>
                        <p><strong>Marca:</strong> <?php echo htmlspecialchars($ordem['marca']); ?></p>
                        <p><strong>Modelo:</strong> <?php echo htmlspecialchars($ordem['modelo']); ?></p>
                        <p><strong>Número de Série:</strong> <?php echo htmlspecialchars($ordem['numero_serie']); ?></p>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <h5>Detalhes do Serviço</h5>
                        <hr>
                        <p><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($ordem['descricao'])); ?></p>
                        <p><strong>Data de Entrada:</strong> <?php echo date('d/m/Y', strtotime($ordem['data_entrada'])); ?></p>
                        <p><strong>Prazo:</strong> <?php echo date('d/m/Y', strtotime($ordem['data_previsao'])); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-<?php 
                                echo $ordem['status'] == 'Aguardando' ? 'warning' : 
                                    ($ordem['status'] == 'Em Andamento' ? 'info' : 
                                        ($ordem['status'] == 'Concluído' ? 'success' : 'secondary')); 
                            ?>">
                                <?php echo htmlspecialchars($ordem['status']); ?>
                            </span>
                        </p>
                        <p><strong>Valor:</strong> R$ <?php echo number_format($ordem['valor'], 2, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="edit.php?id=<?php echo $ordem['id']; ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="pdf.php?id=<?php echo $ordem['id']; ?>" class="btn btn-secondary">
                    <i class="fas fa-file-pdf"></i> Gerar PDF
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>