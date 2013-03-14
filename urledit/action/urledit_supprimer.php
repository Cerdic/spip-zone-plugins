<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_urledit_supprimer_dist
function action_urledit_supprimer_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($type_objet, $id_objet) = preg_split('/\W/', $arg);
	$id_objet = intval($id_objet);
	$type_objet = _q($type_objet);
	$url = _q(_request('urlpropre'));
	$set = array('url='.$url, 'type='.$type_objet, 'id_objet' => $id_objet);
	
	if (!@sql_delete('spip_urls', $set) > 0) {
		//retour rien a effacer ?
	}
}
?>