<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function action_retirer_zone_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();
	
	if (preg_match(',^([0-9]+|-1)-([a-z]+)-([0-9]+)$,',$arg,$regs)){
		$id_zone = intval($regs[1]);
		$type = $regs[2];
		$id_objet = intval($regs[3]);
		sql_delete("spip_zones_{$type}s","id_$type=".intval($id_objet).(intval($id_zone)>0?" AND id_zone=".intval($id_zone):""));
	}
}

?>