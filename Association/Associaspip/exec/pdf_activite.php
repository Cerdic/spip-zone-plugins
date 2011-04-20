<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James & Jeannot Lapin     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('pdf/extends');

function exec_pdf_activite()
{
	if (!autoriser('associer', 'activites')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

	$id_evenement=intval($_GET['id']);

	$pdf=new PDF();	
	$pdf->titre = _T('asso:activite_titre_inscriptions_activites');
	$pdf->Open();
	$pdf->AddPage();
	//On définit les colonnes (champs,largeur,intitulé,alignement)
	$pdf->AddCol('id_activite',10,'ID','R');
	$pdf->AddCol('nom',50,_T('asso:activite_libelle_nomcomplet'),'L');
	$pdf->AddCol('id_adherent',20,'N° membre','R');
	$pdf->AddCol('membres',50,'Membres','L');
	$pdf->AddCol('non_membres',50,'Non membres','L');
	$pdf->AddCol('inscrits',10,'Nbre','R');
	$pdf->AddCol('montant',10,'€','R');
	$pdf->AddCol('statut',10,'Statut','L');
	$prop=array(
		'HeaderColor'=>array(255,150,100),
          'color1'=>array(224,235,255),
          'color2'=>array(255,255,255),
          'padding'=>2);
	$pdf->Table("SELECT * FROM spip_asso_activites WHERE id_evenement=$id_evenement ORDER BY nom",$prop);
	$pdf->Output();
	}
}
?>
