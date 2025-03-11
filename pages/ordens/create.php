<?php
require_once '../../config/database.php';
require_once '../../auth.php';

// Buscar lista de clientes
$stmt = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Ordem de Serviço - WR Eletrônica</title>
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
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-plus"></i> Nova Ordem de Serviço</h4>
            </div>
            <div class="card-body">
                <form action="save.php" method="POST">
                    <div class="form-group mb-3">
                        <label for="cliente_id">Cliente</label>
                        <select class="form-control" id="cliente_id" name="cliente_id" required>
                            <option value="">Selecione um cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>">
                                    <?php echo htmlspecialchars($cliente['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tipo_aparelho">Tipo do Aparelho</label>
                        <input type="text" class="form-control" id="tipo_aparelho" name="tipo_aparelho" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="marca">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="modelo">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="numero_serie">Número de Série</label>
                        <input type="text" class="form-control" id="numero_serie" name="numero_serie">
                    </div>
                    <div class="form-group mb-3">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="data_entrada">Data de Entrada</label>
                        <input type="date" class="form-control" id="data_entrada" name="data_entrada" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="data_previsao">Previsão de Entrega</label>
                        <input type="date" class="form-control" id="data_previsao" name="data_previsao" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="valor">Valor</label>
                        <input type="text" class="form-control" id="valor" name="valor" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>