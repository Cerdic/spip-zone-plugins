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
	$sql="SELECT decis.id_table, sex.sexe, pre.prefixe, nom, prenoms, eta.etablissement, can.serie idSerie, decis.delib1, decis.delib2, decis.delib3, decis.moyenne\n".
	"FROM odb_ref_sexe sex, odb_ref_etablissement eta, odb_decisions decis, odb_repartition rep, odb_candidats can\n".
	"left join odb_ref_prefixe pre on pre.id=can.prefixe\n".
	"WHERE rep.id_table=decis.id_table and can.id_table=decis.id_table and can.sexe=sex.id and can.etablissement=eta.id\n".
	"and can.annee=$annee and decis.annee=$annee and rep.annee=$annee and rep.id_etablissement=$idCentre\n".
	"ORDER BY nom, prenoms";
	//echo $sql;
	$result=mysql_query($sql) or die(KO." - erreur dans la requete <pre>".str_replace($pass,'*****',$sql)."</pre><br/>".mysql_error());
	$cpt=0;
	$tDelib=array();
	while($row=mysql_fetch_array($result)) {
		$cpt++;
		foreach(array('id_table','sexe','prefixe','nom','prenoms','etablissement','idSerie','delib1','delib2','delib3','moyenne') as $col) $$col=$row[$col];
		if($sexe=='F') {$sexe='Mlle';$e='e';} else {$sexe='M.';$e='';}
		if($delib1!='Admissible') $delib=$delib1;
		elseif($delib2!='Oral') $delib=$delib2;
		else $delib=$delib3;
		$nom=$prefixe." <b>$nom</b>";
		if($sexe=='F') {$sexe='Mlle';$e='e';} else {$sexe='M.';$e='';}
		if(in_array(strtolower($delib),array('passable','abien','bien','tbien'))) $cle='Admis';
		//elseif($delib!='Reserve' && $delib!='Absent') $cle='Refuse';
		else $cle=$delib;
		$tDelib[$cle][$idSerie][$cpt]['id_table']=$id_table;
		$tDelib[$cle][$idSerie][$cpt]['candidat']=utf8_decode("$sexe $nom $prenoms");
		$tDelib[$cle][$idSerie][$cpt]['etablissement']=utf8_decode($etablissement);
		if($delib==$delib1) $delib=ereg_replace("e$","&eacute;",$delib1).$e;
		$tDelib[$cle][$idSerie][$cpt]['delib']=html_entity_decode(utf8_decode($delib));
		$tDelib[$cle][$idSerie][$cpt]['moyenne']=round($moyenne,2);
	}
	foreach(array_keys($tDelib['Ajourne']) as $id_serie) {
		$tSeries[$id_serie]=$tab_referentiel['serie'][$id_serie];
	}
	asort($tSeries);
	$msg.="<table class='spip'>\n";
	foreach($tSeries as $iIdSerie=>$sSerie) {
		$nom_pdf=getRewriteString("Ajourn&eacute;s centre $centre-Serie $sSerie");
		$_SESSION['cols'][$nom_pdf]=array('id_table'=>html_entity_decode('Num&eacute;ro table'),'candidat'=>'Candidat','etablissement'=>html_entity_decode('&Eacute;tablissement'),'delib'=>'Mention','moyenne'=>'Moyenne');
		$_SESSION['format'][$nom_pdf]=array('taille'=>'A3','orientation'=>'portrait');
		//$_SESSION['options'][$nom_pdf]=$PDF_A3_PORTRAIT;
	   $_SESSION['options'][$nom_pdf]=$PDF_A4_PORTRAIT;
	   $_SESSION['format'][$nom_pdf]=array('taille'=>'A4', 'orientation'=>'portrait');
		//echo"tDelib<pre>";print_r($tDelib);echo"</pre>";
		$_SESSION['data'][$nom_pdf]=$tDelib['Ajourne'][$iIdSerie];
		$_SESSION['titre'][$nom_pdf]=html_entity_decode("Ajourn&eacute;(e)s centre $centre - S&eacute;rie $sSerie");
		//$_SESSION['post'][$nom_pdf]=html_entity_decode("Le pr&eacute;sident du jury,");
		$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf' target='_BLANK'>";
		$tmp2="Ajourn&eacute;s au centre <b>$centre</b><br/>S&eacute;rie <b>$sSerie</b></A><br/><br/>";
		$lien=$tmp1.$tmp2;
		$msg.="<tr><td>".vignette('pdf',"Ajourn&eacute;s centre $centre - s&eacute;rie $sSerie")."</td><td colspan=2>$lien</td></tr>\n";
	}
	$msg.="</table>\n";
} else $msg="Veuillez choisir un centre avant de cliquer sur ce bouton";
?>