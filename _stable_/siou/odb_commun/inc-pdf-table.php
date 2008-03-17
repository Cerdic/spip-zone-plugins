<?php
session_start();
if(isset($_GET['reset'])) session_destroy();
//on rcupere le tableau  afficher, $data, dans la session
//tableau de la forme $data[$num_row]=$row
$pdfG=$_GET['pdf'];
if($pdfG=='') die('Veuillez pr&eacute;ciser le nom du tableau pdf &agrave; g&eacute;n&eacute;rer');
$data=$_SESSION['data'][$pdfG];
$title=$_SESSION['titre'][$pdfG];	// titre au-dessus du tableau
$pied=$_SESSION['pied'][$pdfG];		// pied de page, rpt en bas de chaque page
if($pied=='') $pied=$title;
$cols=$_SESSION['cols'][$pdfG];		// colonnes  inclure dans la table
$format=$_SESSION['format'][$pdfG];	// format du fichier pdf (par dfaut A3 paysage)
if(!is_array($format)) $format=array('taille'=>'A3', 'orientation'=>'landscape');
$options=$_SESSION['options'][$pdfG];	// options (facultatives) du tableau 
if(!is_array($option))
	if($format['taille']=='A4' && $format['orientation']!='landscape')
		$options=array('maxWidth'=>575);
$infos=$_SESSION['infos'][$pdfG];	// informations du fichier pdf (titre, auteur, date creation...)
if(!is_array($infos)) $infos=array('Title'=>$title,'Author'=>'SIS/DOB via SIOU','CreationDate'=>date("d/m/Y"));
$encryption=$_SESSION['encryption'][$pdfG];	// protection par mot de passe 
$post=$_SESSION['post'][$pdfG];		// texte a afficher a la fin du document pdf

//error_reporting(E_WARNING);

//echo "<pre>";print_r($_SESSION);die('</pre>');

$debug=false;
if($debug) {
	echo($pdfG);
	//print_r($_SESSION);
	foreach(array('title','pied','cols','options','infos','format','encryption','data') as $var) {
		echo "<hr size='1'/><pre><b>$var</b> ";print_r($$var);echo "</pre>\n";
	}
	die(OK.' - fin du debug');
}

$pdf_dir='../odb_contrib/ezpdf/';
include $pdf_dir.'class.ezpdf.php';	// inclusion du code de la bibliothque
$pdf =& new Cezpdf($format['taille'],$format['orientation']);
$pdf->selectFont($pdf_dir.'fonts/Helvetica');
$pdf->ezStartPageNumbers(12, 12, 8, 'right', "$pied - Page {PAGENUM}/{TOTALPAGENUM} - SIS/DOB");
$pdf->addInfo($infos);

// make the table
$pdf->ezTable($data,$cols,$title,$options);
$pdf->ezSetDy(-10);
$pdf->ezText($post);
// do the output, this is my standard testing output code, adding ?d=1
// to the url puts the pdf code to the screen in raw form, good for
// checking
// for parse errors before you actually try to generate the pdf file.
if(isset($_GET['d'])) $d=$_GET['d'];
if (isset($d) && $d){
   $pdfcode = $pdf->output(1);
   $pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
   echo '<html><body>';
   echo trim($pdfcode);
   echo '</body></html>';
} else {
   if(is_array($encryption)) $pdf->setEncryption($encryption['user'], $encryption['owner'], $encryption['action']);
//   $pdf->ezStream();


   $pdfcode = $pdf->ezOutput();
   $pdf_fic="pdf/siou-".date('Y')."_$pdfG.pdf";
   $fp=fopen($pdf_fic,'wb');
   fwrite($fp,$pdfcode);
   fclose($fp);

	echo "<A HREF='$pdf_fic'>Cliquez ici pour lire le fichier $pdf_fic</a>\n";
	echo "<script>document.location='$pdf_fic'</script>";
   //session_destroy();
}

?>
