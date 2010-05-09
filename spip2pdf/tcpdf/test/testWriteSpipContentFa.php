<?php 
require_once('../pdfLibForSpip.php');
$pdf = new PDF_FOR_SPIP(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);


$pdf->debug = false;

$pdf->setProperties(PDF_CREATOR, PDF_AUTHOR, PDF_TITLE, PDF_SUBJECT, PDF_KEYWORDS);

$pdf->setHeaderProperties(20,0);

$pdf->setHeaderLogo(
						PDF_HEADER_LOGO,
						'right',
						50);

$pdf->setHeaderTitle(
						"Header title",
						PDF_FONT_NAME_MAIN,
						'',
						'#0006ff',
						15,
						'left');
						
$pdf->setHeaderString(
						"Header string",
						PDF_FONT_NAME_MAIN,
						'',
						'#ff007e',
						12,
						'left');

//content
//set margins  (left, top, right)  
$pdf->SetMargins(40, 75, 40);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setLanguageArray($l); //set language items
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//content 
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->SetFont("times");
$pdf->setRTL(true);
$pdf->multiColumn(4,2);

//initialize document
$pdf->AliasNbPages();
$pdf->AddPage();

$content = file_get_contents("text_fa.txt",false);

$pdf->writeSpipContent($content,"justify");




//Close and output PDF document
$pdf->Output();

?>