<?php

/**
 * Gestion de l'action supprimer_dictionnaire
 *
 * @package SPIP\Dictionnaires\Actions
 */
 
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action supprimant un dictionnaire dans la base de données
 * dont l'identifiant du dictionnaire est donné en paramètre de cette fonction
 * ou en argument de l'action sécurisée
 *
 * Supprime le dictionnaire uniquement si on en a l'autorisation.
 * Cela supprime toutes les définitions attachées à ce dictionnaire.
 * 
 * @param null|int $arg
 *     Identifiant du dictionnaire à supprimer. En absence utilise l'argument
 *     de l'action sécurisée.
 */
function action_supprimer_dictionnaire_dist($arg=null){
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if ($id_dictionnaire = intval($arg)){
		if (autoriser('supprimer', 'dictionnaire', $id_dictionnaire)) {
			// On supprime réellement toutes les définitions contenues
			sql_delete('spip_definitions', 'id_dictionnaire = '.$id_dictionnaire);
			// On supprime le dictionnaire
			sql_delete('spip_dictionnaires', 'id_dictionnaire = '.$id_dictionnaire);
			// On supprime les liens disparus
			include_spip('action/editer_liens');
			objet_optimiser_liens(array('definition'=>'*'),'*');
		}
	}
}

?>
