<?php
require('vendor/fpdf/fpdf.php');

class PDF extends FPDF
{
    // Variable para almacenar total de páginas
    var $totalPages;

    function AliasNbPages($alias = '{nb}')
    {
        parent::AliasNbPages($alias);
        $this->totalPages = 0; // inicializar
    }

    function Header()
    {
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.1);
        $this->Rect(22, 25, $this->w - 35, $this->h - 40);
        $this->SetFont('Arial','B',12);
        $this->SetXY(10, 15);
        $this->Cell(50, 10, 'Cuadro visible', 0, 0, 'C');
    }

    // Sobrescribir método Footer para dibujar cuadro solo en última página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');

        // Solo dibujar cuadro si es la última página
        if ($this->PageNo() == $this->totalPages) {
            $this->SetDrawColor(0,0,0);
            $this->SetLineWidth(0.1);
            $this->SetFillColor(232, 228, 227);
            /*primer cuadro*/
            $this->Rect(22, 212, 60, 10, 'FD');
            $this->SetXY(40, 60);
            $this->SetFont('Arial', '', 10);              
            $this->Ln(150);              
            $this->Cell(60, 10, 'Nombre y C.I. del Responsable', 0, 0, 'C', false);



            $this->Rect(22, 222, 60, 20);
            $this->SetXY(40, 60);
            $this->Rect(22, 242, 60, 7.4, 'FD');
            $this->SetXY(40, 60);
            $this->Rect(22, 249.4, 60, 15);
            $this->SetXY(40, 60);
            /*segundo cuadro*/
            $this->Rect(82, 212, 60, 10, 'FD');
            $this->SetXY(40, 60);
            $this->Rect(82, 222, 60, 20);
            $this->SetXY(40, 60);
            $this->Rect(82, 242, 60, 7.4, 'FD');
            $this->SetXY(40, 60);
            $this->Rect(82, 249.4, 60, 15);
            $this->SetXY(40, 60);
            /*tercer cuadro*/
            $this->Rect(142, 212, 61, 10, 'FD');
            $this->SetXY(40, 60);
            $this->Rect(142, 222, 61, 20);
            $this->SetXY(40, 60);
            $this->Rect(142, 242, 61, 7.4, 'FD');
            $this->SetXY(40, 60);
            $this->Rect(142, 249.4, 61, 15);
            $this->SetXY(40, 60);

            $this->SetFont('Arial','B',14);
            $this->Cell(130, 10, 'Cuadro en la ultima pagina', 0, 0, 'C');
        }
    }

    // Sobrescribir Output para capturar total de páginas antes de enviar
    function Output($dest = '', $name = 'poder.pdf', $isUTF8 = true)
    {
        // Guardar total de páginas antes de enviar el PDF
        $this->totalPages = $this->PageNo();
        parent::Output($dest, $name, $isUTF8);
    }
}

$pdf = new PDF('P', 'mm', array(215.9, 279.4));
$pdf->AliasNbPages();

$limite_vertical = 180;
for ($i = 1; $i <= 3; $i++) {
    $pdf->AddPage();
    $pdf->SetFont('Arial','',12);
    
    // Si la posición actual Y es mayor o igual al límite, agregar página nueva
    if ($pdf->GetY() >= $limite_vertical) {
        $pdf->AddPage();
    }

    // Imprimir línea o texto
    $pdf->Cell(0, 10, "Línea número $i", 0, 1);
}
/*
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,'Contenido de prueba',0,1);*/
$pdf->Output();

?>