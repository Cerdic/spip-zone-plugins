<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 09/2011                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */

if (!defined("_ECRIRE_INC_VERSION"))
	return;

// Export du Compte de Resultat au format Pdf, Csv ou Xml

function exec_export_compte_resultat() {
	if (!autoriser('associer', 'export_compte_resultat')) {
		include_spip('inc/minipres');
		echo minipres();
	}
	else {

		$var = _request('var');
		$type = intval(_request('type'));

		switch ($type) {
			case 'pdf':
				include_spip('pdf/export');

				$pdf = new EXPORT_PDF();
				$pdf->SetFont('Arial', '', 12);

				$pdf->AddPage();

				$pdf->init($var);
				$pdf->enTete();
				$pdf->lesCharges($GLOBALS['association_metas']['classe_charges']);
				$pdf->lesProduits($GLOBALS['association_metas']['classe_produits']);
				$pdf->leResultat();
				$pdf->lesContributionsVolontaires($GLOBALS['association_metas']['classe_contributions_volontaires']);

				$pdf->Output('cpte_resultat.pdf', 'I');

				break;

			case 'csv':
				/* TODO : non encore implementee !!! Mais beaucoup plus simple que le PDF */
				break;

			case 'xml':
				/* TODO : non encore implementee !!! Mais beaucoup plus simple que le PDF */
				break;
		}
	}
}

?>
