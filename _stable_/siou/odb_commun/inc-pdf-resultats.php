<?php
include_once('inc-html.php');
include_once('inc-odb.php');
$tBddConf=getBddConf();

mysql_connect($tBddConf['host'], $tBddConf['user'], $tBddConf['pass']) or die('Connection impossible<br/>'.mysql_error());
mysql_select_db($tBddConf['bdd']) or die('Base inaccessible<br/>'.mysql_error());

$where=''; 
$pdfG='resultats_';
$deliberation=$_REQUEST['deliberation'];
if($deliberation==3) {
	$isAdmis=true;
	$andDelib = " and delib1='Admissible' and (delib2 = 'Oral' OR delib2='Reserve') and (delib3 = 'Passable' or delib2='Reserve')";
	$titre="Admis 2&deg; groupe";
	$pdfG.=getRewriteString("repeches");
} elseif($deliberation==2) {
	$isAdmis=true;
	$andDelib = " and delib1='Admissible' and delib2 != 'Oral' and delib2 != 'Reserve' and delib2!='Refuse'";
	$titre="Admis 1&deg; groupe";
	$pdfG.=getRewriteString("admis");
}
elseif($deliberation==1) {
	$isAdmis=false; 
	$andDelib = " and (delib1 = 'Absent' or delib1 = 'Ajourne' or delib1 = 'Refuse') ";
	$titre="Non admissibles";
	$pdfG.=getRewriteString("non admissibles");
} else die((KO." - Numero de deliberation [$deliberation] incorrect<br/>".__FILE__));
$annee=$_REQUEST['annee'];
if($annee=='') $annee=date('Y');
$limit=$_REQUEST['limit'];
if($limit=='') $limit=0;
$jury=$_REQUEST['jury'];
if($jury>0) {
	$where.=" AND jury='$jury'";
	//$tRefEta=getReferentiel('etablissement');
	$pdfG.='_'.getRewriteString("jury $jury");
	$titre.=" jury $jury ($deliberation&deg; d&eacute;lib&eacute;ration)";
}
$nom_jury=html_entity_decode(utf8_decode(ucwords(trim($_REQUEST['nom_jury']))));
$lieu_jury=html_entity_decode(utf8_decode(ucwords(trim($_REQUEST['lieu_jury']))));
$date_jury=trim($_REQUEST['date_jury']);
$tTmp=explode('/',$date_jury);
$jour=(int)$tTmp[0];
$mois=(int)$tTmp[1];
$annee=(int)$tTmp[2];
if(!checkdate($mois,$jour,$annee)) die("<b>[KO]</b> La date <b>$date_jury</b> est incorrecte, veuillez recommencer.");
setlocale(LC_TIME, 'fr_FR','fr_BE.UTF8','fr_FR.UTF8');
$sDateConvoc=utf8_decode(strftime("%A %d %B %Y", mktime(0, 0, 0, $mois, $jour, $annee)));
//die($sDateConvoc);

$sql = "select can.id_table, sex.sexe, pre.prefixe, nom, prenoms, matiere, type, note, notes.coeff, delib$deliberation delib, moyenne, ser.serie\n"
     . " from odb_notes notes, odb_decisions decis, odb_ref_matiere mat, odb_ref_sexe sex, odb_ref_serie ser, odb_repartition rep, odb_candidats can\n"
     . " left join odb_ref_prefixe pre on pre.id=can.prefixe\n"
     . " where notes.id_anonyme=decis.id_anonyme and notes.id_table=can.id_table and rep.id_table=can.id_table\n"
     . " and notes.annee=$annee and decis.annee=$annee and can.annee=$annee and rep.annee=$annee\n"
     . ' and notes.id_matiere=mat.id and sex.id=can.sexe and can.serie=ser.id'
     . " and rep.jury=$jury\n"
     . "$andDelib\n"
     . " order by ser.serie, nom, prenoms";
//die($sql);
$result=odb_query($sql,__FILE__,__LINE__);

$pdf_dir='../odb_contrib/ezpdf/';
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
$colonnes=array('id_table','sexe','prefixe','nom','prenoms','matiere','type','note','coeff','delib','moyenne','serie');
while($row=mysql_fetch_array($result)) {
   foreach($colonnes as $col) $$col=utf8_decode(stripslashes($row[$col]));
   $moyenne=str_replace('.',',',round($moyenne,2));
   $id_table=getIdTableHumain($id_table);
   if($sexe=='M') {
      $civ='M.';
      $e='';
   }
   else {
      $civ='Mlle';
      $e='e'; // pour mettre des mots au feminin si c'est une candidate
   }
   if(!$isAdmis && $delib[strlen($delib)-1]=='e') $delib=substr($delib,0,strlen($delib)-1).'&eacute;';
   if(!$isAdmis) $delib.=$e;
   $prefixe=strtolower($prefixe);
   $candidat="$civ $prefixe <b>$nom</b> $prenoms";
   if($type=='Divers' && $note==0) $isFacultatif=true;
   else $isFacultatif=false;
   if($note<0) {
   	$tNotes[$id_table][$type][$matiere]['note']='Absent';
   	$tNotes[$id_table][$type][$matiere]['points']='Absent';
   	$tAbsent[$id_table]=true;
   } elseif(!$isFacultatif) {
	   $tNotes[$id_table][$type][$matiere]['note']=$note;
	   $tNotes[$id_table][$type][$matiere]['points']=($type=='Divers'?$note:$note*$coeff);
	   $tTotal[$id_table][$type]['points']+=$tNotes[$id_table][$type][$matiere]['points'];
	   $tPresent[$id_table]=true;
   }
   if(!$isFacultatif) {
	   $tNotes[$id_table][$type][$matiere]['type']=$type;
	   $tNotes[$id_table][$type][$matiere]['matiere']=$matiere;
	   if($matiere=='Epreuve Facultative 1' || $matiere=='Epreuve Facultative 2'){
	   $tNotes[$id_table][$type][$matiere]['coeff']='-';
   		}else{
   		$tNotes[$id_table][$type][$matiere]['coeff']=($coeff==0?'-':$coeff);
   		}
   		if($matiere=='Epreuve Facultative 1' || $matiere=='Epreuve Facultative 2'){
	   $tNotes[$id_table][$type][$matiere]['sur']='-';
   		}else{
   		$tNotes[$id_table][$type][$matiere]['sur']=($coeff==0?'-':20*$coeff);
   		}
	   	$tTotal[$id_table][$type]['coeff']+=$tNotes[$id_table][$type][$matiere]['coeff'];
	 	$tTotal[$id_table][$type]['sur']+=$tNotes[$id_table][$type][$matiere]['sur'];
	   	
   }
    
   $tCandidats[$id_table]['delib']=$delib;
   $tCandidats[$id_table]['candidat']=$candidat;
   $tCandidats[$id_table]['e']=$e;
   $tCandidats[$id_table]['serie']=$serie;
   $tCandidats[$id_table]['moyenne']=$moyenne;
   
}

foreach($tCandidats as $id_table=>$t1) {
	$e=$t1['e'];
	$serie=$t1['serie'];
	$moyenne=$t1['moyenne'];
	if($tAbsent[$id_table] && $deliberation==1) { 
		if($tPresent[$id_table]) $delib='Absent'.$e;
		else $delib='Abandon';
	} else $delib=$t1['delib'];
	$candidat=$t1['candidat'];
   $pdf->addDestination(getRewriteString($candidat),'FitH');
   if($iPage>0)
      $iPage=$pdf->newPage();
   else $iPage=1;
   $extra='';
   $sommaire.="<c:ilink:".getRewriteString($candidat).">".str_pad($id_table,13,'0',STR_PAD_LEFT)." - $candidat (".html_entity_decode($delib).", $serie)".$extra."</c:ilink>\n";
   //$pdf->rectangle(20,20,$width-35,$height-40);

	//$pdf->addJpegFromFile('img_pack/fond_notes.jpg',0,0,600);
   
   // rectangle SERIE
   $pdf->setColor(0.8,0.8,0.8);
   $pdf->filledRectangle(20,$height-180,80,50);
   $pdf->setColor(0,0,0);
   // fond ODB gris
   
   
	$pdf->setColor(0.7,0.7,0.7);
   $pdf->addText(60,500,300,'<b>O</b>');
   $pdf->addText(300,100,300,'<b>B</b>');
	$pdf->setColor(0.5,0.5,0.5);
   $pdf->addText(170,690,40,'<b>ffice</b>');
   $pdf->addText(350,105,40,html_entity_decode('<b>accalaur&eacute;at</b>'));
   
   // rectangles RELEVE DE NOTES
   $pdf->ezSetY($height-25);
   $pdf->setColor(0.9,0.9,0.9);
   $pdf->setStrokeColor(0.6,0.6,0.6);
   $pdf->filledRectangle(round($width/2,0)+50,$height-110,210,70);
   $pdf->rectangle(round($width/2,0)+50,$height-110,210,70);
   $pdf->setColor(0.8,0.8,0.8);
   $pdf->filledRectangle(round($width/2,0)+80,$height-48,150,20);
   $pdf->setStrokeColor(0.5,0.5,0.5);
   $pdf->rectangle(round($width/2,0)+80,$height-48,150,20);
   $pdf->setColor(0,0,0);
   
   $pdf->ezColumnsStart(array('num'=>2,'gap'=>100));
   //$pdf->ezSetY($height-25);
   $sTmp=html_entity_decode("<b>R&eacute;publique du B&eacute;nin</b>\n\nMinist&egrave;re de l'Enseignement Sup&eacute;rieur\net de la Recherche Scientifique\n\n<b>Office du Baccalaur&eacute;at</b>\n\n03 BP 1525 Cotonou\n");
   $pdf->ezText($sTmp,8,array('justification'=>'center'));
   $pdf->ezSetDy(-40);
   $pdf->ezText(html_entity_decode('S&eacute;rie'),8,array('justification'=>'left'));
   $pdf->ezSetDy(28);
   $pdf->ezText("  <i>$serie</i>",32,array('justification'=>'left'));
   
   $pdf->ezNewPage();
   $sTmp=array();
   $sTmp[]=array(16=>"<b>Relev&eacute; de notes</b>");
   $sTmp[]=array(8=>" ");
   if(in_array($serie,array('A1','A2','B','C','D'))) $sTmp[]=array(14=>"Baccalaur&eacute;at de l'enseignement secondaire g&eacute;n&eacute;ral");
   else $sTmp[]=array(11=>"Baccalaur&eacute;at des enseignements secondaires technique et professionnel");
   $sTmp[]=array(8=>"Session unique de juin $annee\n");
   foreach($sTmp as $tTmp1)
   	foreach($tTmp1 as $taille=>$str) $pdf->ezText(html_entity_decode($str),$taille,array('justification'=>'center'));
   $pdf->ezColumnsStop();

   
   //$pdf->rectangle(25,$height-165,$width-45,50);
   //if(!$isAdmis) $pdf->ezSetDy(-60);
   $pdf->ezSetDy(-70);
   $data=array(
   	array('param'=>html_entity_decode('Nom et pr&eacute;noms'),'valeur'=>html_entity_decode($candidat)),
   	array('param'=>html_entity_decode('Num&eacute;ro de table'),'valeur'=>html_entity_decode("<b>$id_table</b> (jury <b>$jury</b>)"))
   );
   $options=array(
   	//'xPos'=>'left',
   	//'xOrientation'=>'right',
   	'showHeadings'	=> 0,
   	'fontSize'		=> 14,
   	'shaded'			=> 2,
   	'showLines'		=> 2,
   	'shadeCol'		=> array(0.8,0.8,0.8),
   	'shadeCol2'		=> array(0.8,0.8,0.8),
   	'lineCol'		=> array(0.8,0.8,0.8)
   );
   $pdf->ezTable($data,array('param'=>'','valeur'=>''),'',$options);

   $pdf->ezSetDy(-30);
   if(!$isAdmis && !is_array($tNotes[$id_table]['Pratique'])) $pdf->ezSetDy(-50);
	$cols=array(
		//'type'=>html_entity_decode("Nature de l'&eacute;preuve"),
		'matiere'=>html_entity_decode("Mati&egrave;re"),
		'note'=>html_entity_decode("Note\n/20"),
		'coeff'=>html_entity_decode("Coeff"),
		'points'=>html_entity_decode("Points\nObtenus"),
		'sur'=>html_entity_decode("Sur")
	);
	$options=array(
		'cols'=>array(
			'type'=>array('justification'=>'center','width'=>100),
			'matiere'=>array('justification'=>'left','width'=>240),
			'note'=>array('justification'=>'center','width'=>50),
			'coeff'=>array('justification'=>'center','width'=>50),
			'points'=>array('justification'=>'center','width'=>60),
			'sur'=>array('justification'=>'center','width'=>50)
		),
		'xPos'=>round($width/2+5,0),
		'titleFontSize'=> 15,
		'fontSize'=>11
	);
	ksort($tNotes[$id_table]);
	//echo"<pre>";print_r($tNotes[$id_table]);die('</pre>');
	foreach($tNotes[$id_table] as $type=>$tN1) {
		ksort($tNotes[$id_table][$type]);
		$tTotal[$id_table][$type]['note']='-';
		$tNotes[$id_table][$type]['total']=$tTotal[$id_table][$type];
		
	}
	if(is_array($tNotes[$id_table]['Oral'])) {
		$tNotes[$id_table]['Oral']['total2']=$tNotes[$id_table]['Oral']['total'];
		foreach($tNotes[$id_table]['Oral']['total2'] as $k=>$v) 
			if($k!='matiere' && $k!='note') {
				$tNotes[$id_table]['Oral']['total2'][$k]+=(int)$tNotes[$id_table]['Ecrit']['total'][$k]+(int)$tNotes[$id_table]['Pratique']['total'][$k]+(int)$tNotes[$id_table]['Divers']['total'][$k];
			}
		$tNotes[$id_table]['Oral']['total']['matiere']=html_entity_decode('Total des &eacute;preuves orales');
		$tNotes[$id_table]['Oral']['total2']['matiere']=html_entity_decode('Total des &eacute;preuves');
	}
	
	if(is_array($tNotes[$id_table]['Divers']) || is_array($tNotes[$id_table]['Pratique'])) {
		foreach($tNotes[$id_table]['Ecrit']['total'] as $k=>$v) 
			if($k!='matiere' && $k!='note') {
				$tNotes[$id_table]['Divers']['total'][$k]+=(int)$tNotes[$id_table]['Ecrit']['total'][$k]+(int)$tNotes[$id_table]['Pratique']['total'][$k];
			}
	}
	
	$tNotes[$id_table]['Ecrit']['total']['matiere']=html_entity_decode('Total des &eacute;preuves &eacute;crites');
	$tNotes[$id_table]['Ecrit']['total']['note']=str_replace('.',',',round(
		20*$tNotes[$id_table]['Ecrit']['total']['points']/$tNotes[$id_table]['Ecrit']['total']['sur'],2));
	//echo"<pre>";print_r($tNotes[$id_table]['Ecrit']);die('</pre>');

	if($deliberation==3) {
		//$pdf->ezSetDy(10);
		$options['fontSize']=9;
		$options['titleFontSize']=11;
	} else {
		$pdf->ezSetDy(-20);
	}
	if(is_array($tNotes[$id_table]['Pratique'])) {
		$tNotes[$id_table]['Pratique']['total']['matiere']=html_entity_decode('Total des &eacute;preuves pratiques');
		$pdf->ezSetDy(20);
		$tNotes[$id_table]['Pratique']['total']['note']=str_replace('.',',',round(
			20*$tNotes[$id_table]['Pratique']['total']['points']/$tNotes[$id_table]['Pratique']['total']['sur'],2));
		foreach($tNotes[$id_table]['Pratique']['total'] as $k=>$v) $tNotes[$id_table]['Pratique']['total'][$k]="<b>$v</b>";
		$pdf->ezTable($tNotes[$id_table]['Pratique'],$cols,html_entity_decode('&Eacute;preuves pratiques du <b>premier groupe</b>'),$options);
	}
	
	$pdf->ezSetDy(-10);
	foreach($tNotes[$id_table]['Ecrit']['total'] as $k=>$v) $tNotes[$id_table]['Ecrit']['total'][$k]="<b>$v</b>";
	$pdf->ezTable($tNotes[$id_table]['Ecrit'],$cols,html_entity_decode('&Eacute;preuves &eacute;crites du <b>premier groupe</b>'),$options);	

	if(is_array($tNotes[$id_table]['Divers'])) {
		//$pdf->ezSetDy(-10);
		$tNotes[$id_table]['Divers']['total']['matiere']=html_entity_decode('Total du 1er groupe</b>');
		$tNotes[$id_table]['Divers']['total']['note']=str_replace('.',',',round(
			20*$tNotes[$id_table]['Divers']['total']['points']/$tNotes[$id_table]['Divers']['total']['sur'],2));
		foreach($tNotes[$id_table]['Divers']['total'] as $k=>$v) $tNotes[$id_table]['Divers']['total'][$k]="<b>$v</b>";
		$pdf->ezTable($tNotes[$id_table]['Divers'],$cols,html_entity_decode(' '),$options);
	}
	
	if(is_array($tNotes[$id_table]['Oral'])) {
		$pdf->ezSetDy(-10);
		$tNotes[$id_table]['Oral']['total']['note']=str_replace('.',',',round(
			20*$tNotes[$id_table]['Oral']['total']['points']/$tNotes[$id_table]['Oral']['total']['sur'],2));
		$tNotes[$id_table]['Oral']['total2']['note']=str_replace('.',',',round(
			20*$tNotes[$id_table]['Oral']['total2']['points']/$tNotes[$id_table]['Oral']['total2']['sur'],2));
		foreach($tNotes[$id_table]['Oral']['total'] as $k=>$v) $tNotes[$id_table]['Oral']['total'][$k]="<b>$v</b>";
		foreach($tNotes[$id_table]['Oral']['total2'] as $k=>$v) $tNotes[$id_table]['Oral']['total2'][$k]="<b>$v</b>";
		$pdf->ezTable($tNotes[$id_table]['Oral'],$cols,html_entity_decode('&Eacute;preuves orales du <b>deuxi&egrave;me groupe</b>'),$options);
	}
	
	if($deliberation!=3) $pdf->ezSetDy(-10);
	$pdf->ezText(html_entity_decode("Fait &agrave; $lieu_jury, $sDateConvoc\n"),10,array('justification'=>'right'));
	
   if($deliberation!=3) $pdf->ezSetDy(-10);
   else $pdf->ezSetDy(10);
	
   $pdf->ezColumnsStart(array('num'=>2,'gap'=>100));
   $data=array(
   	array('param'=>html_entity_decode('D&eacute;cision du jury'),'valeur'=>html_entity_decode("<b>".($isAdmis?"Admis$e":$delib)."</b>"))
   );
   if($isAdmis) {
   	$delib=ucfirst(str_replace('abien','Assez bien',str_replace('tbien','Tr&egrave;s bien',strtolower($delib))));
   	$data[]=array('param'=>'Mention','valeur'=>html_entity_decode("<b>$delib</b>"));
   }
	$data[]=array('param'=>'Moyenne','valeur'=>html_entity_decode("<b>$moyenne</b>/20"));
   $options=array(
   	//'xPos'=>'left',
   	//'xOrientation'=>'right',
   	'showHeadings'	=> 0,
   	'fontSize'		=> ($deliberation==3?13:15),
   	'shaded'			=> 2,
   	'showLines'		=> 0,
   	'shadeCol'		=> array(0.8,0.8,0.8),
   	'shadeCol2'		=> array(0.8,0.8,0.8),
   	'lineCol'		=> array(0.8,0.8,0.8)
   );
   $pdf->ezTable($data,array('param'=>'','valeur'=>''),'',$options);
	$pdf->ezNewPage();
	$pdf->ezText(html_entity_decode("Signature et cachet du pr&eacute;sident du jury"),10,array('justification'=>'center'));
	$pdf->ezSetY(70);
	$pdf->ezText($nom_jury,11,array('justification'=>'center'));
   $pdf->ezColumnsStop();
   
   $pdf->ezSetY(55);
   $pdf->ezText(html_entity_decode("<b>N.B. :</b> Aucun duplicata de cette pi&egrave;ce ne sera d&eacute;livr&eacute;"),8,array('justification'=>'left'));
	
   $pdf->line(30,40,$width-30,40);
   $pdf->ezSetY(40);
   $pdf->ezText(html_entity_decode("T&eacute;l : +229 21 32 02 64 -=- Fax : +229 21 32 58 17 -=- Adresse : 03 BP 1525 Cotonou (R&eacute;p. du B&eacute;nin) -=- Site : http://www.officedubacbenin.bj"),8,array('justification'=>'center'));
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
