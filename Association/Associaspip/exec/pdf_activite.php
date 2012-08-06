<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('pdf/extends');

function exec_pdf_activite()
{
	if (!autoriser('associer', 'activites')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_evenement = intval(_request('id'));
		$pdf = new PDF();
		$pdf->titre = utf8_decode(_T('asso:activite_titre_inscriptions_activites'));
		$pdf->Open();
		$pdf->AddPage();
		// On définit les colonnes (champs,largeur,intitulé,alignement)
		$pdf->AddCol('id_activite',10,_T('asso:entete_id'),'R');
		$pdf->AddCol('nom',50,utf8_decode(_T('asso:entete_nom')),'L');
		$pdf->AddCol('id_adherent',20,'N°','R');
		$pdf->AddCol('inscrits',10,_T('asso:activite_entete_inscrits'),'R');
		$pdf->AddCol('montant',20,utf8_decode(_T('asso:entete_montant')),'R');
		$prop = array(
			'padding'=>2
		);
		$pdf->Table("SELECT * FROM spip_asso_activites WHERE id_evenement=$id_evenement ORDER BY nom, date_inscription", $prop);
		$pdf->Output();
	}
}

?>