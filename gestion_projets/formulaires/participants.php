<?php

function formulaires_participants_charger_dist(){

	//On charge les définitions
	
	$id_projet=_request('id_projet');
	
	// Si l'id projet est connu, on charge les données


	$participants=sql_getfetsel('participants','spip_projets','id_projet='.sql_quote($id_projet));	
	
	$valeurs['participants']=unserialize($participants);
	

	return $valeurs;
}


function formulaires_participants_traiter_dist(){

	$id_projet=_request('id_projet');

	$valeurs=array('participants'=>serialize(_request('participants')));

	
	sql_updateq('spip_projets',$valeurs,'id_projet='.sql_quote($id_projet));
	

return;
    
}
	 
?>