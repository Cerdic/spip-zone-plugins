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
function exec_pdf_comptesresultat()
{
	if (!autoriser('associer', 'export_comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/association_comptabilite');
		$pdf = new ExportComptes_PDF();
		$pdf->init();
		$pdf->association_cartouche_pdf('cpte_resultat_titre_general');
		$lesProduits = $pdf->association_liste_totaux_comptes_classes($GLOBALS['association_metas']['classe_produits'], 'cpte_resultat', +1, $pdf_>exercice, $pdf->destination);
		$lesCharges = $pdf->association_liste_totaux_comptes_classes($GLOBALS['association_metas']['classe_charges'], 'cpte_resultat', -1, $pdf_>exercice, $pdf->destination);
		$pdf->association_liste_resultat_net($lesProduits, $lesCharges);
		$pdf->association_liste_totaux_comptes_classes($GLOBALS['association_metas']['classe_contributions_volontaires'], 'cpte_benevolat', 0, $pdf_>exercice, $pdf->destination);
		$pdf->File('comptes_resultats');
	}
}

?>