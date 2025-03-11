<?php
session_start();
require_once '../../config/database.php';

// Verifica se é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_id'] != 1) {
    header("Location: /systemwr/index.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'update_user') {
        $nome = $_POST['nome'] ?? '';
        $username = $_POST['username'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        
        try {
            if (!empty($nova_senha)) {
                $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, username = ?, password = ? WHERE id = 1");
                $stmt->execute([$nome, $username, $hash]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, username = ? WHERE id = 1");
                $stmt->execute([$nome, $username]);
            }
            $message = '<div class="alert alert-success">Dados atualizados com sucesso!</div>';
            $_SESSION['usuario_nome'] = $nome;
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Erro ao atualizar dados.</div>';
        }
    }
}

// Busca dados atuais do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = 1");
$stmt->execute();
$usuario = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuário - WR Eletrônica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/systemwr/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="/systemwr/index.php">
                <img src="/systemwr/assets/img/logo.png" alt="WR Eletrônica" class="logo-img">
                WR Eletrônica
            </a>
            <div class="ms-auto">
                <a href="/systemwr/index.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-home"></i> Início
                </a>
                <a href="/systemwr/logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-cog"></i> Gerenciar Usuário</h5>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="update_user">
                            
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                    value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuário</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                    value="<?php echo htmlspecialchars($usuario['username']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nova_senha" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                                <input type="password" class="form-control" id="nova_senha" name="nova_senha">
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="/systemwr/index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Voltar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>