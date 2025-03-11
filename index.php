<?php
require_once 'auth.php';
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WR Eletrônica - Sistema de Gestão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/systemwr/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="/systemwr/index.php">
                <img src="/systemwr/assets/img/logo.png?v=<?php echo time(); ?>" alt="WR Eletrônica" class="logo-img">
                WR Eletrônica
            </a>
            <div class="ms-auto">
                <span class="text-light me-3">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                </span>
                <a href="/systemwr/pages/admin/gerenciar_usuario.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-user-cog"></i> Gerenciar Usuário
                </a>
                <a href="/systemwr/logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Clientes</h5>
                    </div>
                    <div class="card-body">
                        <p>Gerenciar cadastro de clientes</p>
                        <a href="/systemwr/pages/clientes/index.php" class="btn btn-primary w-100">
                            <i class="fas fa-arrow-right"></i> Acessar
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tools"></i> Serviços</h5>
                    </div>
                    <div class="card-body">
                        <p>Gerenciar ordens de serviço</p>
                        <a href="/systemwr/pages/servicos/index.php" class="btn btn-primary w-100">
                            <i class="fas fa-arrow-right"></i> Acessar
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-search"></i> Buscar</h5>
                    </div>
                    <div class="card-body">
                        <p>Pesquisar clientes e serviços</p>
                        <a href="/systemwr/pages/search.php" class="btn btn-primary w-100">
                            <i class="fas fa-arrow-right"></i> Acessar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>