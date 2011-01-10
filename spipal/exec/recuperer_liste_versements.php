<?php
/***************************************************************************\
 *  SPIPAL, Utilitaire de paiement en ligne pour SPIP                      *
 *                                                                         *
 *  Copyright (c) 2007 Thierry Schmit                                      *
 *  Copyright (c) 2011 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

function exec_recuperer_liste_versements_dist() {

 	if (!autoriser('payer_en_ligne', 'versement')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
    
	    $form = charger_fonction('recuperer_liste_versements', 'inc');
	    ajax_retour($form()); 
	}
}
?>
