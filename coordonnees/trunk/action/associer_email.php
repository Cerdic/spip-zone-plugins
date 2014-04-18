<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Associer un email à un objet editorial
 *
 * arg 2 : id_email
 * arg 3 : objet
 * arg 4 : id_objet
 *
 * exemple : #URL_ACTION_AUTEUR{associer_email, #ID_EMAIL/#OBJET/#ID_OBJET, #SELF}
 */

function action_associer_email_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (
		list($id_email, $objet, $id_objet) = preg_split('/\W/', $arg)
		AND intval($id_coordonnee)>0 AND intval($id_objet)>0
		AND autoriser('modifier', $objet, $id_objet)
	){
		include_spip('action/editer_liens');
		objet_associer(array('email' => $id_email), array($objet => $id_objet));
	}
}

?>
