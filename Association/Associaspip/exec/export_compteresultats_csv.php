<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 01/2012                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('exec/compte_resultat'); // c'est pour la definition de classe ExportCompteResultats

// Export du Compte de Resultat au format CSV
// http://fr.wikipedia.org/wiki/Comma-separated_values
// (forme commune de base : champs separes par une virgule et point decimal !)
function exec_export_compteresultats_csv() {
	if (!autoriser('associer', 'export_compteresultats')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$csv = new ExportCompteResultats(_request('var'));
		$csv->exportLignesUniques(',', "\n", array('"'=>'""'), '"', '"');
		$csv->leFichier('csv');
	}
}

?>