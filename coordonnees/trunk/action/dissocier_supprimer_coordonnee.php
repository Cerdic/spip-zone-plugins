<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Dissocier une coordonnee d'un objet, puis eventuellement la supprimer
 *
 * arg 1 : type de coordonnee : adresse, email, numero
 * arg 2 : id_coordonnee
 * arg 3 : objet
 * arg 4 : id_objet
 *
 * exemple : #URL_ACTION_AUTEUR{dissocier_supprimer_coordonnee, adresse/#ID_ADRESSE/#OBJET/#ID_OBJET, #SELF}
 */

function action_dissocier_supprimer_coordonnee_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($coordonnee, $id_coordonnee, $objet, $id_objet) = preg_split('/\W/', $arg);

	if ($coordonnee AND $id_coordonnee AND autoriser('supprimer', $coordonnee, $id_coordonnee)) {
		// on supprime les liens entre l'objet et la coordonnee
		include_spip('action/editer_liens');
		objet_dissocier( array($coordonnee => $id_coordonnee), array($objet => $id_objet) );
		// si la coordonnee n'a pas d'autre lien, c'est qu'elle n'est plus utilisee
		// on la supprime sans etat d'ame
		if ( count(objet_trouver_liens( array($coordonnee => $id_coordonnee), '*' )) == 0 ) {
			$table = table_objet_sql($coordonnee);
			sql_delete($table, "id_${coordonnee}=" . sql_quote($id_coordonnee));
		}
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_$coordonnee/$id_coordonnee'");
	}

}

?>
