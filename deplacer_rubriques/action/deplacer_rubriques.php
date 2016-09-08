<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2012                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/charsets');	# pour le nom de fichier

/**
 * Déplacer des rubriques vers une autre rubrique
 */
function action_deplacer_rubriques_dist($arguments=null) {
	if (is_null($arguments)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arguments = $securiser_action();
	}

	include_spip('action/editer_rubrique');

	$message = "Déplacement des rubriques ";
	$arguments = explode(':', $arguments);

	$rubriques_a_deplacer = explode(",",preg_replace("/([^0-9,]?)/", "", $arguments[0]));

	if (intval($arguments[1])){
		foreach ($rubriques_a_deplacer as $key => $value) {
			if (intval($value)){
				spip_log("On déplace $value vers ".$arguments[1], "deplacer_rubriques8");
				rubrique_instituer($value, array("id_parent"=>$arguments[1]));
			}
		}
	}
}