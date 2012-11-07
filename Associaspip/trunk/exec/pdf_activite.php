<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_pdf_activite() {
	if (!autoriser('exporter_inscriptions', 'association') OR !test_plugin_actif('fpdf')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('pdf/extends');
		$id_evenement = association_passeparam_id('evenement');
		$pdf = new PDF();
		$pdf->titre = utf8_decode(_T('asso:activite_titre_inscriptions_activites'));
		$pdf->Open();
		$pdf->AddPage();
		// On définit les colonnes (champs,largeur,intitulé,alignement)
		$pdf->AddCol('id_activite',10,_T('asso:entete_id'),'R');
		$pdf->AddCol('nom',50,utf8_decode(_T('asso:entete_nom')),'L');
		$pdf->AddCol('id_auteur',20,'N°','R');
		$pdf->AddCol('quantite',10,_T('asso:activite_entete_quantite'),'R');
		$pdf->AddCol('prix_unitaire',20,utf8_decode(_T('asso:entete_montant')),'R');
		$pdf->Table("SELECT * FROM spip_asso_activites WHERE id_evenement=$id_evenement ORDER BY nom, date_inscription");
		$pdf->Output();
	}
}

?>