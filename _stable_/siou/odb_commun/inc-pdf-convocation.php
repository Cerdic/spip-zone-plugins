<?php
include_once('inc-html.php');
include_once('inc-odb.php');
$tBddConf=getBddConf();
//print_r($tBddConf);
mysql_connect($tBddConf['host'], $tBddConf['user'], $tBddConf['pass']) or die('Connection impossible<br/>'.mysql_error());
mysql_select_db($tBddConf['bdd']) or die('Base inaccessible<br/>'.mysql_error());

$where='';
$pdfG='convocations';
$annee=$_GET['annee'];
if($annee=='') $annee=date('Y');
$limit=$_GET['limit'];
if($limit=='') $limit=0;
$id_etablissement=$_GET['id_etablissement'];
if(strlen($id_etablissement)>0) {
	$where.=" AND can.etablissement='$id_etablissement'";
	//$tRefEta=getReferentiel('etablissement');
	$pdfG.="_$id_etablissement";
}
$serie=$_GET['serie'];
if(strlen($serie)>0) {
	$where.=" AND ser.serie='$serie'";
	//$tRefSerie=getReferentiel('serie');
	$pdfG.="_$serie";
}
$id_departement=$_GET['id_departement'];
$departement=$_GET['departement'];
if(strlen($departement)>0) {
	$where.=" AND can.departement='$id_departement'";
	//$tRefSerie=getReferentiel('serie');
	$pdfG.='_'.getRewriteString($departement);
}
$sql = 'SELECT can.`id_table`, can.id_table_old, sex.`sexe`, pre.`prefixe`, can.`nom`,can.`prenoms`, cen.etablissement centre, ser.serie, lv1.lv lv1, lv2.lv lv2, ef1.ef ef1, ef2.ef ef2, eps.eps'
        . ' FROM odb_ref_sexe sex, odb_repartition rep, odb_ref_etablissement cen,'
        . ' odb_ref_serie ser, odb_ref_eps eps,'
        . ' odb_candidats can'
        . ' left join odb_ref_prefixe pre on can.prefixe=pre.id'
        . ' left join odb_ref_lv lv1 on lv1.id=can.lv1'
        . ' left join odb_ref_lv lv2 on lv2.id=can.lv2'
        . ' left join odb_ref_ef ef1 on ef1.id=can.ef1'
        . ' left join odb_ref_ef ef2 on ef2.id=can.ef2'
        . ' where can.sexe=sex.id and can.serie=ser.id and eps.id=can.eps'
        . ' and rep.id_table=can.id_table and rep.id_etablissement=cen.id'
        . " and can.annee=$annee and rep.annee=$annee $where"
        . " order by can.etablissement, ser.serie, can.nom, can.prenoms"
        . " LIMIT $limit, 300"
        ;

//die($sql);
$result=odb_query($sql,__FILE__,__LINE__);
$pdf_dir='../contrib/ezpdf/';
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
setlocale(LC_TIME, 'fr_FR','fr_BE.UTF8','fr_FR.UTF8');
//$pdf->ezStartPageNumbers(70, 20, 8, 'left', "$title - Page {PAGENUM} sur {TOTALPAGENUM}");
$colonnes=array('id_table','id_table_old','sexe','prefixe','nom','prenoms','centre','serie','lv1','lv2','ef1','ef2','eps');
$sDateConvoc=html_entity_decode(utf8_decode(strftime("%A %d %B %Y", mktime(0, 0, 0, date('m'), date('d'), date('Y')))));
while($row=mysql_fetch_array($result)) {
   foreach($colonnes as $col) $$col=utf8_decode(stripslashes($row[$col]));
   $id_table=getIdTableHumain($id_table);
   if($sexe=='M') {
      $civ='M.';
      $e='';
   }
   else {
      $civ='Mlle';
      $e='e'; // pour mettre des mots au fminin si c'est une candidate
   }
   $tTmp=explode('-',$id_table);
   $salle=$tTmp[1];
   $salle=$salle[0].(int)substr($salle,1);
   $place=(int)$tTmp[2];
   $prefixe=strtolower($prefixe);
   $candidat="$civ $prefixe <b>$nom</b> $prenoms";
   if(!is_array($tCalendrier[$serie])) {
      $sql2="SELECT matiere, examen date, type, duree\n"
         . " FROM odb_ref_examen exa, odb_ref_serie ser, odb_ref_matiere mat\n"
         . " where id_serie=ser.id and id_matiere=mat.id and serie='$serie' and exa.annee=$annee\n"
         . " order by serie, date, matiere"
         ;
      $result2=odb_query($sql2,__FILE__,__LINE__);
      while($row2=mysql_fetch_array($result2)) {
         foreach(array('matiere','date', 'duree','type') as $col) $$col=$row2[$col];
         $tDateHeure=explode(" ",$date);
         $sDate=$tDateHeure[0];
         $sHeure=$tDateHeure[1];
         $tDate=explode("-",$sDate);
         $anneeM=$tDate[0];
         $mois=$tDate[1];
         $jour=$tDate[2];
         $sDateMatiere=utf8_decode(ucfirst(strftime("%A %d %B %Y", mktime(0, 0, 0, $mois, $jour, $anneeM))));
         //echo "$date $jour/$mois/$anneeM : $sDateMatiere<br/>";
         
         $tHeure=explode(':',$sHeure);
         $heure=$tHeure[0];
         $minute=$tHeure[1];
         $seconde=$tHeure[2];
         
         if($type=='Oral') {
            $tCalendrier[$serie]['oral'][]=$matiere;
         } elseif ($type=='Pratique') {
            $tCalendrier[$serie]['pratique']["$sDateMatiere|$heure|$duree"][]=$matiere;
         } elseif($type=='Ecrit') {
            $tCalendrier[$serie]['ecrit'][$sDateMatiere]["$heure|$duree"]=$matiere;
         } else die(KO." - type <b>$type</b> inconnu pour la matiere <b>$matiere</b> (serie <b>$serie</b>)");
      }
   }
   $pdf->addDestination(getRewriteString($candidat),'FitH');
   if($iPage>0)
      $iPage=$pdf->newPage();
   else $iPage=1;
   $extra='';
   if($id_table_old!='') $extra.=" - anciennement $id_table_old";
   $sommaire.="<c:ilink:".getRewriteString($candidat).">".str_pad($id_table,13,'0',STR_PAD_LEFT)." - $candidat ($centre, $serie)".$extra."</c:ilink>\n";
   $pdf->rectangle(20,20,$width-35,$height-40);
   $pdf->ezSetY($height-25);
   $pdf->ezText("Cotonou, $sDateConvoc\n",10,array('justification'=>'right'));
   $convocation=html_entity_decode("<b>Convocation</b> &agrave; l'examen du baccalaur&eacute;at \nSession unique de juin $annee");
   $pdf->ezText($convocation,12,array('justification'=>'right'));

   $pdf->ezSetY($height-25);
   $adresse=html_entity_decode("R&eacute;publique du B&eacute;nin\n\nMinist&egrave;re de l'Enseignement Sup&eacute;rieur\net de la Recherche Scientifique\n\nOffice du Baccalaur&eacute;at\n\n03 BP 1525 Cotonou");
   $pdf->ezText($adresse,8);
   
   $pdf->rectangle(25,$height-175,$width-45,60);
   $pdf->ezSetY($height-120);
   $candidat=$candidat.html_entity_decode(", inscrit$e sous le num&eacute;ro <b>$id_table</b>, est invit&eacute;$e &agrave; se pr&eacute;senter au centre <b>$centre</b> dans la salle <b>$salle</b> &agrave; la place <b>$place</b>, aux jours et heures indiqu&eacute;s ci-dessous pour subir les &eacute;preuves du baccalaur&eacute;at s&eacute;rie <b>$serie</b>.");
   $pdf->ezText($candidat,12,array('justification'=>'full'));

   $pdf->rectangle(25,250,$width-45,400);
   $pdf->line(25,620,$width-25,620);
   $pdf->line(round($width/2,0),650,round($width/2,0),250);
   $pdf->ezSetY(645);
   $pdf->ezColumnsStart(array('num'=>2,'gap'=>10));
   if(is_array($tCalendrier[$serie]['pratique'])) {
      ////////////// EPREUVES PRATIQUES
      $pdf->ezText(html_entity_decode("&Eacute;preuves pratiques"),12,array('justification'=>'center'));
      foreach($tCalendrier[$serie]['pratique'] as $jourHeureDuree=>$tTmp) {
      	list($jour,$heure,$duree)=explode('|',$jourHeureDuree);
			$reste=$duree-floor($duree);
			$fin=($heure+$duree-$reste)."h";
			if($reste>0) $fin.=(string)($reste*60);
			$pdf->ezText(html_entity_decode("\n&Agrave; partir du $jour, de ".$heure."h &agrave; $fin"),12);
			foreach($tTmp as $matiere) {
				$pdf->ezText(html_entity_decode("\t- $matiere"),11);
			}
      }
      $pdf->ezSetY(520);
      $pdf->rectangle(25,495,round($width/2,0)-25,30);
   }
   $pdf->ezText(html_entity_decode("&Eacute;preuves &eacute;crites"),12,array('justification'=>'center'));
   if(is_array($tCalendrier[$serie]['ecrit']))
      /////// EPREUVES ECRITES
      foreach($tCalendrier[$serie]['ecrit'] as $jour=>$tTmp) {
         $pdf->ezText(html_entity_decode("\n- $jour :"),12);
         if(!is_array($tTmp)) {
         	echo "$serie - $jour<pre>";
         	print_r($tCalendrier[$serie]);
         	die("</pre>");
			}
         foreach($tTmp as $heureDuree=>$matiere) {
            list($heure,$duree)=explode('|',$heureDuree);
            $reste=$duree-floor($duree);
            $fin=($heure+$duree-$reste)."h";
            if($reste>0) $fin.=(string)($reste*60);
            if(strtolower($matiere)=='lv1') $matiere.=" ($lv1)";
            elseif(strtolower($matiere)=='lv2') $matiere.=" ($lv2)";
            $pdf->ezText(html_entity_decode("\t De ".$heure."h &agrave; $fin : $matiere"),11);
         }
      }
   $pdf->ezNewPage();
   $pdf->ezText(html_entity_decode("&Eacute;preuves orales + E.P.S.\n"),12,array('justification'=>'center'));
   $pdf->ezText(html_entity_decode("En cas d'admissibilit&eacute; vous passerez les &eacute;preurves d'EPS et les &eacute;preuves facultatives si vous les avez choisies.\n"),8,array('justification'=>'full'));
   $pdf->ezText(html_entity_decode("Au cas o&ugrave; vous serez autoris&eacute;$e &agrave; passer les &eacute;preuves orales du deuxi&egrave;me groupe, vous serez interrog&eacute;$e dans les mati&egrave;res ci-apr&egrave;s :\n"),8,array('justification'=>'full'));
   if(is_array($tCalendrier[$serie]['oral']))
      ///////// ORAL
      foreach($tCalendrier[$serie]['oral'] as $matiere) {
         if(strtolower($matiere)=='lv1') $matiere.=" ($lv1)";
         elseif(strtolower($matiere)=='lv2') $matiere.=" ($lv2)";
         $pdf->ezText(html_entity_decode("- $matiere"),11);
      }
   
   if($eps=='Apte') $sEPS="Vous &ecirc;tes apte &agrave; l'&eacute;preuve d'E.P.S. (&Eacute;ducation physique et sportive)";
   else $sEPS="Vous &ecirc;tes dispens&eacute;$e d'E.P.S. (&Eacute;ducation physique et sportive) <b>sous r&eacute;serve de justification</b>";
   $pdf->ezText(html_entity_decode("\n$sEPS\n"),12,array('justification'=>'full'));
   
   $pdf->ezText(html_entity_decode("Les lieu, date et heure de composition de ces &eacute;preuves vous seront communiqu&eacute;s ult&eacute;rieurement."),8,array('justification'=>'full'));
   $pdf->ezColumnsStop();

   if($ef1!='' || $ef2!='') { // epreuves facultatives
      $pdf->ezSetY(240);
      if(trim($ef1)=='' || trim($ef2)=='') $s='';
      else $s='s';
      $pdf->ezText(html_entity_decode("<b>&Eacute;preuve$s facultative$s</b>"),12,array('justification'=>'center'));
      $pdf->ezText(html_entity_decode("\n$ef1\n$ef2"),12,array('justification'=>'center'));
      $pdf->rectangle(150,150,$width-300,100);
   }
   $pdf->ezSetY(55);
   $pdf->ezText(html_entity_decode("Veuillez consulter les recommandations pr&eacute;vues pour le bon d&eacute;roulement du baccalaur&eacute;at au verso de cette convocation."),10,array('justification'=>'full'));
}

$pdf->ezInsertMode(1,1,'before');
$pdf->ezNewPage();
$pdf->ezText("Sommaire des convocations\n",12);
$pdf->ezText($sommaire,8);
$pdf->ezInsertMode(0);

$title="Convocations au bac";
$infos=array('Title'=>$title,'Author'=>'SIOU / Office du Bac','CreationDate'=>date("d/m/Y"));
$pdf->addInfo($infos);

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
