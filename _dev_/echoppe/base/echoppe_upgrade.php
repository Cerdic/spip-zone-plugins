<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function installer_echoppe(){
			//Appel de la fonction d'installation. Lors du clic sur l'icône depuis le panel.
			//quand le plugin est activé et test retourne false
			$version_echoppe_locale = 1.0;
			include_spip('base/echoppe');
			include_spip('base/create');
			include_spip('base/abstract_sql');
			include_spip('inc/import_origine');
			include_spip('base/echoppe_patch_upgrade');
			spip_log("Faut-il mettre à jour echoppe ?");
			if ($version_echoppe_installee > 0){
				switch ($version_echoppe_installee){
					case '0.5' :
						patch_05to06();
						patch_06to07();
						patch_07to08();
						patch_08to09();
						patch_09to10();
					break;
					
					case '0.6' :
						patch_06to07();
						patch_07to08();
						patch_08to09();
						patch_09to10();
					break;
					
					case '0.7' :
						patch_07to08();
						patch_08to09();
						patch_09to10();
					break;
					
					case '0.8' :
						patch_08to09();
						patch_09to10();
						
					break;
					
					case '0.9' :
						patch_09to10();
					break;
					
				}
				
			}else{
				spip_log('Installation plugin echoppe '.$version_echoppe_locale);
				creer_base();
				ecrire_meta('echoppedb_version',$version_echoppe_locale);
				ecrire_metas();
			}
}

function echoppe_vider_tables(){
	include_spip('base/abstract_sql');
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
	effacer_meta('echoppedb_version');
}

//~ $version_echoppe_installee = $GLOBALS['meta']['echoppe_version'];
function echoppe_install($action){
	$version_echoppe_installee = $GLOBALS['meta']['echoppedb_version'];
	switch ($action){
		case 'test':
			//Contrôle du plugin à chaque chargement de la page d'administration
			// doit retourner true si le plugin est proprement installé et à jour, false sinon
			$version_echoppe_locale = 1.0;
			//echo "Echoppe DB V".$version_echoppe_installee;
			//~ echo $version_echoppe_locale.' <-> '.$version_echoppe_installee.'<br />';
			if ($version_echoppe_installee == $version_echoppe_locale){
				$test = true; 
			}else{
				$test = false;
			}
			//~ echo ('Test :'.$test.'<br />');
			//~ return ($GLOBALS['meta']['echoppe_version'] == $version_echoppe_locale);
			return $test;
		break;
		case 'install':
			installer_echoppe();
		break;
		case 'uninstall':
			//Appel de la fonction de suppression
			//quand l'utilisateur clickque sur "supprimer tout" (disponible si test retourne true)
			echoppe_vider_tables();
		break;
	}
}
?>
