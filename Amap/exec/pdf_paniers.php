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
	/* recupere le type panier et l'id_auteur associe */
    if ($type_paniers_auteurs = sql_select('id_auteur, type_panier', 'spip_auteurs')) {
		while ($row = sql_fetch($type_paniers_auteurs)) {
			$type_panier_extension[$row['id_auteur']] = array("type_panier" => _T('amap:'.$row['type_panier']));
		}
    }
	$pdf->Query_extended(sql_select("a.nom as nom, b.date_distribution as date_distribution, a.id_auteur as id_auteur", "spip_amap_paniers b LEFT JOIN spip_auteurs a ON a.id_auteur=b.id_auteur", "date_distribution=".sql_quote($date_distribution),"" , "nom"), $prop, $type_panier_extension, "id_auteur");
	$pdf->Output();
	}
}
?>