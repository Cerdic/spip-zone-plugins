<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function action_supprimer_provider_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	list($id_provider) = preg_split(',[^0-9],',$arg);
	include_spip('inc/autoriser');
	if (!autoriser('modifier','provider',$id_provider))
		return false;
		
	if (intval($id_provider)){
		sql_delete('spip_oembed_providers', 'id_provider='.intval($id_provider));
	}
	$id_provider = 0;
	return $id_provider;
}

?>