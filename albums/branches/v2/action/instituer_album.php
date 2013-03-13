<?php
/**
 * Plugin Albums
 * Licence GNU/GPL
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Changer le statut d'un album :
 * arg 1 : prepa, prop, publie, refuse, poubelle (statut)
 * arg 2 : id_album
 * exemple : [(#URL_ACTION_AUTEUR{instituer_album, publie-#ID_ALBUM, #SELF})]
 */
function action_instituer_album_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$table_arg = explode('-', $arg);

	list($statut,$id_album) = $table_arg;
	include_spip('inc/autoriser');

	//Changer le statut d'un album
	if ($id_album = intval($id_album)
	  AND autoriser('instituer', 'album', $id_album, null, array('statut'=>$statut))){
		include_spip('action/editer_objet');
		objet_modifier("album", $id_album, array("statut" => $statut));
	}

}


?>
