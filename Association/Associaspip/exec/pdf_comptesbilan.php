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
		$ids = association_passe_parametres_comptables();
		$pdf = new ExportComptes_PDF();
		$pdf->init($ids);
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
		$lesActifs = $pdf->association_liste_totaux_comptes_classes_pdf($classes_bilan, 'cpte_bilan', +1, $pdf_>exercice, $pdf->destination);
		$lesPassifs = $pdf->association_liste_totaux_comptes_classes_pdf($classes_bilan, 'cpte_bilan', -1, $pdf_>exercice, $pdf->destination);
//		$pdf->leResultat($lesPassifs, $lesActifs);
		$pdf->File('comptes_bilan');
	}
}

?>