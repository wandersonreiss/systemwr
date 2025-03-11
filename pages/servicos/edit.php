<?php
session_start();
require_once '../../config/database.php';
require_once '../../auth.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE ordem_servico SET 
            tipo_aparelho = ?, marca = ?, modelo = ?, numero_serie = ?,
            descricao = ?, status = ?, data_previsao = ?, valor = ?
            WHERE id = ?");
        
        $stmt->execute([
            $_POST['tipo_aparelho'],
            $_POST['marca'],
            $_POST['modelo'],
            $_POST['numero_serie'],
            $_POST['descricao'],
            $_POST['status'],
            $_POST['data_previsao'],
            str_replace(['R$', '.', ','], ['', '', '.'], $_POST['valor']),
            $id
        ]);

        header("Location: index.php");
        exit();
    } catch(PDOException $e) {
        $error = "Erro ao atualizar os dados.";
    }
}

// Buscar dados atuais
try {
    $stmt = $pdo->prepare("SELECT os.*, c.nome, c.telefone 
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
    <title>Editar Ordem de Serviço - WR Eletrônica</title>
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
                <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Ordem de Serviço #<?php echo $ordem['id']; ?></h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cliente</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($ordem['nome']); ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo do Aparelho</label>
                                <input type="text" name="tipo_aparelho" class="form-control" value="<?php echo htmlspecialchars($ordem['tipo_aparelho']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Marca</label>
                                <input type="text" name="marca" class="form-control" value="<?php echo htmlspecialchars($ordem['marca']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Modelo</label>
                                <input type="text" name="modelo" class="form-control" value="<?php echo htmlspecialchars($ordem['modelo']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Número de Série</label>
                                <input type="text" name="numero_serie" class="form-control" value="<?php echo htmlspecialchars($ordem['numero_serie']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Aguardando" <?php echo $ordem['status'] == 'Aguardando' ? 'selected' : ''; ?>>Aguardando</option>
                                    <option value="Em Andamento" <?php echo $ordem['status'] == 'Em Andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                                    <option value="Concluído" <?php echo $ordem['status'] == 'Concluído' ? 'selected' : ''; ?>>Concluído</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prazo</label>
                                <input type="date" name="data_previsao" class="form-control" value="<?php echo $ordem['data_previsao']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Valor</label>
                                <input type="text" name="valor" class="form-control" value="<?php echo number_format($ordem['valor'], 2, ',', '.'); ?>" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="4" required><?php echo htmlspecialchars($ordem['descricao']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>