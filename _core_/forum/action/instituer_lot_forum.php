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

// http://doc.spip.org/@action_instituer_forum_dist
function action_instituer_lot_forum_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (preg_match(",^(\w+)-,",$arg,$match)
	 AND in_array($statut=$match[1],array('publie','off','spam'))
	 AND autoriser('instituer','forum',0)){
	 	$arg = substr($arg,strlen($statut)+1);
	 	
	 	$arg = explode('/',$arg);
	 	$ip = array_shift($arg);
	 	$email_auteur = array_shift($arg);
	 	$id_auteur = intval(array_shift($arg));
	 	$auteur = implode('/',$arg);
	 	$where = array();
	 	if ($ip) $where[] = "ip=".sql_quote($ip);
	 	if ($email_auteur) $where[] = "email_auteur=".sql_quote($email_auteur);
	 	if ($id_auteur) $where[] = "id_auteur=".intval($id_auteur);
	 	if ($auteur) $where[] = "auteur=".sql_quote($auteur);
		$rows = sql_allfetsel("*", "spip_forum", $where);
		if (!count($rows)) return;
		
		include_spip('action/instituer_forum');
		foreach ($rows as $row) {
			instituer_un_forum($statut,$row);			
		}
	}
}

?>
