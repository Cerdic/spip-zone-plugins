<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/meta');



function echoppe_vider_tables($nom_meta_base_version){
	sql_drop_table("spip_echoppe_categories");
	sql_drop_table("spip_echoppe_categories_articles");
	sql_drop_table("spip_echoppe_categories_descriptions");
	sql_drop_table("spip_echoppe_categories_produits");
	sql_drop_table("spip_echoppe_categories_rubriques");
	sql_drop_table("spip_echoppe_client");
	sql_drop_table("spip_echoppe_clients");
	sql_drop_table("spip_echoppe_prestataires");
	sql_drop_table("spip_echoppe_depots");
	sql_drop_table("spip_echoppe_gammes");
	sql_drop_table("spip_echoppe_gammes_produits");
	sql_drop_table("spip_echoppe_options");
	sql_drop_table("spip_echoppe_options_descriptions");
	sql_drop_table("spip_echoppe_options_valeurs");
	sql_drop_table("spip_echoppe_options_valeurs_descriptifs");
	sql_drop_table("spip_echoppe_panier");
	sql_drop_table("spip_echoppe_prix");
	sql_drop_table("spip_echoppe_paniers");
	sql_drop_table("spip_echoppe_statuts_paniers");
	sql_drop_table("spip_echoppe_produits");
	sql_drop_table("spip_echoppe_produits_articles");
	sql_drop_table("spip_echoppe_produits_descriptions");
	sql_drop_table("spip_echoppe_produits_documents");
	sql_drop_table("spip_echoppe_produits_rubriques");
	sql_drop_table("spip_echoppe_produits_sites");
	sql_drop_table("spip_echoppe_valeurs");
	sql_drop_table("spip_echoppe_stock_produits");
	sql_drop_table("spip_echoppe_commentaires_paniers");
	effacer_meta('echoppedb_version');
	effacer_meta($nom_meta_base_version);
}

function echoppe_upgrade($nom_meta_base_version,$version_cible){
	
	echo debut_boite_info(true);
	echo 'Echoppe install&eacute;e : '.$GLOBALS['meta'][$nom_meta_base_version].'<br />';
	echo 'Echoppe local : '.$version_cible.'<br />';
	
	if ($version_echoppe_locale != $GLOBALS['meta'][$nom_meta_base_version]){
		
		include_spip('base/tables_principales');
		include_spip('base/tables_auxiliaires');
		include_spip('base/tables_interfaces');		
		include_spip('base/echoppe_patch_upgrade');		
		
		
		
		if (!isset($GLOBALS['meta'][$nom_meta_base_version]) ){
			include_spip('base/create');
			include_spip('base/abstract_sql');
		 	creer_base();
		 	ecrire_meta($nom_meta_base_version,$version_echoppe_locale=$version_cible,'non');
		 	
		 	echo "INSTALL Echoppe --> ".$version_echoppe_locale." OK";
		 	
		}
		
		if ($GLOBALS['meta'][$nom_meta_base_version] == '0.0.5'){
		 	patch_05to06();
			patch_06to07();
			patch_07to08();
			patch_08to09();
			patch_09to10();
			patch_10to11();
			patch_11to12();
		 	ecrire_meta($nom_meta_base_version,$version_echoppe_locale=$version_cible,'non');
		 	
		 	echo "MAJ Echoppe ".$GLOBALS['meta'][$nom_meta_base_version]." --> ".$version_cible." OK";
		 	
		}
		
		if ($GLOBALS['meta'][$nom_meta_base_version] == '0.0.6'){
			patch_06to07();
			patch_07to08();
			patch_08to09();
			patch_09to10();
			patch_10to11();	
			patch_11to12();
		 	ecrire_meta($nom_meta_base_version,$version_echoppe_locale=$version_cible,'non');	
		 	
		 	echo "MAJ Echoppe ".$GLOBALS['meta'][$nom_meta_base_version]." --> ".$version_cible." OK";
		 	 	
		}
		
		if ($GLOBALS['meta'][$nom_meta_base_version] == '0.0.7'){
		 	patch_07to08();
			patch_08to09();
			patch_09to10();	
			patch_10to11();
			patch_11to12();
		 	ecrire_meta($nom_meta_base_version,$version_echoppe_locale=$version_cible,'non');	
		 	
		 	echo "MAJ Echoppe ".$GLOBALS['meta'][$nom_meta_base_version]." --> ".$version_cible." OK";
		 	
		}
		
		if ($GLOBALS['meta'][$nom_meta_base_version] == '0.0.8'){
		 	patch_08to09();
			patch_09to10();
			patch_10to11();
			patch_11to12();
		 	ecrire_meta($nom_meta_base_version,$version_echoppe_locale=$version_cible,'non');
		 	
		 	echo "MAJ Echoppe ".$GLOBALS['meta'][$nom_meta_base_version]." --> ".$version_cible." OK";
		 	
		}
		
		if ($GLOBALS['meta'][$nom_meta_base_version] == '0.0.9'){
		 	patch_09to10();
			patch_10to11();
			patch_11to12();
		 	ecrire_meta($nom_meta_base_version,$version_echoppe_locale=$version_cible,'non');
		 	
		 	echo "MAJ Echoppe ".$GLOBALS['meta'][$nom_meta_base_version]." --> ".$version_cible." OK";
		 	
		}
		
		if ($GLOBALS['meta'][$nom_meta_base_version] == '0.3.0'){
			patch_10to11();
			patch_11to12();
		 	ecrire_meta($nom_meta_base_version,$version_echoppe_locale=$version_cible,'non');
		 	
		 	echo "MAJ Echoppe ".$GLOBALS['meta'][$nom_meta_base_version]." --> ".$version_cible." OK";
		 	
		}
		
		if ($GLOBALS['meta'][$nom_meta_base_version] == '0.3.1'){
			patch_11to12();
			patch_12to13();
		 	ecrire_meta($nom_meta_base_version,$version_echoppe_locale=$version_cible,'non');
		 	
		 	echo "MAJ Echoppe ".$GLOBALS['meta'][$nom_meta_base_version]." --> ".$version_cible." OK";
		 	
		}
		
		if ($GLOBALS['meta'][$nom_meta_base_version] == '0.3.2'){
			patch_12to13();
		 	ecrire_meta($nom_meta_base_version,$version_echoppe_locale=$version_cible,'non');
		 	
		 	echo "MAJ Echoppe ".$GLOBALS['meta'][$nom_meta_base_version]." --> ".$version_cible." OK";
		 	
		}
	}else{
		echo "NO MAJ<br />";
	}
	echo fin_boite_info(true);
	
}
?>
