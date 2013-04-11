<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Dissocier une adresse d'un objet, puis eventuellement la supprimer
 *
 * arg 1 : id_adresse
 * arg 2 : objet
 * arg 3 : id_objet
 *
 * exemple : #URL_ACTION_AUTEUR{supprimer_adresse, #ID_ADRESSE/#OBJET/#ID_OBJET, #SELF}
 */

function action_supprimer_adresse_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($id_adresse, $objet, $id_objet) = preg_split('/\W/', $arg);

	if (intval($id_adresse) AND autoriser('supprimer', 'adresse', $id_adresse)) {
		// on supprime les liens entre l'objet et l'adresse
		include_spip('action/editer_liens');
		objet_dissocier( array('adresse' => $id_adresse), array($objet => $id_objet) );
		// si l'adresse n'a pas d'autre lien, c'est qu'elle n'est plus utilisee
		// on la supprime sans etat d'ame
		if ( count(objet_trouver_liens( array('adresse' => $id_adresse), '*' )) == 0 )
			sql_delete('adresse', "id_adresse=" . sql_quote($id_adresse));
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_adresse/$id_adresse'");
	}

}

?>
