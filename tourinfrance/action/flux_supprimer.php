<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_flux_supprimer_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_flux_dist $arg pas compris");
	} else {
		action_flux_supprimer_post($r[1]);
	}
}

function action_flux_supprimer_post($id_flux) {
    
    
    //Tableau des bordereaux.
	include_spip('base/tourinfrance_bordereaux');
	$tab_bordereaux_tourinfrance = creer_tab_bordereaux();
	
    
    //Suppression des RUBRIQUES pour chaque bordereau
	for($i=0; $i<count($tab_bordereaux_tourinfrance); $i++){
		$nom_table = "spip_tourinfrance_" . $tab_bordereaux_tourinfrance[$i];
    	
    	if ($req = sql_select("id_article", $nom_table, "id_flux=" . $id_flux)) {
		    while ($res = sql_fetch($req)) {
		        $id_art = $res['id_article'];
		        
		        
		        //Supprime la liaison auteur-article.
		       	sql_delete("spip_auteurs_articles", "id_article=" . $id_art); 
		       	
		       	
		       	//MOT-CLE : RŽcupre sa commune
		       	$id_mot = sql_getfetsel("id_mot", "spip_mots_articles", "id_article=$id_art");
		       	//Supprime la liaison
		       	sql_delete("spip_mots_articles", "id_article=" . $id_art);
		       	//S'il n'y a plus de liaison avec ce mot : on le supprime aussi
		       	if (!sql_countsel('spip_mots_articles', "id_mot=$id_mot")) {
				    sql_delete("spip_mots", "id_mot=$id_mot");
				}
				
				//RUBRIQUE : recupere la rubrique de la'rticle supprimer
		       	$id_rubrique = sql_getfetsel("id_rubrique", "spip_articles", "id_article=$id_art");
		       	//Supprime l'article associŽ ˆ cette offre
		       	sql_delete("spip_articles", "id_article=" . $id_art); 
				//S'il n'y a plus d'article dans cette rubrique : on change son statut.				
				if (!sql_countsel('spip_articles', "id_rubrique=$id_rubrique")) {
					sql_updateq('spip_rubriques', array('statut'=>'0'), "id_rubrique=$id_rubrique");
				}
		    }
		}	
       	//Supprime l'offre de la table du bordereau
       	sql_delete($nom_table, "id_flux=" . $id_flux); 
	}
	
	
	//Supprime le flux de la table des flux.
	sql_delete("spip_tourinfrance_flux", "id_flux=" . $id_flux);


	//sql_delete("spip_chats", "id_chat=" . sql_quote($id_chat));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_flux/$id_flux'");
}
?>
