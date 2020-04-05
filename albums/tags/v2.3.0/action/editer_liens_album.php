<?php
/**
 * Plugin Albums
 * Licence GNU/GPL
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Associer/dissocier un album a un objet editorial
 *
 * arg 1 : associer, dissocier	action
 * arg 2 : objet_lien		objet sur lequel porte l association
 * arg 2 : id_objet_lien
 * arg 3 : id_album
 *
 * exemple : [(#URL_ACTION_AUTEUR{editer_liens_album, associer/#OBJET/#ID_OBJET/#ID_ALBUM, #SELF})]
 */

function action_editer_liens_album_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($action, $objet_lien, $id_objet_lien, $id_album) = preg_split('/\W/', $arg);

	include_spip('inc/autoriser');
	if (intval($id_objet_lien) AND autoriser('associeralbum', $objet_lien, $id_objet_lien)){
		include_spip('action/editer_liens');
		if ($action == 'associer')
			objet_associer(array('album'=>$id_album), array($objet_lien=>$id_objet_lien));
		elseif ($action == 'dissocier')
			objet_dissocier(array('album'=>$id_album), array($objet_lien=>$id_objet_lien));
	}
}

?>
