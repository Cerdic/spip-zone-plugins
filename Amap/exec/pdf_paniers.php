<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('pdf/extends');

function exec_pdf_paniers(){
	if (!autoriser('associer', 'paniers')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

	$pdf=new PDF();
	$date_distribution = $_GET["date_distribution"];
	$pdf->titre = _T('amap:distribution_paniers', array('nb'=>date('d/m/Y',strtotime($date_distribution))));
	$pdf->Open();
	$pdf->AddPage();
	//On definit les colonnes (champs,largeur,intitule,alignement)
	$pdf->AddCol('nom',40,_T('amap:nom'),'L');
	$pdf->AddCol('type_panier',30,_T('amap:type_panier'),'L');
	$pdf->AddCol('signature',40,_T('amap:signature'),'L');
	$prop=array(
		'HeaderColor'=>array(255,150,100),
		'color1'=>array(224,235,255),
		'color2'=>array(255,255,255),
		'padding'=>2);
	$pdf->Table("SELECT * FROM spip_amap_paniers,spip_auteurs WHERE spip_amap_paniers.id_auteur=spip_auteurs.id_auteur AND date_distribution=".sql_quote($date_distribution),$prop);
	$pdf->Output();
	}
}
?>