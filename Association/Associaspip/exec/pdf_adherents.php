<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('pdf/extends');

function exec_pdf_adherents()
{
	if (!autoriser('associer', 'adherents')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

	$pdf=new PDF();	

	$pdf->titre = _T('asso:adherent_titre_liste_actifs');
	$pdf->Open();
	$pdf->AddPage();
	//On définit les colonnes (champs,largeur,intitulé,alignement)
	$pdf->AddCol($GLOBALS['association_metas']['indexation'],15,_T('asso:adherent_libelle_id_adherent'),'R');
	$pdf->AddCol('nom_famille',50,_T('asso:adherent_libelle_nom'),'L');
	$pdf->AddCol('prenom',40,_T('asso:adherent_libelle_prenom'),'L');
	$pdf->AddCol('ville',50,_T('asso:adherent_libelle_ville'),'L');
	$pdf->AddCol(unicode_to_utf_8('categorie'),30,_T('asso:adherent_libelle_categorie'),'C');
	$pdf->AddCol('validite',20,_T('asso:adherent_libelle_validite'),'L');
	$pdf->AddCol('statut_interne',15,_T('asso:adherent_entete_statut'),'C');
	$prop=array(
		'HeaderColor'=>array(255,150,100),
		'color1'=>array(224,235,255),
		'color2'=>array(255,255,255),
		'padding'=>2
	);
	$order = $GLOBALS['association_metas']['indexation'];
	$order = 'nom_famille' . ($order ? (",$order") : '');
	$pdf->Query(sql_select('*',_ASSOCIATION_AUTEURS_ELARGIS, request_statut_interne(), '', $order), $prop);
	$pdf->Output();
	}
}
?>
