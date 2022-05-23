<?php
    //require_once ('file:///C:/xampp/htdocs/netixperu_demo/application/third_party/netix_fpdf/fpdf.php');
require_once APPPATH."/third_party/netix_fpdf/fpdf.php";
?>
<?php

class PDF extends FPDF{
// Cabecera de página
    function Header() {
        // Logo
      // $this->Image('file:///C:/xampp/htdocs/netixperu_demo/public/img/netix_logo2.png',10,10,-300);
        // Arial bold 15
        $this->SetFont('Times','B',18);
        // Movernos a la derecha
        $this->ln(10);
        $this->Cell(60);
        // Título
        $this->Cell(90,15,utf8_decode('PROFORMA DE RECEPCIÓN'),1,0,'C');
        // Salto de línea
        $this->Ln(15);
        $this->Image('./public/img/netix_logo2.png', 40, 20, '');
        $this->SetFont('Times','',7);
        $this->Cell(80,3,utf8_decode('SISTEMA COMERCIAL'),0,0,'C');
        $this->Ln(3);
        $this->Cell(80,3,utf8_decode(' NETIX PERÚ'),0,0,'C');
        $this->Ln(10);
    }

// Pie de página
    function Footer(){
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,utf8_decode("SISTEMA COMERCIAL NETIX PERÚ").' / PAGINA '.$this->PageNo(),0,0,'C');
    }
}

// Creación del objeto de la clase heredada
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Times','',12);
    $pdf->Cell(0,10,utf8_decode('CLIENTE: '.$recepcion[0]['nombrepersona']));
    $pdf->ln(10);
    $pdf->Cell(0,10,utf8_decode('EMPLEADO: '.$recepcion[0]['nombreempleado']));
    $pdf->ln(10);
    $pdf->Cell(0,10,utf8_decode('PRODUCTO: '.$recepcion[0]['producto']));
    $pdf->ln(10);
    $pdf->Cell(0,10,utf8_decode('MARCA: '.$recepcion[0]['marca']));
    $pdf->ln(10);
    $pdf->Cell(0,10,utf8_decode('MODELO: '.$recepcion[0]['modelo']));
    $pdf->ln(10);
    $pdf->Cell(0,10,utf8_decode('DESCRIPCION: '.$recepcion[0]['descripcion']));
    $pdf->ln(10);
    $pdf->Cell(0,10,utf8_decode('FECHA: '.$recepcion[0]['fecharecepcion']));
    $pdf->ln(10);
    $pdf->Cell(0,10,utf8_decode('TIPO DE PAGO: '.$recepcion[0]['tipopago']));
    $pdf->ln(10);
    $pdf->Cell(0,10,utf8_decode('IMPORTE S/: '.$recepcion[0]['importe']));

    $pdf->Output();
?>