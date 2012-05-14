<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('pdf/extends');

function exec_pdf_responsables(){
	if (!autoriser('associer', 'paniers')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

	$pdf=new PDF();
	$date_distribution = $_GET["mois_distribution"];

	$pdf->titre = _T('amap:responsables_distribution_paniers', array('nb'=>date('m/Y',strtotime($date_distribution))));
	$pdf->Open();
	$pdf->AddPage();

	//Tabelau des responsables de distribution pour la date demander
	//On definit les colonnes (champs,largeur,intitule,alignement)
	$pdf->TitreChapitre(1,_T('amap:responsables'));
	$pdf->AddCol('nom',40,_T('amap:nom'),'L');
	$pdf->AddCol('date_distribution',40,_T('amap:date_distribution'),'L');
	$prop=array(
		'HeaderColor'=>array(255,150,100),
		'color1'=>array(224,235,255),
		'color2'=>array(255,255,255),
		'padding'=>2);
	$pdf->Query_extended(sql_select("a.nom as nom, b.date_distribution as date_distribution, a.id_auteur as id_auteur", "spip_amap_responsables b LEFT JOIN spip_auteurs a ON a.id_auteur=b.id_auteur", "date_distribution = MONTH(date)=".sql_quote(date($date_distribution, 'm')),"" , "nom"), $prop, $date_distribution, "id_auteur");

	$pdf->Output();
	}
}
?>
