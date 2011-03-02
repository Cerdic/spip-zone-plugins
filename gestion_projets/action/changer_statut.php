<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_changer_statut_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$explode=explode('-',_request('arg'));
	$id = trim($explode[0]);
	$objet= trim($explode[1]);
	if($objet!='projet')$objet_table = '_'.$objet.'s';	
	$statut =trim($explode[2]);
	$table ='spip_projets'.$objet_table;
	$where ='id_'.$objet.'='.sql_quote($id);
	
 	
    	// traitements
	sql_updateq($table,array('statut' => $statut),$where);
    	
    	
    	if($statut =='50poubelle' AND $objet=='tache'){
    	
    		$projet=sql_fetsel('id_projet,id_tache_source','spip_projets_taches',array('id_tache='.sql_quote($id)));
    		
    		if($projet['id_tache_source'] == $id) sql_updateq('spip_projets_taches',array('statut' => $statut),'id_projet='.sql_quote($projet['id_projet']).' AND id_tache_source='.sql_quote($projet['id_tache_source']));
    		else{
    		
    		}
    		
		$actualiser=charger_fonction('actualiser', 'inc');
		$actualiser($projet['id_projet']);
    		}
    }
    
    
?>