<?php
session_start();
include('inc-odb.php');
$tBddConf=getBddConf();
mysql_connect($tBddConf['host'], $tBddConf['user'], $tBddConf['pass']) or die('Connection impossible<br/>'.mysql_error());
mysql_select_db($tBddConf['bdd']) or die('Base inaccessible<br/>'.mysql_error());
if(isset($_GET['reset'])) session_destroy();
//on rcupere le tableau  afficher, $data, dans la session
//tableau de la forme $data[$num_row]=$row
$pdfG=$_GET['pdf'];
//recupération de la variable qui identifie la requete à utiliser
$param=$_GET['param'];
if($pdfG=='') die('Veuillez pr&eacute;ciser le nom du tableau pdf &agrave; g&eacute;n&eacute;rer');
//recuperation de la requete
if(is_array($_SESSION['requete'][$param])) {
	$num=0; //numero de la requete
	foreach($_SESSION['requete'][$param] as $sql) {
		// traitement sur la requete : affichage du tableau
		$result=odb_query($sql,__FILE__,__LINE__) or die(KO." - Erreur dans la requete $sql<br/>".mysql_error());
				//recueille le nonmbre de colonne du resultat
				$field = mysql_num_fields($result );
   				      for ( $i = 0; $i < $field; $i++ ) {
       	              	$names[] = mysql_field_name($result,$i);
      				  }   
      			  	$cpt=0;
      			  	while($row=mysql_fetch_array($result)) {
      			  		foreach($names as $col){
							if($col=="id_table") $row[$col]=getIdTableHumain($row[$col]);
							/*  FIXME: Est-ce une condition obligatoire?
							if($col=="id_anonyme"){
								if((int)$id_anonyme<10000 || $id_anonyme>99999) // on vérifie que tous les numeros anonymes font bien 5 caracteres
            					die(KO." - Erreur sur le num&eacute;ro anonyme $id_anonyme ($id_table) dans le centre $centre ($departement)");
							} 
							*/
               			$data[$num][$cpt][$col]=utf8_decode($row[$col]);
						}
					$cpt++;
            		}
		$num++;
		// saut de page plus bas
	}
} else die(KO.' - Vous devez passer un tableau de requetes SQL en parametre');

//$sql=$_SESSION['requete'][$pdfG];
//echo ($sql);
            	
//////////////////////////////////////////////
$format=$_SESSION['format'][$param];	// format du fichier pdf (par dfaut A3 paysage)
if(!is_array($format)) $format=array('taille'=>'A3', 'orientation'=>'landscape');
$infos=$_SESSION['infos'][$param];	// informations du fichier pdf (titre, auteur, date creation...)
if(!is_array($infos)) $infos=array('Title'=>$title,'Author'=>'SIS/DOB via SIOU','CreationDate'=>date("d/m/Y"));
$encryption=$_SESSION['encryption'][$param];	// protection par mot de passe 
//echo"<pre>";
//print_r($_SESSION);
//die('</pre>');
$post=$_SESSION['post'][$param];		// texte a afficher a la fin du document pdf
$options=$_SESSION['options'][$param];	// options (facultatives) du tableau 
if(!is_array($option))
	if($format['taille']=='A4' && $format['orientation']!='landscape')
		$options=array('maxWidth'=>575);

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
$pdf->addInfo($infos);

// make the table
// YEDA 25 Mars 2008 : Ajout de la découpe suivant les centres
for($i=0;$i<$num;$i++){
$mondata=$data[$i];
$title=$_SESSION['titre'][$param][$i];	// titre au-dessus du tableau
$pied=$_SESSION['pied'][$param][$i];		// pied de page, rpt en bas de chaque page
if($pied=='') $pied=$title;
$cols=$_SESSION['cols'][$param][$i];		// colonnes  inclure dans la table
$pdf->ezTable($mondata,$cols,$title,$options);
$pdf->ezStartPageNumbers(12, 12, 8, 'right', "$pied - Page {PAGENUM}/{TOTALPAGENUM} - SIS/DOB");
$pdf->ezNewPage();
}

/////////////////////////
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
  // $pdf->ezStream();


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
