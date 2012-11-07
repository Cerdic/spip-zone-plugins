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

// Version PDF de la synthese des Comptes de Bilan
function action_pdf_comptesbilan() {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		include_spip('inc/association_comptabilite');
		$pdf = new ExportComptes_PDF($GLOBALS['association_metas']['fpdf_orientation']?$GLOBALS['association_metas']['fpdf_orientation']:'P', $GLOBALS['association_metas']['fpdf_unit']?$GLOBALS['association_metas']['fpdf_unit']:'mm', $GLOBALS['association_metas']['fpdf_format']?$GLOBALS['association_metas']['fpdf_format']:( ($GLOBALS['association_metas']['fpdf_widht'] AND $GLOBALS['association_metas']['fpdf_height'])?array($GLOBALS['association_metas']['fpdf_widht'],$GLOBALS['association_metas']['fpdf_height']):'A4') );
		$pdf->init();
		$pdf->association_cartouche_pdf('cpte_bilan_titre_general');
		$classes_bilan = array();
		$query = sql_select(
			'classe', // select
			'spip_asso_plan', // from
			sql_in('classe', array($GLOBALS['association_metas']['classe_charges'],$GLOBALS['association_metas']['classe_produits'],$GLOBALS['association_metas']['classe_contributions_volontaires']), 'NOT'), // where  not in
			'classe', // group by
			'classe' // order by
		);
		while ($data = sql_fetch($query)) {
			$classes_bilan[] = $data['classe'];
		}
		$lesPassifs = $pdf->association_liste_totaux_comptes_classes($classes_bilan, 'cpte_bilan', +1, $pdf_>exercice, $pdf->destination);
		$lesActifs = $pdf->association_liste_totaux_comptes_classes($classes_bilan, 'cpte_bilan', -1, $pdf_>exercice, $pdf->destination);
		$pdf->association_liste_resultat_net($lesPassifs, $lesActifs);
		$pdf->File('comptes_bilan');
}

?>