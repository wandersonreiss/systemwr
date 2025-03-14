<?php
session_start();
require_once '../../config/database.php';
require_once '../../auth.php';
require_once '../../includes/functions.php'; // Adicione esta linha

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $observacoes = $_POST['observacoes'];
    $cpf = !empty($_POST['cpf']) ? $_POST['cpf'] : null;
    
    $error = null;
    
    // Only validate CPF if one was provided
    if (!empty($cpf)) {
        $cpf_clean = preg_replace('/[^0-9]/', '', $cpf);
        if (!validateCPF($cpf_clean)) {
            $error = "CPF inválido. Por favor, verifique.";
        } else {
            $cpf = formatCPF($cpf_clean);
        }
    }
    
    if (!$error) {
        $stmt = $pdo->prepare("INSERT INTO clientes (nome, telefone, email, endereco, observacoes, cpf) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $telefone, $email, $endereco, $observacoes, $cpf]);
        header("Location: index.php");
        exit();
    }
    
    // Adicione máscara ao campo CPF
    echo "<script>
        function formatarCPF(campo) {
            campo.value = campo.value.replace(/\D/g, '');
            campo.value = campo.value.replace(/(\d{3})(\d)/, '$1.$2');
            campo.value = campo.value.replace(/(\d{3})(\d)/, '$1.$2');
            campo.value = campo.value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }
    </script>";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Cliente - WR Eletrônica</title>
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
                <a href="index.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="../../index.php" class="btn btn-outline-light btn-sm btn-back">
                    <i class="fas fa-home"></i> Início
                </a>
                <span class="text-light me-3">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                </span>
                <a href="../../logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>
<div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-user-plus"></i> Novo Cliente</h4>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">CPF</label>
                            <input type="text" name="cpf" class="form-control" 
                                   placeholder="000.000.000-00" 
                                   oninput="formatarCPF(this)" 
                                   maxlength="14">
                            <small class="text-muted">Digite apenas números. A formatação será automática.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Endereço</label>
                            <input type="text" name="endereco" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validação do formulário
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>