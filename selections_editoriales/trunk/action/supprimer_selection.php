<?php 

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_selection_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_selection = intval($arg);
	
	if ($id_selection > 0){
		include_spip('action/editer_selection');
		selection_supprimer($id_selection);
	}
	
}
