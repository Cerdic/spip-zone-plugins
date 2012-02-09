<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// exemple #URL_ACTION_AUTEUR{instituer_dictionnaire, #ID_DICTIONNAIRE-1, #SELF}
function action_instituer_dictionnaire_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($id_dictionnaire, $statut) = preg_split('/\W/', $arg);

	$id_dictionnaire = intval($id_dictionnaire);

	include_spip('action/editer_dictionnaire');

	instituer_dictionnaire($id_dictionnaire, array('actif' => $statut));
}

?>
