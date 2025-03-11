<?php
require_once '../../config/database.php';
require_once '../../auth.php';

try {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Debug dos dados recebidos
    echo "<h3>Dados do POST:</h3>";
    print_r($_POST);

    // Preparação dos dados
    $dados = [
        'cliente_id' => $_POST['cliente_id'],
        'tipo_aparelho' => $_POST['tipo_aparelho'],
        'marca' => $_POST['marca'],
        'modelo' => $_POST['modelo'],
        'numero_serie' => $_POST['numero_serie'],
        'observacoes' => $_POST['observacoes'],
        'data_entrada' => $_POST['data_entrada'],
        'data_previsao' => $_POST['data_previsao'],
        'valor' => str_replace(['R$', '.', ','], ['', '', '.'], $_POST['valor']),
        'status' => 'Aguardando'
    ];

    // Gerar campos e valores para a query
    $campos = implode(', ', array_keys($dados));
    $valores = ':' . implode(', :', array_keys($dados));
    
    $sql = "INSERT INTO wr_eletronica.ordem_servico ($campos) VALUES ($valores)";
    
    echo "<h3>Dados preparados:</h3>";
    print_r($dados);
    // Query simplificada com nome da tabela explícito
    $sql = "INSERT INTO wr_eletronica.ordem_servico ($campos) VALUES ($valores)";
    
    echo "<h3>SQL gerado:</h3>";
    echo $sql;

    $stmt = $pdo->prepare($sql);
    
    echo "<h3>Executando query...</h3>";
    $result = $stmt->execute($dados);

    if ($result) {
        $id = $pdo->lastInsertId();
        echo "<h3>Ordem de serviço cadastrada com sucesso!</h3>";
        echo "ID: " . $id;
        header("refresh:3;url=index.php?success=1");
    }

} catch (PDOException $e) {
    echo "<h3>Erro no banco de dados:</h3>";
    echo "Mensagem: " . $e->getMessage() . "<br>";
    echo "SQL: " . $sql . "<br>";
    echo "Dados: ";
    print_r($dados);
    die();
}