<?php
require_once '../../config/database.php';
require_once '../../lib/TCPDF-main/tcpdf.php';  // Nova linha de inclusão
require_once '../../vendor/autoload.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Buscar dados do serviço e cliente
try {
    $stmt = $pdo->prepare("SELECT s.*, c.nome as name, c.telefone as phone, c.endereco as address, c.email 
                          FROM ordem_servico s 
                          JOIN clientes c ON s.cliente_id = c.id 
                          WHERE s.id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$data) {
        header("Location: index.php");
        exit();
    }
} catch(PDOException $e) {
    die("Erro ao buscar dados.");
}

// Criar PDF
class MYPDF extends TCPDF {
    public function Header() {
        $this->SetFont('helvetica', 'B', 20);
        $this->Cell(0, 15, 'Ordem de Serviço', 0, true, 'C');
        $this->SetFont('helvetica', '', 10);
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C');
    }
}

$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8');

$pdf->SetCreator('Repair Shop');
$pdf->SetAuthor('Repair Shop');
$pdf->SetTitle('Ordem de Serviço #' . $id);

$pdf->AddPage();

// Informações do Cliente
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Dados do Cliente', 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 7, 'Nome: ' . $data['name'], 0, 1);
$pdf->Cell(0, 7, 'Telefone: ' . $data['phone'], 0, 1);
$pdf->Cell(0, 7, 'Email: ' . $data['email'], 0, 1);
$pdf->Cell(0, 7, 'Endereço: ' . $data['address'], 0, 1);

$pdf->Ln(10);

// Informações do Serviço
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Detalhes do Serviço', 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 7, 'Aparelho: ' . $data['tipo_aparelho'], 0, 1);
$pdf->Cell(0, 7, 'Marca: ' . $data['marca'], 0, 1);
$pdf->Cell(0, 7, 'Modelo: ' . $data['modelo'], 0, 1);
$pdf->Cell(0, 7, 'Número de Série: ' . $data['numero_serie'], 0, 1);
$pdf->Cell(0, 7, 'Observações: ' . $data['descricao'], 0, 1);
$pdf->Cell(0, 7, 'Data de Entrada: ' . date('d/m/Y', strtotime($data['data_entrada'])), 0, 1);
$pdf->Cell(0, 7, 'Prazo: ' . date('d/m/Y', strtotime($data['data_previsao'])), 0, 1);
$pdf->Cell(0, 7, 'Valor: R$ ' . number_format($data['valor'], 2, ',', '.'), 0, 1);
$pdf->Cell(0, 7, 'Problema: ' . $data['problem_description'], 0, 1);
$pdf->Cell(0, 7, 'Data de Entrada: ' . date('d/m/Y', strtotime($data['entry_date'])), 0, 1);
$pdf->Cell(0, 7, 'Prazo: ' . date('d/m/Y', strtotime($data['deadline'])), 0, 1);

$pdf->Ln(10);

// Termo de Abandono (se status for 'abandoned')
if ($data['status'] == 'abandoned') {
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Termo de Abandono', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    
    $texto_abandono = "De acordo com o artigo 1.275, III, do Código Civil Brasileiro, " .
                     "o aparelho acima descrito será considerado abandonado após 90 (noventa) " .
                     "dias da data limite para retirada. Após este prazo, o estabelecimento " .
                     "poderá dar ao bem o destino que julgar apropriado, incluindo sua venda " .
                     "para cobrir despesas de armazenamento e serviços prestados.";
    
    $pdf->MultiCell(0, 7, $texto_abandono, 0, 'J');
    
    $pdf->Ln(20);
    $pdf->Cell(0, 7, 'Data: ____/____/________', 0, 1);
    $pdf->Ln(10);
    $pdf->Cell(0, 7, 'Assinatura do Cliente: _________________________________', 0, 1);
}

// Assinaturas (para status normal)
else {
    $pdf->Ln(20);
    $pdf->Cell(95, 7, 'Cliente: _________________________', 0, 0);
    $pdf->Cell(95, 7, 'Técnico: _________________________', 0, 1);
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(95, 7, 'Data: ____/____/________', 0, 0);
    $pdf->Cell(95, 7, 'Data: ____/____/________', 0, 1);
}

$pdf->Output('ordem_servico_' . $id . '.pdf', 'I');