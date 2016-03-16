<?php
require_once('fpdf.php');
require_once('fpdi.php');

$pdf =& new FPDI();
$pdf->AddPage();

//Set the source PDF file
$pagecount = $pdf->setSourceFile('test.pdf');

//Import the first page of the file
$tppl = $pdf->importPage(1);
//$tppl = $pdf->importPage();
//SetAutoPageBreak

//Use this page as template
// use the imported page and place it at point 20,30 with a width of 170 mm
//$pdf->useTemplate($tppl, -10, 20, 210);
//$size1 = $pdf->getTemplateSize($tppl,0,0);
//var_dump($size1);
$size2 = $pdf->useTemplate($tppl,NULL,NULL,0,0,TRUE); //TRUE set theo kich thuoc ban dau
//var_dump($size2);

#Print Hello World at the bottom of the page

//Select Arial italic 8
/*$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);
$pdf->SetXY(90, 160);*/

$height = $pdf->GetPageHeight();
$width	= $pdf->GetPageWidth();
$pdf->Image('logo.jpg',$width/2,$height/2,30,30);//x,y,width,height
/*$pdf->Image(‘Om.jpg’,65,220,15,10);
$pdf->Image(‘think.jpg’,80,220,15,10);
$pdf->Image(‘Om.jpg’,140,240,15,10);*/

//$pdf->Write(0, “Hello World”);

$pdf->Output('modified_pdf.pdf', 'F');
?>