<?php
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Buscar dados do serviço
try {
    $stmt = $pdo->prepare("SELECT s.*, c.nome as client_name 
                          FROM ordem_servico s 
                          JOIN clientes c ON s.cliente_id = c.id 
                          WHERE s.id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        header("Location: index.php");
        exit();
    }
} catch(PDOException $e) {
    $error = "Erro ao buscar serviço.";
}

// Buscar todos os clientes para o select
$stmt = $pdo->query("SELECT id, nome as name, telefone as phone FROM clientes ORDER BY nome");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['client_id'];
    $tipo_aparelho = $_POST['tipo_aparelho'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $numero_serie = $_POST['numero_serie'];
    $descricao = $_POST['observacoes'];
    $data_previsao = $_POST['deadline'];
    $valor = $_POST['valor_estimado'];
    $status = $_POST['status'];

    try {
        $sql = "UPDATE ordem_servico SET 
                cliente_id = ?, 
                tipo_aparelho = ?, 
                marca = ?,
                modelo = ?,
                numero_serie = ?,
                descricao = ?,
                data_previsao = ?,
                valor = ?,
                status = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $cliente_id,
            $tipo_aparelho,
            $marca,
            $modelo,
            $numero_serie,
            $descricao,
            $data_previsao,
            $valor,
            $status,
            $id
        ]);
        $message = "Serviço atualizado com sucesso!";
        $alertClass = "alert-success";
        
        // Atualizar dados exibidos
        $service = [
            'cliente_id' => $cliente_id,
            'tipo_aparelho' => $tipo_aparelho,
            'marca' => $marca,
            'modelo' => $modelo,
            'numero_serie' => $numero_serie,
            'descricao' => $descricao,
            'data_previsao' => $data_previsao,
            'valor' => $valor,
            'status' => $status
        ];
    } catch(PDOException $e) {
        $message = "Erro ao atualizar serviço: " . $e->getMessage();
        $alertClass = "alert-danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">Repair Shop</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Serviço</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($message)): ?>
                            <div class="alert <?php echo $alertClass; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="client_id" class="form-label">Cliente</label>
                                <select class="form-control select2" id="client_id" name="client_id" required>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo $client['id']; ?>" 
                                            <?php echo ($client['id'] == $service['cliente_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($client['name'] . ' - ' . $client['phone']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tipo_aparelho" class="form-label">Tipo do Aparelho</label>
                                <input type="text" class="form-control" id="tipo_aparelho" name="tipo_aparelho"
                                       value="<?php echo htmlspecialchars($service['tipo_aparelho']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca"
                                       value="<?php echo htmlspecialchars($service['marca']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo"
                                       value="<?php echo htmlspecialchars($service['modelo']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="numero_serie" class="form-label">Número de Série</label>
                                <input type="text" class="form-control" id="numero_serie" name="numero_serie"
                                       value="<?php echo htmlspecialchars($service['numero_serie']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" 
                                          rows="3"><?php echo htmlspecialchars($service['descricao']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="deadline" class="form-label">Prazo de Entrega</label>
                                <input type="date" class="form-control" id="deadline" name="deadline" 
                                       value="<?php echo $service['data_previsao']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="valor_estimado" class="form-label">Valor Estimado</label>
                                <input type="number" step="0.01" class="form-control" id="valor_estimado" name="valor_estimado"
                                       value="<?php echo htmlspecialchars($service['valor']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="Aguardando" <?php echo ($service['status'] == 'Aguardando') ? 'selected' : ''; ?>>Aguardando</option>
                                    <option value="Em Andamento" <?php echo ($service['status'] == 'Em Andamento') ? 'selected' : ''; ?>>Em Andamento</option>
                                    <option value="Concluído" <?php echo ($service['status'] == 'Concluído') ? 'selected' : ''; ?>>Concluído</option>
                                    <option value="Cancelado" <?php echo ($service['status'] == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Voltar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>
</body>
</html>