<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function action_supprimer_quickvote_dist($arg=null){
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	if ($id_quickvote = intval($arg)){
		// On supprime réellement toutes les reponses & votes associés
		sql_delete('spip_quickvotes_reponses', 'id_quickvote = '.$id_quickvote);
    sql_delete('spip_quickvotes_votes', 'id_quickvote = '.$id_quickvote);
		// On supprime le quickvote
		sql_delete('spip_quickvotes', 'id_quickvote = '.$id_quickvote);
	}
}

?>
