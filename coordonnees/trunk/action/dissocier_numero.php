<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Dissocier un numero d'un objet editorial
 *
 * arg 2 : id_numero
 * arg 3 : objet
 * arg 4 : id_objet
 *
 * exemple : #URL_ACTION_AUTEUR{dissocier_numero, #ID_NUMERO/#OBJET/#ID_OBJET, #SELF}
 */

function action_dissocier_numero_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (
		list($id_numero, $objet, $id_objet) = preg_split('/\W/', $arg)
		AND intval($id_numero)>0 AND intval($id_objet)>0
		AND autoriser('modifier', $objet, $id_objet)
	){
		include_spip('action/editer_liens');
		objet_dissocier(array('numero' => $id_numero), array($objet => $id_objet));
	}
}

?>
