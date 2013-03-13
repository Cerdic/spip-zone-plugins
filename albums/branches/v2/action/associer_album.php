<?php
/**
 * Plugin Albums
 * Licence GNU/GPL
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Associer un album a un objet editorial
 *
 * arg 1 : id_album
 * arg 2 : objet
 * arg 3 : id_objet
 *
 * exemple : #URL_ACTION_AUTEUR{associer_album, #ID_ALBUM/#OBJET/#ID_OBJET, #SELF}
 */

function action_associer_album_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($id_album, $objet, $id_objet) = preg_split('/\W/', $arg);

	include_spip('inc/albums');
	editer_liens_album('associer', $id_album, $objet, $id_objet);
}

?>
