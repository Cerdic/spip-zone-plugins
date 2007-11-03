<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


//~ $version_echoppe_installee = $GLOBALS['meta']['echoppe_version'];
function echoppe_install($action){
	switch ($action){
		case 'test':
			//Contrôle du plugin à chaque chargement de la page d'administration
			// doit retourner true si le plugin est proprement installé et à jour, false sinon
			$version_echoppe_locale = 0.4;
			$version_echoppe_installee = $GLOBALS['meta']['echoppe_version'];
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
			//Appel de la fonction d'installation. Lors du clic sur l'icône depuis le panel.
			//quand le plugin est activé et test retourne false
			$version_echoppe_locale = 0.4;
			//~ echo ("Test : ".$version_echoppe_locale);
			spip_log('Installation plugin echoppe '.$version_echoppe_locale);
			include_spip('base/echoppe');
			include_spip('base/create');
			include_spip('base/abstract_sql');
			include_spip('inc/import_origine');
			creer_base();
			ecrire_meta('echoppe_version',$version_echoppe_locale);
			ecrire_metas();
			
		break;
		case 'uninstall':
			//Appel de la fonction de suppression
			//quand l'utilisateur clickque sur "supprimer tout" (disponible si test retourne true)
			
			$sql_supprimer_table = "DROP TABLE `spip_echoppe_categories` ,
						`spip_echoppe_categories_articles` ,
						`spip_echoppe_categories_descriptions` ,
						`spip_echoppe_categories_produits` ,
						`spip_echoppe_categories_rubriques` ,
						`spip_echoppe_gammes` ,
						`spip_echoppe_gammes_produits` ,
						`spip_echoppe_options` ,
						`spip_echoppe_options_descriptifs` ,
						`spip_echoppe_options_valeurs` ,
						`spip_echoppe_options_valeurs_descriptifs` ,
						`spip_echoppe_prix` ,
						`spip_echoppe_produits_articles` ,
						`spip_echoppe_produits_descriptions` ,
						`spip_echoppe_produits_documents` ,
						`spip_echoppe_produits_sites` ;";
			spip_query($sql_supprimer_table);
			ecrire_meta('echoppe_version','0.0');
			ecrire_metas();
		break;
	}
}
?>
