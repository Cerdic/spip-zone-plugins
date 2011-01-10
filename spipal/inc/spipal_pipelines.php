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

function spipal_ajouter_boutons($flux){
	$flux['naviguer']->sousmenu['spipal']= 
	  new Bouton(_DIR_PLUGIN_SPIPAL_ICONES."/av_edit.png",_T('spipal:menu_editer'));
    
	return $flux;
}

// il faudrait rajouter la gestion des droits
// et conditionner a des rubriques d'articles
function spipal_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	if ($exec=='articles') {
		$f = charger_fonction('spipal_article', 'inc');
		$flux['data'] .= $f($flux['args']['id_article']);
	}
	return $flux;
}
?>
