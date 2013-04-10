<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Dissocier une email d'un objet, puis eventuellement la supprimer
 *
 * arg 1 : id_email
 * arg 2 : objet
 * arg 3 : id_objet
 *
 * exemple : #URL_ACTION_AUTEUR{supprimer_email, #ID_ADRESSE/#OBJET/#ID_OBJET, #SELF}
 */

function action_supprimer_email_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($id_email, $objet, $id_objet) = preg_split('/\W/', $arg);

	if ($email AND $id_email AND autoriser('supprimer', 'email', $id_email)) {
		// on supprime les liens entre l'objet et l'email
		include_spip('action/editer_liens');
		objet_dissocier( array('email' => $id_email), array($objet => $id_objet) );
		// si l'email n'a pas d'autre lien, c'est qu'elle n'est plus utilisee
		// on la supprime sans etat d'ame
		if ( count(objet_trouver_liens( array('email' => $id_email), '*' )) == 0 )
			sql_delete('email', "id_email=" . sql_quote($id_email));
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_email/$id_email'");
	}

}

?>
