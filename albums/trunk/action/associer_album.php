<?php
/**
 * Plugin Albums
 * Licence GNU/GPL
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Associer/dissocier un album a un autre objet editorial :
 * arg 1 : lier, delier (action)
 * arg 2 : id_album
 * arg 3 : objet|id_objet (objet sur lequel porte la liaison)
 * exemple : [(#URL_ACTION_AUTEUR{associer_album, lier-#ID_ALBUM/[(#ENV{associer_objet})], #SELF})]
 
 */
function action_associer_album_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$table_arg = explode('-', $arg);
	$action = $table_arg[0]; #on fait un list() des autres arguments apres car ils different selon les actions
	include_spip('inc/autoriser');

	//Associer/dissocier un album a un autre objet editorial
	if (in_array($action, array('lier','delier')) 
	  AND list(, $id_album, $objet_liaison) = $table_arg
	  AND list($objet, $id_objet) = explode('|', $objet_liaison)
	  AND intval($id_album)
	  AND autoriser('associeralbum', $objet, $id_objet)){
		include_spip('action/editer_liens');
		switch ($action) {
			case 'lier':
				objet_associer(array("album"=>$id_album), array($objet=>$id_objet));
				break;
			case 'delier':
				objet_dissocier(array("album"=>$id_album), array($objet=>$id_objet));
				break;
		}
	}

}


?>
