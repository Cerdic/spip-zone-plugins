<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function action_supprimer_dictionnaire_dist($arg=null){
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	if ($id_dictionnaire = intval($arg)){
		// On supprime réellement toutes les définitions contenues
		sql_delete('spip_definitions', 'id_dictionnaire = '.$id_dictionnaire);
		// On supprime le dictionnaire
		sql_delete('spip_dictionnaires', 'id_dictionnaire = '.$id_dictionnaire);
	}
}

?>
