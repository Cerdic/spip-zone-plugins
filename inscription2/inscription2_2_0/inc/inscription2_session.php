<?php

function i2_poser_session($id_auteur){
	// on pose une session permettant d'identifier l'abonne 
	// si desfois il lui prenait l'idée de faire "retour" avec son navigateur
	// on prend la date pour permettre la manip que pendant deux minutes
	// voir un exemple dans le plugin abonnement (pipeline)
	include_spip("inc/session");
	session_set("id_inscrit", $id_auteur) ;	
	session_set("date_inscription", time()) ;
	return ;	
}

function i2_session_valide(){
	include_spip("inc/session");
			
	if($id_inscrit = session_get("id_inscrit")){
		$time = time() - session_get("date_inscription") ;
		$limite = 4*60 ; // 4 minutes
		spip_log("auteur $id_inscrit depuis $time secondes sur $limite secondes","session_lol");
		// valide pendant quelques minutes
		if($time < $limite)
			return $id_inscrit ;
	}	
	
	return false ;
}

?>