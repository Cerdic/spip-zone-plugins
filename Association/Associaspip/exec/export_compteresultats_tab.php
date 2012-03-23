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

// Export du Compte de Resultat au format .tab
function exec_export_compteresultats_tab() {
	if (!autoriser('associer', 'export_compteresultats_csv')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		exec_export_compteresultats_tsv();
	}
}

?>