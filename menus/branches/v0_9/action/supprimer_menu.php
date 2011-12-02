<?php 

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_menu_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_menu = intval($arg);
	
	if ($id_menu > 0){
		include_spip('inc/menus');
		menus_supprimer_menu($id_menu);
	}
	
}

?>