<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
 
function action_supprimer_pensebete_dist($id_pensebete=null){

	if (is_null($id_pensebete)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_pensebete = $securiser_action();
	}
	
	// on va faire
	spip_log("Demande de suppression du pense-bête n°$id_pensebete par l'auteur n°".
			$GLOBALS['auteur_session']['id_auteur']." (".$GLOBALS['auteur_session']['nom'].")",
					'pensebetes.' . _LOG_INFO_IMPORTANTE);

	// on peut pas faire
	if (empty($id_pensebete)) {
		spip_log("action_supprimer_pensebete_dist : $id_pensebete est vide",
					'pensebetes.' . _LOG_ERREUR);

		return;
	}

	// cas suppression
	if ($id_pensebete) {
		sql_delete('spip_pensebetes',  'id_pensebete=' . intval($id_pensebete));
	}
	else {
		spip_log("action_supprimer_pensebete_dist : suppession d'$id_pensebete pas possible",
					'pensebetes.' . _LOG_ERREUR);

	}
}

?>