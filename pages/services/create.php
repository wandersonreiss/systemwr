<?php
require_once '../../config/database.php';

// Buscar todos os clientes para o select
// No início do arquivo
$stmt = $pdo->query("SELECT id, nome as name, telefone as phone FROM clientes ORDER BY nome");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// No PHP, adicionar a variável
// No PHP, atualizar o nome da variável e da coluna
// No PHP, remover a variável defeito_relatado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['client_id'];
    $tipo_aparelho = $_POST['tipo_aparelho'];  // Removido o 'o' extra
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $numero_serie = $_POST['numero_serie'];
    $data_entrada = $_POST['data_entrada'];
    $data_previsao = $_POST['deadline'];
    $valor = $_POST['valor_estimado'];

    // Formatando a descrição completa
    $descricao = "APARELHO:\n";
    $descricao .= "Tipo: " . $tipo_aparelho . "\n";
    $descricao .= "Marca: " . $marca . "\n";
    $descricao .= "Modelo: " . $modelo . "\n";
    $descricao .= "Número de Série: " . $numero_serie . "\n\n";
    $descricao .= "OBSERVAÇÕES:\n" . $_POST['observacoes'];

    $sql = "INSERT INTO ordem_servico (cliente_id, tipo_aparelho, marca, modelo, numero_serie, descricao, data_entrada, data_previsao, valor) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    try {
        // Debug para ver a query real
        echo "<pre>";
        $debug_sql = "INSERT INTO ordem_servico (cliente_id, tipo_aparelho, marca, modelo, numero_serie, descricao, data_entrada, data_previsao, valor) 
            VALUES ('$cliente_id', '$tipo_aparelho', '$marca', '$modelo', '$numero_serie', '$descricao', '$data_entrada', '$data_previsao', '$valor')";
        echo "SQL Debug: " . $debug_sql;
        echo "</pre>";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $cliente_id,
            $tipo_aparelho,
            $marca,
            $modelo,
            $numero_serie,
            $descricao,
            $data_entrada,
            $data_previsao,
            $valor
        ]);
        
        $message = "Ordem de serviço cadastrada com sucesso!";
        $alertClass = "alert-success";
    } catch(PDOException $e) {
        $message = "Erro ao cadastrar ordem de serviço: " . $e->getMessage();
        $alertClass = "alert-danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Novo Serviço</title>
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
                        <h4 class="mb-0"><i class="fas fa-tools"></i> Cadastrar Novo Serviço</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($message)): ?>
                            <div class="alert <?php echo $alertClass; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="client_id" class="form-label">Cliente</label>
                                <select class="form-control select2" id="client_id" name="client_id" required>
                                    <option value="">Selecione um cliente</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo $client['id']; ?>">
                                            <?php echo htmlspecialchars($client['name'] . ' - ' . $client['phone']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tipo_aparelho" class="form-label">Tipo do Aparelho</label>
                                <input type="text" class="form-control" id="tipo_aparelho" name="tipo_aparelho" required>
                            </div>
                            <div class="mb-3">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca" required>
                            </div>
                            <div class="mb-3">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" required>
                            </div>
                            <div class="mb-3">
                                <label for="numero_serie" class="form-label">Número de Série</label>
                                <input type="text" class="form-control" id="numero_serie" name="numero_serie">
                            </div>
                            <!-- Campo defeito_relatado removido -->
                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="data_entrada" class="form-label">Data de Entrada</label>
                                <input type="date" class="form-control" id="data_entrada" name="data_entrada" required>
                            </div>
                            <div class="mb-3">
                                <label for="deadline" class="form-label">Prazo de Entrega</label>
                                <input type="date" class="form-control" id="deadline" name="deadline" required>
                            </div>
                            <div class="mb-3">
                                <label for="valor_estimado" class="form-label">Valor Estimado</label>
                                <input type="number" step="0.01" class="form-control" id="valor_estimado" name="valor_estimado">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Cadastrar Serviço
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

            // Definir data mínima como hoje
            var today = new Date().toISOString().split('T')[0];
            document.getElementById('deadline').setAttribute('min', today);
        });
    </script>
</body>
</html>