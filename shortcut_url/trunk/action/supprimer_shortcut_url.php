<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Effacer une shortcut_url
 *
 *
 * @param null $id_shortcut_url
 * @return void
 */
function action_supprimer_shortcut_url_dist() {
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	list($id_shortcut_url) = preg_split(',[^0-9],', $arg);
	include_spip('inc/autoriser');
	if (intval($id_shortcut_url) and autoriser('supprimer', 'shortcut_url', $id_shortcut_url)) {
		include_spip('action/editer_shortcut_url');
		shortcut_url_supprimer($id_shortcut_url);
	}
}
