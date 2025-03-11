<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once 'config/database.php';

// Create new user with fresh hash
$nome = 'Administrador';
$username = 'admin';
$senha = '123456';
$hash = password_hash($senha, PASSWORD_BCRYPT); // Using BCRYPT explicitly

try {
    // Clear the table
    $pdo->exec("TRUNCATE TABLE usuarios");
    
    // Insert new user
    $sql = "INSERT INTO usuarios (nome, username, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $username, $hash]);
    
    // Verify if the hash works
    $verify = password_verify($senha, $hash);
    
    echo "Usuário criado com sucesso!<br>";
    echo "Username: " . $username . "<br>";
    echo "Senha: " . $senha . "<br>";
    echo "Hash gerado: " . $hash . "<br>";
    echo "Verificação do hash: " . ($verify ? 'Funciona' : 'Não funciona');
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}