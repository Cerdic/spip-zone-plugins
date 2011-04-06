<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function tourinfrance_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 1.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/tourinfrance_table');
		// cas d'une installation
		if ($current_version==1.0){
			include_spip('base/create');
            include_spip('base/abstract_sql');
			creer_base();
			//maj_tables('spip_articles');
			
			//Tableau des bordereaux.
			include_spip('base/tourinfrance_bordereaux');
			$tab_bordereaux_tourinfrance = creer_tab_bordereaux();
			
			//Création de RUBRIQUES pour chaque bordereau (id_rubrique=id_secteur)
			for($i=0; $i<count($tab_bordereaux_tourinfrance); $i++){
		    	$id_rub = sql_insertq('spip_rubriques', array(
		    		'titre'=>$tab_bordereaux_tourinfrance[$i],
		    		'lang'=>'fr'
		    	));
		    	sql_updateq('spip_rubriques', array('id_secteur'=>$id_rub), "id_rubrique=$id_rub");
			}
			
			$id_gp = sql_insertq('spip_groupes_mots', array(
				'titre'=>'communes',
				'descriptif'=>'Liste des communes associ&eacute;es &agrave; une offre Tourinfrance.',
				'tables_liees'=>'articles',
				'unseul'=>'non',
				'obligatoire'=>'non',
				'minirezo'=>'oui',
				'comite'=>'oui',
				'forum'=>'non',
			));
			
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
		}
	}
}
function tourinfrance_vider_tables($nom_meta_base_version) {

	include_spip('base/abstract_sql');
    
    //Tableau des bordereaux.
	include_spip('base/tourinfrance_bordereaux');
	$tab_bordereaux_tourinfrance = creer_tab_bordereaux();
	
   
    // On efface les tables du plugin
    sql_drop_table('spip_tourinfrance_flux');
    

	//Supprime le groupes de mots "communes", et tous ses mots clés.
	$req = sql_fetsel("id_groupe", "spip_groupes_mots", "titre='communes'");
	$id_gp_mot_communes = $req['id_groupe'];
	sql_delete("spip_mots", "id_groupe=" . $id_gp_mot_communes);
	sql_delete("spip_groupes_mots", "id_groupe=" . $id_gp_mot_communes);
    
    
    //Suppression des RUBRIQUES pour chaque bordereau
	for($i=0; $i<count($tab_bordereaux_tourinfrance); $i++){
		$nom_table = "spip_tourinfrance_" . $tab_bordereaux_tourinfrance[$i];
    	
    	$req = sql_fetsel("id_rubrique", "spip_rubriques", "titre='" . $tab_bordereaux_tourinfrance[$i] . "'");
    	$id_rub = $req['id_rubrique'];
    	
    	if ($req2 = sql_select("id_article", "spip_articles", "id_rubrique=" . $id_rub)) {
		    while ($res2 = sql_fetch($req2)) {
		        $id_art = $res2['id_article'];
		        
		        //Supprime les liaisons des avec les articles.
		       	sql_delete("spip_mots_articles", "id_article=" . $id_art);
		       	sql_delete("spip_auteurs_articles", "id_article=" . $id_art); 
		    }
		}
		//Supprime tous les articles de la rubrique 
  		sql_delete("spip_articles", "id_rubrique=" . $id_rub);
		
		//Supprime la rubrique du bordereau
    	sql_delete("spip_rubriques", "titre = '" . $tab_bordereaux_tourinfrance[$i] . "'");
    	
    	//Supprime la table du bordereau
    	sql_drop_table($nom_table);
	}
   
   
	//sql_alter("TABLE spip_articles DROP type_tourinsoft, DROP donnees_tourinsoft, DROP identifiant_offre_tourinsoft");
	effacer_meta($nom_meta_base_version);
}
?>