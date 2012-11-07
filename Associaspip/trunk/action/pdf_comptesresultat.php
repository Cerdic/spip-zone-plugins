<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 * @copyright Copyright (c) 201108 Marcel Bolla
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Version PDF de la synthese des Comptes de Resultat
function action_pdf_comptesresultat() {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();

		include_spip('inc/association_comptabilite');
		$pdf = new ExportComptes_PDF($GLOBALS['association_metas']['fpdf_orientation']?$GLOBALS['association_metas']['fpdf_orientation']:'P', $GLOBALS['association_metas']['fpdf_unit']?$GLOBALS['association_metas']['fpdf_unit']:'mm', $GLOBALS['association_metas']['fpdf_format']?$GLOBALS['association_metas']['fpdf_format']:( ($GLOBALS['association_metas']['fpdf_widht'] AND $GLOBALS['association_metas']['fpdf_height'])?array($GLOBALS['association_metas']['fpdf_widht'],$GLOBALS['association_metas']['fpdf_height']):'A4') );
		$pdf->init();
		$pdf->association_cartouche_pdf('cpte_resultat_titre_general');
		$lesProduits = $pdf->association_liste_totaux_comptes_classes($GLOBALS['association_metas']['classe_produits'], 'cpte_resultat', +1, $pdf_>exercice, $pdf->destination);
		$lesCharges = $pdf->association_liste_totaux_comptes_classes($GLOBALS['association_metas']['classe_charges'], 'cpte_resultat', -1, $pdf_>exercice, $pdf->destination);
		$pdf->association_liste_resultat_net($lesProduits, $lesCharges);
		$pdf->association_liste_totaux_comptes_classes($GLOBALS['association_metas']['classe_contributions_volontaires'], 'cpte_benevolat', 0, $pdf_>exercice, $pdf->destination);
		$pdf->File('comptes_resultats');
}

?>