<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_zotspip_forcer_maj_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	include_spip('inc/zotspip');
	zotspip_nettoyer();
	if (zotspip_maj_items(true)<0) {
		$continuer = true;
		while($continuer)
			if (zotspip_maj_items()>=0)
				$continuer = false;
	}
	
	// Redirection
	if ($redirect = _request('redirect')) {
		include_spip('inc/headers');
		redirige_par_entete($redirect.'&maj=ok');
	}
}
?>