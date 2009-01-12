<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

function action_supprimer_zone_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();
	
	if ($id_zone = intval($arg)
	 AND autoriser('supprimer','zone',$id_zone)) {
	 	include_spip('action/editer_zone');
	 	accesrestreint_supprime_zone($id_zone);
	}
}

?>