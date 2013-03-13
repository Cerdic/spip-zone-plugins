<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Dissocier une coordonnee d'un objet editorial
 *
 * arg 1 : type de coordonnee : adresse, email, numero
 * arg 2 : id_coordonnee
 * arg 3 : objet
 * arg 4 : id_objet
 *
 * exemple : #URL_ACTION_AUTEUR{dissocier_coordonnee, adresse/#ID_ADRESSE/#OBJET/#ID_OBJET, #SELF}
 */

function action_dissocier_coordonnee_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($id_syndic, $objet, $id_objet) = preg_split('/\W/', $arg);

	list($coordonnee, $id_coordonnee, $objet, $id_objet) = preg_split('/\W/', $arg);

	if ($coordonnee AND $id_coordonnee AND $objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
		include_spip('action/editer_liens');
		objet_dissocier(array($coordonnee => $id_coordonnee), array($objet => $id_objet));
	}
}

?>
