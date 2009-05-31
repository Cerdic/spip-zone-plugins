<?php
$idCentre=$stats_resultats_centre;
if($idCentre>0) {
	foreach($tab_referentiel['centre'] as $id_departement=>$t1)
		foreach($t1 as $un_centre=>$id_centre)
			if($id_centre==$idCentre) { 
			$centre=$un_centre;
			$departement=$tab_referentiel['departement'][$id_departement];
		}
	$msg="<h1>$departement</h1>\n<h2>$centre</h2>\n";
	/*odb_maj_decisions($annee);
	odb_maj_decisions($annee,0,3,2);
	odb_maj_decisions($annee,0,3,3);*/
	$sql="SELECT decis.id_table, sex.sexe, pre.prefixe, nom, prenoms, eta.etablissement, can.serie idSerie, decis.delib1, decis.delib2, decis.delib3, ldn, ne_en, ne_le, ne_vers\n".
	"FROM odb_ref_sexe sex, odb_ref_etablissement eta, odb_decisions decis, odb_repartition rep, odb_candidats can\n".
	"left join odb_ref_prefixe pre on pre.id=can.prefixe\n".
	"WHERE rep.id_table=decis.id_table and can.id_table=decis.id_table and can.sexe=sex.id and can.etablissement=eta.id\n".
	"and can.annee=$annee and decis.annee=$annee and rep.annee=$annee and rep.id_etablissement=$idCentre\n".
	"ORDER BY idSerie, nom, prenoms";
	//echo $sql;
	$result=mysql_query($sql) or die(KO." - erreur dans la requete <pre>".str_replace($pass,'*****',$sql)."</pre><br/>".mysql_error());
	$cpt=0;
	$tDelib=array();
	while($row=mysql_fetch_array($result)) {
		$cpt++;
		foreach(array('id_table','sexe','prefixe','nom','prenoms','etablissement','idSerie','delib1','delib2','delib3','ne_en','ne_le','ne_vers','ldn') as $col) $$col=$row[$col];
      if($ne_en>0) $ddn="En $ne_en";
      elseif($ne_vers>0) $ddn="Vers $ne_vers";
      else {
         $tDate=explode('-',$ne_le);
         $annee=$tDate[0];
         $mois=$tDate[1];
         $jour=$tDate[2];
         $ddn="$jour/$mois/$annee";
      }
      $ddn.=" $ldn";
		if($sexe=='F') {$sexe='Mlle';$e='e';} else {$sexe='M.';$e='';}
		if($delib1!='Admissible') $delib=$delib1;
		elseif($delib2!='Oral') $delib=$delib2;
		else $delib=$delib3;
		$nom=$prefixe." <b>$nom</b>";
		if($sexe=='F') {$sexe='Mlle';$e='e';} else {$sexe='M.';$e='';}
		if(in_array(strtolower($delib),array('passable','abien','bien','tbien'))) $cle='Admis';
		elseif($delib!='Reserve' && $delib!='Absent') $cle='Refuse';
		else $cle=$delib;
		$tDelib[$cle][$cpt]['id_table']=$id_table;
		$tDelib[$cle][$cpt]['serie']=$tab_referentiel['serie'][$idSerie];
		$tDelib[$cle][$cpt]['candidat']=utf8_decode("$sexe <b>$nom</b> $prenoms");
		$tDelib[$cle][$cpt]['etablissement']=utf8_decode($etablissement);
		if($delib==$delib1) $delib=ereg_replace("e$","&eacute;",$delib1).$e;
		$tDelib[$cle][$cpt]['delib']=html_entity_decode(utf8_decode($delib));
		$tDelib[$cle][$cpt]['ddn']=utf8_decode($ddn);
	}

	$msg.="<table class='spip'>\n";
	$nom_pdf=getRewriteString("Refus&eacute;s centre $centre");
	$_SESSION['cols'][$nom_pdf]=array('id_table'=>html_entity_decode('Num&eacute;ro table'),'serie'=>html_entity_decode('S&eacute;rie'),'candidat'=>'Candidat','ddn'=>'Date de naisssance','etablissement'=>html_entity_decode('&Eacute;tablissement'),'delib'=>'Mention');
	//$_SESSION['format'][$nom_pdf]=array('taille'=>'A3','orientation'=>'portrait');
	$_SESSION['options'][$nom_pdf]=$PDF_A3_PAYSAGE;
   //$_SESSION['options'][$nom_pdf]=$PDF_A4_PORTRAIT;
   //$_SESSION['format'][$nom_pdf]=array('taille'=>'A4', 'orientation'=>'portrait');
	//echo"tDelib<pre>";print_r($tDelib);echo"</pre>";
	$_SESSION['data'][$nom_pdf]=$tDelib['Admis'];
	$_SESSION['titre'][$nom_pdf]=html_entity_decode("Centre d'examen : $centre");
	//$_SESSION['post'][$nom_pdf]=html_entity_decode("Le pr&eacute;sident du jury,");
	$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf' target='_BLANK'>";
	$tmp2="Centre d'examen : <b>$centre</b></A><br/><br/>";
	$lien=$tmp1.$tmp2;
	$msg.="<tr><td>".vignette('pdf',"Centre d'examen : $centre")."</td><td colspan=2>$lien</td></tr>\n";
	$msg.="</table>\n";
} else $msg="Veuillez choisir un centre avant de cliquer sur ce bouton";
?>