<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

include_spip('inc/session');

// Met à jour la session et enregistre dans la base
function bigbrother_enregistrer_la_visite_du_site(){
	
	session_set('date_visite', time());
	sql_insertq(
		"spip_visites_auteurs",
		array(
			'date' => date('Y-m-d H:i:s', session_get('date_visite')),
			'id_auteur' => session_get('id_auteur')
		)
	);
	
}

// Teste s'il faut enregistrer la visite ou pas
function bigbrother_tester_la_visite_du_site(){
	
	// On fait seulement si qqn est connecté
	if(intval(session_get('id_auteur')) > 0){
		
		// Si la "connexion" n'existe pas on la crée et on enregistre
		if(!session_get('date_visite')){
			
			bigbrother_enregistrer_la_visite_du_site();
			
		}
		// Sinon si la dernière visite est plus vieille que 30min
		elseif ((time() - session_get('date_visite')) > (30*60)){
			
			// On met à jour et en enregistre
			bigbrother_enregistrer_la_visite_du_site();
			
		}
		// Sinon on ne met que à jour la session
		else{
			
			session_set('date_visite', time());
			
		}
		
	}
	
}

// Enregistre l'entrée dans un article
function bigbrother_enregistrer_l_entree_d_un_article($id_article, $id_auteur){
	
	$date_debut = date('Y-m-d H:i:s', time());
	
	sql_insertq(
		"spip_visites_articles_auteurs",
		array(
			'id_article' => $id_article,
			'id_auteur' => $id_auteur,
			'date_debut' => $date_debut
		)
	);
	
	return $date_debut;
	
}

// Enregistre la sortie d'un article
function bigbrother_enregistrer_la_sortie_d_un_article($id_article, $id_auteur, $date_debut){
	
	if(!intval($id_article) OR !intval($id_auteur))
		return false;
	
	$date_fin = date('Y-m-d H:i:s', time());
	
	sql_updateq(
		"spip_visites_articles_auteurs",
		array(
			'date_fin' => $date_fin
		),
		"id_article=".intval($id_article)." AND id_auteur=".intval($id_auteur)." AND date_debut=".sql_quote($date_debut)
	);
	
	return $date_fin;
	
}

?>
