<?php 

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_composition_noizetier_dist($page=NULL){
	if (is_null($page)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$page = $securiser_action();
	}
	
	if ($page) {
		include_spip('noizetier_fonctions');
		noizetier_supprimer_composition($page);
	}
}

?>