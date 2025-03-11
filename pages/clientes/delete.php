<?php
session_start();
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

try {
    // Verifica se existem serviços vinculados
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM servicos WHERE cliente_id = ?");
    $stmt->execute([$id]);
    $tem_servicos = $stmt->fetchColumn() > 0;

    if ($tem_servicos) {
        $_SESSION['error'] = "Não é possível excluir o cliente pois existem serviços vinculados.";
        header("Location: index.php");
        exit();
    }

    // Exclui o cliente
    $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success'] = "Cliente excluído com sucesso.";
    header("Location: index.php");
    exit();
} catch(PDOException $e) {
    $_SESSION['error'] = "Erro ao excluir cliente.";
    header("Location: index.php");
    exit();
}