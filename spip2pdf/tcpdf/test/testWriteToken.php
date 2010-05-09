<?php 
require_once('../pdfLibForSpip.php');
$pdf = new PDF_FOR_SPIP(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);

$pdf->debug = false;

$pdf->setProperties(PDF_CREATOR, PDF_AUTHOR, PDF_TITLE, PDF_SUBJECT, PDF_KEYWORDS);

$pdf->setHeaderProperties(20,0);

$pdf->setHeaderLogo(
						PDF_HEADER_LOGO,
						'left',
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
$pdf->SetMargins(30, 80, 30);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setLanguageArray($l); //set language items
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//content 
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->SetFont("tahoma");


//initialize document
$pdf->AliasNbPages();

$pdf->AddPage();
$token = array();
$token['type']="li";
$token['content']="a li thing";
$pdf->writeLi($token,"justify");




$pdf->AddPage();
$token = array();
$token['type']="plain";
$token['content']="A rather complete text with enough content to generate a line break, hope that will work hey man !! Cet article est un article en français qui a pour but de tester le rendu d’un pdf en français avec spip. Tous les ans en inde se tient le dernier marché des éléphants de l’Inde, 200 a 300 éléphants venus de toute l’inde y sont vendus chaque année. c’est aussi une fête religieuse importante la victoire de l’éléphant sur le crocodile, c’est aussi un des lieux de l’inde ou se font des exorcismes, ou les travestis dansent avec les enfants, ou il y a des crémations au son du tambour, c’est aussi un immense marché aux bovidés et aux chevaux de l’inde. un endroit du bout du Monde en Inde profonde avec une authenticité séculaire, c ’est l’Inde insolite , l’inde religieuse et l’inde païenne et le seul marché actuel des éléphants de l’inde. Un marché qui dure 4 jours pour les éléphnats et un mois pour les autres animaux.";
$pdf->writePlain($token,"justify");

$token['type']="li";
$token['content']="a li 2 thing";
$pdf->writeLi($token,"justify");

$token['type']="li";
$token['content']="a li 3 thing";
$pdf->writeLi($token,"justify");

$token = array();
$token['type']="h3";
$token['content']="a h3 thing";
$pdf->writeH3($token,"center");

$token = array();
$token['type']="plain";
$token['content']="A rather complete text with enough content to generate a line break, hope that will work hey man !! Cet article est un article en français qui a pour but de tester le rendu d’un pdf en français avec spip. Tous les ans en inde se tient le dernier marché des éléphants de l’Inde, 200 a 300 éléphants venus de toute l’inde y sont vendus chaque année. c’est aussi une fête religieuse importante la victoire de l’éléphant sur le crocodile, c’est aussi un des lieux de l’inde ou se font des exorcismes, ou les travestis dansent avec les enfants, ou il y a des crémations au son du tambour, c’est aussi un immense marché aux bovidés et aux chevaux de l’inde. un endroit du bout du Monde en Inde profonde avec une authenticité séculaire, c ’est l’Inde insolite , l’inde religieuse et l’inde païenne et le seul marché actuel des éléphants de l’inde. Un marché qui dure 4 jours pour les éléphnats et un mois pour les autres animaux.";
$pdf->writePlain($token,"justify");

$pdf->AddPage();
$token = array();
$token['type']="h3";
$token['content']="a h3 thing";
$pdf->writeH3($token,"center");

$pdf->AddPage();
$token = array();
$token['type']="img";
$token['src']="http://localhost/spip/local/cache-vignettes/L368xH278/elephant-5a610.jpg";
$pdf->writeImage($token);
	

//Close and output PDF document
$pdf->Output();

?>