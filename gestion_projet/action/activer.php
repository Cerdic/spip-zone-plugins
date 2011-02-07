<?php
	 if (!defined("_ECRIRE_INC_VERSION")) return;
	 function action_activer_dist(){
	 $securiser_action = charger_fonction('securiser_action', 'inc');
	 $arg = $securiser_action();
	 $explode=explode('-',_request('arg'));
 	$id_projet = $explode[0];
 	$statut =$explode[1];
 	
    // traitements

    sql_updateq("spip_projets",array("statut" => $statut), "id_projet=". $id_projet);
    }
?>