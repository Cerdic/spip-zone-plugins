<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Dissocier une numero d'un objet, puis eventuellement la supprimer
 *
 * arg 1 : id_numero
 * arg 2 : objet
 * arg 3 : id_objet
 *
 * exemple : #URL_ACTION_AUTEUR{supprimer_numero, #ID_ADRESSE/#OBJET/#ID_OBJET, #SELF}
 */

function action_supprimer_numero_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($id_numero, $objet, $id_objet) = preg_split('/\W/', $arg);

	if (intval($id_numero) AND autoriser('supprimer', 'numero', $id_numero)) {
		// on supprime les liens entre l'objet et l'numero
		include_spip('action/editer_liens');
		objet_dissocier( array('numero' => $id_numero), array($objet => $id_objet) );
		// si l'numero n'a pas d'autre lien, c'est qu'elle n'est plus utilisee
		// on la supprime sans etat d'ame
		if ( count(objet_trouver_liens( array('numero' => $id_numero), '*' )) == 0 )
			sql_delete('spip_numeros', "id_numero=" . sql_quote($id_numero));
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_numero/$id_numero'");
	}

}

?>
