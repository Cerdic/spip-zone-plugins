<?php
function formulaires_rubrique_a_linscription_bascule_charger(){
	return array();	
	
}

function formulaires_rubrique_a_linscription_bascule_verifier(){
	return array();	
	
}

function formulaires_rubrique_a_linscription_bascule_traiter(){
	$id_auteurs = _request('xd_auteur');
	
	if (gettype($id_auteurs)=='array'){	
		include_spip('base/abstract_sql');
		foreach ($id_auteurs as $id_auteur){
		
		sql_update("spip_auteurs",array('rubrique_a_linscription'=>gettype($null)),"id_auteur = $id_auteur");
		spip_log("Bascule de l'auteur $id_auteur en mode auteur normal",'rubrique_a_linscription');
		}
		
	}
	return array('message_ok'=>_T('rubrique_a_linscription:auteur_bascule',array('id_auteurs'=>implode(',',$id_auteurs))));
}

?>