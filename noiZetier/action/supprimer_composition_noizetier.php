<?php 

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_composition_noizetier_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	
	if ($arg!=''){
		include_spip('inc/noizetier');
		$type = noizetier_page_type($arg);
		$compo = noizetier_page_composition($arg);
		$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
		unset($noizetier_compositions[$type][$compo]);
		if (count($noizetier_compositions[$type])==0)
			unset($noizetier_compositions[$type]);
		ecrire_meta('noizetier_compositions',serialize($noizetier_compositions));
	}
	
}

?>