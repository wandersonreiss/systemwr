<?php
session_start();
require_once '../../config/database.php';
require_once '../../auth.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Desativa temporariamente as restrições de chave estrangeira
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        
        // Tenta excluir o cliente
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        // Reativa as restrições
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        
        if ($result && $stmt->rowCount() > 0) {
            $_SESSION['success'] = "Cliente excluído com sucesso.";
        } else {
            $_SESSION['error'] = "Não foi possível excluir o cliente ID: " . $id;
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Erro ao excluir: " . $e->getMessage();
    }
}

header("Location: index.php");
exit();