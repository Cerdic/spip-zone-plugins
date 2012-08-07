<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 12/2011                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Export du Compte de Resultat au format Pdf
function exec_pdf_comptesbilan()
{
	if (!autoriser('associer', 'export_comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
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
}

?>