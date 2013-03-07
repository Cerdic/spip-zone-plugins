<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Associer une coordonnee a un objet editorial
 *
 * arg 1 : type de coordonnee : adresse, email, numero
 * arg 1 : id_syndic
 * arg 2 : objet
 * arg 3 : id_objet
 *
 * exemple : #URL_ACTION_AUTEUR{associer_coordonnee, adresse/#ID_ADRESSE/#OBJET/#ID_OBJET, #SELF}
 */

function action_associer_coordonnee_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($coordonnee, $id_coordonnee, $objet, $id_objet) = preg_split('/\W/', $arg);

	if ($coordonnee AND $id_coordonnee AND $objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
		include_spip('action/editer_liens');
		objet_associer(array($coordonnee => $id_coordonnee), array($objet => $id_objet));
	}

}

?>
