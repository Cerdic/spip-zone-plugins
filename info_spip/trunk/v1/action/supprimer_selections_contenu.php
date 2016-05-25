<?php 

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_selections_contenu_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_selections_contenu = intval($arg);
	
	if ($id_selections_contenu > 0){
		include_spip('action/editer_selections_contenu');
		selections_contenu_supprimer($id_selections_contenu);
	}
	
}
