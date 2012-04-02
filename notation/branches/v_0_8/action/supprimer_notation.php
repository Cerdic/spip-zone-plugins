<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_notation_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_notation = $securiser_action();

	if ($id_notation = intval($id_notation)){
		include_spip('inc/notation');
		$row = sql_fetsel('objet,id_objet','spip_notations','id_notation='.sql_quote($id_notation));
		supprimer_notation($id_notation);
		notation_recalculer_total($row['objet'],$row['id_objet']);
	}
}

?>