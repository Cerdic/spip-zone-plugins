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

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function action_delier_produit($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$args = explode('/', $arg);

	if (is_numeric($args[0]) and is_numeric($args[1])) {
		action_delier_produit_post($args[0], $args[1], $args[2], $args[3]);
	}
}

function action_delier_produit_post($id_produit,$id_objet,$objet,$rang=0) {
	$id_produit = intval($id_produit);
	$id_objet = intval($id_objet);
	
	if ($id_produit && $id_objet) {

		sql_delete("spip_produits_liens", array(
				'id_produit = ' . sql_quote($id_produit),
				'id_objet = ' . sql_quote($id_objet),
				'objet = ' . sql_quote($objet),
		));
		
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_produit/$id_produit'");
	}
}
