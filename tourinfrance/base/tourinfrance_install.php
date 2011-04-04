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
			
			//Tableau des type d'offre.
			$tab_types_tourinfrance = array('hot', 'hpa', 'hlo', 'res', 'fma', 'pna', 'pcu', 'deg', 'loi', 'asc', 'iti', 'vil', 'org', 'mul');
			
			//Création de RUBRIQUES pour chaque type d'offres
			for($i=0; $i<count($tab_types_tourinfrance); $i++){
		    	$id = sql_insertq('spip_rubriques', array('titre'=>$tab_types_tourinfrance[$i]));
			}
			
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
		}
	}
}
function tourinfrance_vider_tables($nom_meta_base_version) {

	include_spip('base/abstract_sql');
    
    //Tableau des type d'offre.
	$tab_types_tourinfrance = array('hot', 'hpa', 'hlo', 'res', 'fma', 'pna', 'pcu', 'deg', 'loi', 'asc', 'iti', 'vil', 'org', 'mul');
   
    // On efface les tables du plugin
    sql_drop_table('spip_tourinfrance_flux');
    
    //Création des RUBRIQUES pour chaque type d'offres
	for($i=0; $i<count($tab_types_tourinfrance); $i++){
		$nom_table = "spip_tourinfrance_" . $tab_types_tourinfrance[$i];
    	sql_drop_table($nom_table);
    	sql_delete("spip_rubriques", "titre = '" . $tab_types_tourinfrance[$i] . "'");
	}
   
   
	//sql_alter("TABLE spip_articles DROP type_tourinsoft, DROP donnees_tourinsoft, DROP identifiant_offre_tourinsoft");
	effacer_meta($nom_meta_base_version);
}
?>