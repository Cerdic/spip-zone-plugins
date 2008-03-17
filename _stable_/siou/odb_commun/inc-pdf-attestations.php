<?php
include_once('inc-html.php');
include_once('inc-odb.php');
$tBddConf=getBddConf();

mysql_connect($tBddConf['host'], $tBddConf['user'], $tBddConf['pass']) or die('Connection impossible<br/>'.mysql_error());
mysql_select_db($tBddConf['bdd']) or die('Base inaccessible<br/>'.mysql_error());

$where='';
$pdfG='attestations_';
$groupe=$_REQUEST['groupe'];
if($groupe==2) {
	$andDelib = " and delib1='Admissible' and delib2 = 'Oral' and delib3 = 'Passable'";
	$titre="Attestations 2&egrave;me groupe";
	$pdfG.=getRewriteString("groupe2");
} elseif($groupe==1) {
	$andDelib = " and delib1='Admissible' and delib2 != 'Oral' and delib2 != 'Reserve'";
	$titre="Attestations 1er groupe";
	$pdfG.=getRewriteString("groupe1");
} else die((KO." - Numero de groupe [$groupe] incorrect<br/>".__FILE__));
$deliberation=$groupe+1;
$annee=$_REQUEST['annee'];
if($annee=='') $annee=date('Y');
$limit=$_REQUEST['limit'];
if($limit=='') $limit=0;
$jury=$_REQUEST['jury'];
if($jury>0) {
	$andDelib.=" AND jury='$jury'";
	//$tRefEta=getReferentiel('etablissement');
	$pdfG.='_'.getRewriteString("jury $jury");
	$titre.=" jury";
}
$date_delib=trim($_REQUEST['date_delib']);
$tTmp=explode('/',$date_delib);
$jour=(int)$tTmp[0];
$mois=(int)$tTmp[1];
$annee=(int)$tTmp[2];
if(!checkdate($mois,$jour,$annee)) die("<b>[KO]</b> La date <b>$date_delib</b> est incorrecte, veuillez recommencer.");
setlocale(LC_TIME, 'fr_FR','fr_BE.UTF8','fr_FR.UTF8');
$sDateDelib=html_entity_decode(utf8_decode(strftime("%A %d %B %Y", mktime(0, 0, 0, $mois, $jour, $annee))));
$date_attestation=trim($_REQUEST['date_attestation']);
$tTmp=explode('/',$date_attestation);
$jour=(int)$tTmp[0];
$mois=(int)$tTmp[1];
$annee=(int)$tTmp[2];
if(!checkdate($mois,$jour,$annee)) die("<b>[KO]</b> La date <b>$date_attestation</b> est incorrecte, veuillez recommencer.");
setlocale(LC_TIME, 'fr_FR','fr_BE.UTF8','fr_FR.UTF8');
$sDateAttestation=html_entity_decode(utf8_decode(strftime("%A %d %B %Y", mktime(0, 0, 0, $mois, $jour, $annee))));
//die($sDateConvoc);

$sql = "select can.id_table, sex.sexe, pre.prefixe, nom, prenoms, ldn, pays, if(ne_le='0000-00-00',if(ne_en='0000',concat('vers ',ne_vers),concat('en ',ne_en)),DATE_FORMAT(ne_le, 'le %d/%m/%Y')) ddn, delib$deliberation delib, moyenne, ser.serie, ser.libelle libSerie, jury, eta.etablissement centre\n"
     . " from odb_decisions decis, odb_ref_etablissement eta, odb_ref_pays pays, odb_ref_sexe sex, odb_ref_serie ser, odb_repartition rep, odb_candidats can\n"
     . " left join odb_ref_prefixe pre on pre.id=can.prefixe\n"
     . " where rep.id_table=can.id_table and decis.id_table=rep.id_table\n"
     . " and decis.annee=$annee and can.annee=$annee and rep.annee=$annee\n"
     . ' and sex.id=can.sexe and can.serie=ser.id and can.nationalite=pays.id and rep.id_etablissement=eta.id '
     . "$andDelib\n"
     . " order by jury, ser.serie, id_table".
       " limit $limit, 2000";
//die("<pre>$sql");
$result=odb_query($sql,__FILE__,__LINE__);

$pdf_dir='contrib/ezpdf/';
include $pdf_dir.'class.ezpdf.php';	// inclusion du code de la bibliothque
$pdf =& new Cezpdf('A4','portrait'); // 595.28 x 841.89
$pdf->selectFont($pdf_dir.'fonts/Helvetica.afm');
$options=array('b'=>'Helvetica-Bold.afm');
$family='Helvetica';
$pdf->setFontFamily($family,$options);
$pdf->setStrokeColor(0,0,0);
$pdf->setLineStyle(1,'round','round');
$width=$pdf->ez['pageWidth'];
$height=$pdf->ez['pageHeight'];
$colonnes=array('id_table','sexe','prefixe','nom','prenoms','ldn','pays','ddn','delib','moyenne','serie','libSerie','jury','centre');
$colonnes2=array('id_table','candidat','ldn','ddn','delib','moyenne','serie','libSerie','jury','centre');
while($row=mysql_fetch_array($result)) {
   foreach($colonnes as $col) $$col=utf8_decode(stripslashes($row[$col]));
   if($sexe=='M') {
      $civ='M.';
      $e='';
   }
   else {
      $civ='Mlle';
      $e='e'; // pour mettre des mots au feminin si c'est une candidate
   }
   $prefixe=strtolower($prefixe);
   $candidat="$civ $prefixe <b>$nom</b> $prenoms";
   if(supprimeAccents(utf8_encode($pays))!='Benin') $ldn.=" ($pays)";
   
   $delib=ucfirst(str_replace('abien','Assez bien',str_replace('tbien','Tr&egrave;s bien',strtolower($delib))));
   if($delib=='Passable') {
   	if($groupe==1) $eme='er';
   	else $eme='&egrave;me';
   	$delib="<b>$delib</b> ($groupe$eme groupe)";
   }
   else $delib="<b>$delib</b>";
   
   foreach($colonnes2 as $col) $tAttestation[$jury][$serie][$id_table][$col]=html_entity_decode($$col);
   
}
ksort($tAttestation);
foreach($tAttestation as $jury=>$t1) {
	ksort($t1);
	foreach($t1 as $serie=>$t2) {
		ksort($t2);
		foreach($t2 as $id_table=>$t3) {
			foreach($colonnes2 as $col) $$col=$t3[$col];
		   $pdf->addDestination(getRewriteString($candidat),'FitH');
		   if($iPage>0)
		   $iPage=$pdf->newPage();
		   else $iPage=1;
		   $extra='';
		   $sommaire.=html_entity_decode("<c:ilink:".getRewriteString($candidat).">".str_pad($id_table,13,'0',STR_PAD_LEFT)." - jury $jury, s&eacute;rie $serie - $candidat ($delib)</c:ilink>\n");

		   $pdf->addText(420,700,20,"<b>$serie</b>");
		   $pdf->addText(350,680,14,$libSerie);
		   
		   $pdf->addText(160,550,14,$candidat);
		   $pdf->addText(160,525,14,$ldn);
		   $pdf->addText(160,500,14,$ddn);
		   
		   $pdf->addText(400,410,20,"<b>$serie</b>");
		   $pdf->addText(120,390,14,$sDateDelib); $pdf->addText(400,390,14,$centre);
		   $pdf->addText(170,365,13,$id_table);   $pdf->addText(350,365,14,$delib);
		   
		   $pdf->addText(370,340,14,$sDateAttestation);
		   
		   //$pdf->ezSetY(800);
		   //foreach($colonnes2 as $col) $pdf->ezText(html_entity_decode("$col ".$$col));
		}
	}
}


$pdf->ezInsertMode(1,1,'before');
$pdf->ezNewPage();
$pdf->ezText(html_entity_decode($titre),12);
$pdf->ezText($sommaire,8);
$pdf->ezInsertMode(0);

$infos=array('Title'=>html_entity_decode($titre),'Author'=>'SIOU / Office du Bac','CreationDate'=>date("d/m/Y"));
$pdf->addInfo($infos);

// do the output, this is my standard testing output code, adding ?d=1
// to the url puts the pdf code to the screen in raw form, good for
// checking
// for parse errors before you actually try to generate the pdf file.
if(isset($_REQUEST['d'])) $d=$_REQUEST['d'];
if (isset($d) && $d){
   $pdfcode = $pdf->output(1);
   $pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
   echo '<html><body>';
   echo trim($pdfcode);
   echo '</body></html>';
} else {
   //$pdf->ezStream();

   $pdfcode = $pdf->ezOutput();
   $pdf_fic="pdf/siou-".date('Y')."_$pdfG.pdf";
   $fp=fopen($pdf_fic,'wb');
   fwrite($fp,$pdfcode);
   fclose($fp);

	echo "<A HREF='$pdf_fic'>Cliquez ici pour lire le fichier $pdf_fic</a>\n";
	echo "<script>document.location='$pdf_fic'</script>";  
}
?>
