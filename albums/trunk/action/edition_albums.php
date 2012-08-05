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
 * exemple : [(#URL_ACTION_AUTEUR{edition_albums, lier/#ID_ALBUM/[(#ENV{associer_objet})], #SELF})]
 
 * Changer le statut d'un album :
 * arg 1 : preparer, proposer, publier, refuser, supprimer (action)
 * arg 2 : prepa, prop, publie, refuse, poubelle (statut correspondant a l'action)
 * arg 3 : id_album
 * exemple : [(#URL_ACTION_AUTEUR{edition_albums, supprimer/poubelle/#ID_ALBUM, #SELF})]
 */
function action_edition_albums_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$table_arg = explode('/', $arg);
	$action = $table_arg[0]; #on fait un list() des autres arguments apres car ils different pour les actions 'associer' et 'statuts'
	include_spip('inc/autoriser');

	//Associer/dissocier un album a un autre objet editorial
	if (in_array($action, array('lier','delier')) 
	AND list(, $id_album, $objet_liaison) = explode('/', $arg)
	AND list($objet, $id_objet) = explode('|', $objet_liaison)
	AND intval($id_album)
	AND autoriser('associeralbums', $objet, $id_objet)){
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

	//Changer le statut d'un album
	elseif (in_array($action, array('preparer','proposer','publier','refuser','supprimer'))
	AND list(, $statut, $id_album) = explode('/', $arg)
	AND intval($id_album)){
		include_spip('action/editer_objet');
		objet_modifier("album", $id_album, array("statut" => $statut));
	}

}


?>
