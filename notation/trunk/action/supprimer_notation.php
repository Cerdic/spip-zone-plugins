<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_notation_dist($id_notation=null){
	if (is_null($id_notation)){
		$securiser_action = charger_fonction('securiser_action','inc');
		$id_notation = $securiser_action();
	}

	if ($id_notation = intval($id_notation)){
		$row = sql_fetsel('objet,id_objet','spip_notations','id_notation='.sql_quote($id_notation));
		include_spip('inc/autoriser');
		if (autoriser('modifier', 'notation', $id_notation, null, array('objet'=>$row['objet'], 'id_objet'=>$row['id_objet']))){
			include_spip('action/editer_notation');
			notation_supprimer($id_notation);
		}
	}
}

?>