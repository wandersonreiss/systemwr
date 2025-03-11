<?php
require_once '../../config/database.php';
require_once '../../lib/TCPDF-main/tcpdf.php';

// Remova esta linha pois já não é necessária
// require_once '../../vendor/autoload.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Buscar dados do serviço e cliente
try {
    $stmt = $pdo->prepare("SELECT os.*, c.id as id_cliente, c.nome, c.telefone, c.endereco, c.email 
                          FROM ordem_servico os 
                          JOIN clientes c ON os.cliente_id = c.id 
                          WHERE os.id = ?");
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
        // Logo centralizada e menor
        $image_file = '../../assets/img/logo.png';
        if (file_exists($image_file)) {
            $this->StartTransform();
            $this->SetAlpha(0.9);
            $this->Image($image_file, 95, 5, 15);
            $this->StopTransform();
        }
        
        // Apenas o título principal
        $this->SetY(25);
        $this->SetFont('helvetica', 'B', 24);
        $this->SetTextColor(70, 70, 70);
        $this->Cell(0, 15, 'Ordem de Serviço', 0, true, 'C');
        
        $this->Ln(20);
    }
}

$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8');

$pdf->SetCreator('WR Eletrônica');
$pdf->SetAuthor('WR Eletrônica');
$pdf->SetTitle('Ordem de Serviço #' . $id);

// Ajuste no conteúdo principal
$pdf->AddPage();
$pdf->Ln(15); // Reduzido de 30 para 15

// Cabeçalho da seção cliente com estilo moderno
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 12, '  Dados do Cliente', 0, 1, 'L', true);
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 11);
// Dados em grid mais organizado
$pdf->Cell(47, 7, 'ID Cliente:', 0, 0);
$pdf->Cell(143, 7, str_pad($data['id_cliente'], 5, '0', STR_PAD_LEFT), 0, 1); // Formatado com zeros à esquerda

$pdf->Cell(47, 7, 'Nome:', 0, 0);
$pdf->Cell(143, 7, $data['nome'], 0, 1);

$pdf->Cell(47, 7, 'Telefone:', 0, 0);
$pdf->Cell(143, 7, $data['telefone'], 0, 1);

$pdf->Cell(47, 7, 'Email:', 0, 0);
$pdf->Cell(143, 7, $data['email'], 0, 1);

$pdf->Cell(47, 7, 'Endereço:', 0, 0);
$pdf->Cell(143, 7, $data['endereco'], 0, 1);
$pdf->Ln(15);

// Informações do Serviço com estilo
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Detalhes do Serviço', 0, 1);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 11);
// Primeira coluna
$pdf->Cell(95, 7, 'Nº OS: ' . $id, 0, 0);
$pdf->Cell(95, 7, 'Data de Entrada: ' . date('d/m/Y', strtotime($data['data_entrada'])), 0, 1);
$pdf->Cell(95, 7, 'Aparelho: ' . $data['tipo_aparelho'], 0, 0);
$pdf->Cell(95, 7, 'Marca: ' . $data['marca'], 0, 1);
$pdf->Cell(95, 7, 'Modelo: ' . $data['modelo'], 0, 0);
$pdf->Cell(95, 7, 'Número de Série: ' . $data['numero_serie'], 0, 1);
$pdf->Ln(5);
$pdf->Cell(0, 7, 'Descrição: ' . $data['descricao'], 0, 1);
$pdf->Ln(5);
$pdf->Cell(95, 7, 'Data de Entrada: ' . date('d/m/Y', strtotime($data['data_entrada'])), 0, 0);
$pdf->Cell(95, 7, 'Prazo: ' . date('d/m/Y', strtotime($data['data_previsao'])), 0, 1);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Valor: R$ ' . number_format($data['valor'], 2, ',', '.'), 0, 1);

// Termos e Condições com estilo melhorado
$pdf->Ln(15);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Termos e Condições', 0, 1);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 10);
$pdf->MultiCell(0, 5, 'POLÍTICA DE ABANDONO: Após a entrega do orçamento ou conclusão do serviço, o cliente será notificado para manifestar interesse ou retirar o equipamento. Caso o aparelho não seja retirado no prazo de 90 (noventa) dias, será considerado abandonado, independentemente da execução ou não do serviço. Neste caso, a WR Eletrônica se reserva o direito de dar a destinação que julgar apropriada ao equipamento, incluindo descarte, sem direito a qualquer tipo de indenização ao cliente.', 0, 'J');
$pdf->Ln(5);
$pdf->MultiCell(0, 5, 'O cliente declara estar ciente e de acordo com os termos acima descritos.', 0, 'J');

// Área de assinaturas mais elegante
$pdf->Ln(25);
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(95, 7, 'Cliente: _________________________', 0, 0, 'C');
$pdf->Cell(95, 7, 'Técnico: _________________________', 0, 1, 'C');
$pdf->Ln(10); // Reduzido de 15 para 10
$pdf->Cell(95, 7, 'Data: ____/____/________', 0, 0, 'C');
$pdf->Cell(95, 7, 'Data: ____/____/________', 0, 1, 'C');

$pdf->Output('ordem_servico_' . $id . '.pdf', 'I');