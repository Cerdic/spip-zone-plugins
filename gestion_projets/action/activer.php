<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_activer_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$explode=explode('-',_request('arg'));
	$id = $explode[0];
	$objet= $explode[1];
	if($objet!='projet')$objet_table = '_'.$objet.'s';	
	$statut =$explode[2];
	
	$table ='spip_projets'.$objet_table;
	$where ='id_'.$objet.'='.sql_quote($id);
 	
    // traitements

    sql_updateq($table,array('statut' => $statut),$where);
    }
?>