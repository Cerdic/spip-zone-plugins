<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function action_supprimer_sympatic_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	list($id_liste) = preg_split(',[^0-9],',$arg);
	include_spip('inc/autoriser');
	if (intval($id_liste) AND autoriser('supprimer','liste',$id_liste,null,null)){
		sql_delete("spip_sympatic_listes","id_liste = $id_liste");
		sql_delete("spip_sympatic_abonnes","id_liste = $id_liste");
	}
}

?>