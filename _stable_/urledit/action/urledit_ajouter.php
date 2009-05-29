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

// http://doc.spip.org/@action_urledit_ajouter_dist
function action_urledit_ajouter_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($type_objet, $id_objet) = preg_split('/\W/', $arg);
	$id_objet = intval($id_objet);
	/*$url = pipeline('creer_chaine_url',
			array(
				'data' => _request('urlpropre'),  // le vieux url_propre
				'objet' => array('type' => $type, 'id_objet' => $id_objet, 'titre'=>_request('urlpropre'))
			)
		);
		*/
	$url =  _request('urlpropre');
	$set = array('url' => $url, 'type' => $type_objet, 'id_objet' => $id_objet, 'date' => 'NOW()');
	if (!@sql_insertq('spip_urls', $set) > 0) {
		//retour erreur duplicite
	}
}

?>