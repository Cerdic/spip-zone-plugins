<?php
	 if (!defined("_ECRIRE_INC_VERSION")) return;
	 function action_session_dist(){
	 include_spip('gestion_projets_fonctions');
	 $securiser_action = charger_fonction('securiser_action', 'inc');
	 $arg = $securiser_action();
	 $explode=explode('-',_request('arg'));
 	$id_projet = $explode[0];
 	$active =$explode[1];
 	

    // traitements

    sql_updateq("spip_projets",array("active" => $active), "id_projet=". $id_projet);
    
    if ($active) {
		sql_insertq('spip_projets_timetracker',array('date_debut'=>date('Y-m-d G:i:s'),'id_projet'=>$id_projet));
		}
	else{
	 	$secondes=3600;

		$date_fin=date('Y-m-d G:i:s');	
		$session=sql_fetsel('id_session,date_debut','spip_projets_timetracker','date_fin="0000-00-00 00:00:00"');
		$date_debut=$session['date_debut'];
		
		

		
		$duree= difference($date_debut,$date_fin);
		
		sql_updateq('spip_projets_timetracker',array('date_fin'=>$date_fin,'id_projet'=>$id_projet,'duree'=>$duree),'id_session='.sql_quote($session['id_session']));	
		}
    }
?>