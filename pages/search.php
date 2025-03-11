<?php
session_start();
require_once '../config/database.php';
require_once '../auth.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$results = [];

if ($search) {
    // Buscar clientes
    $stmt = $pdo->prepare("SELECT 'cliente' as tipo, id, nome as titulo, telefone, email 
                          FROM clientes 
                          WHERE id = ? OR nome LIKE ? OR telefone LIKE ? OR email LIKE ?");
    $searchTerm = "%{$search}%";
    $stmt->execute([$search, $searchTerm, $searchTerm, $searchTerm]);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar serviços
    $stmt = $pdo->prepare("SELECT 's' as tipo, s.id, 
                          CONCAT(c.nome, ' - ', s.tipo_aparelho) as titulo,
                          s.status, s.data_entrada, c.id as cliente_id
                          FROM servicos s
                          JOIN clientes c ON s.cliente_id = c.id
                          WHERE c.nome LIKE ? OR s.tipo_aparelho LIKE ? 
                          OR s.marca LIKE ? OR s.modelo LIKE ?");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $results = array_merge($clientes, $servicos);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar - WR Eletrônica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <img src="../assets/img/logo.png" alt="WR Eletrônica" class="logo-img">
                WR Eletrônica
            </a>
            <div class="ms-auto">
                <a href="../index.php" class="btn btn-outline-light btn-sm btn-back">
                    <i class="fas fa-home"></i> Início
                </a>
                <span class="text-light me-3">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                </span>
                <a href="../logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-search"></i> Buscar</h4>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Digite o ID, nome do cliente, telefone ou aparelho..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </form>

                <?php if ($search): ?>
                    <?php if (empty($results)): ?>
                        <div class="alert alert-info">
                            Nenhum resultado encontrado para "<?php echo htmlspecialchars($search); ?>"
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($results as $result): ?>
                                <?php if ($result['tipo'] == 'cliente'): ?>
                                    <a href="clientes/edit.php?id=<?php echo $result['id']; ?>" 
                                       class="list-group-item list-group-item-action">
                                        <i class="fas fa-user"></i>
                                        <strong>Cliente #<?php echo str_pad($result['id'], 5, '0', STR_PAD_LEFT); ?></strong> - 
                                        <?php echo htmlspecialchars($result['titulo']); ?>
                                        <br>
                                        <small>
                                            Telefone: <?php echo htmlspecialchars($result['telefone']); ?>
                                            | Email: <?php echo htmlspecialchars($result['email']); ?>
                                        </small>
                                    </a>
                                <?php else: ?>
                                    <a href="servicos/edit.php?id=<?php echo $result['id']; ?>" 
                                       class="list-group-item list-group-item-action">
                                        <i class="fas fa-tools"></i>
                                        <strong>Serviço #<?php echo $result['id']; ?></strong> - 
                                        <?php echo htmlspecialchars($result['titulo']); ?>
                                        <br>
                                        <small>
                                            Status: <?php echo htmlspecialchars($result['status']); ?>
                                            | Data: <?php echo date('d/m/Y', strtotime($result['data_entrada'])); ?>
                                            | ID Cliente: <?php echo str_pad($result['cliente_id'], 5, '0', STR_PAD_LEFT); ?>
                                        </small>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>