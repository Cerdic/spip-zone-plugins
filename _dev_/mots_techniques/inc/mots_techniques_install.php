<?php

	include_spip('inc/meta');
	function mots_techniques_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			include_spip('base/mots_techniques');
			if ($current_version<0.1){
				sql_alter("TABLE spip_groupes_mots ADD technique text NOT NULL AFTER maj");
				sql_alter("TABLE spip_groupes_mots ADD affiche_formulaire varchar(3) DEFAULT 'oui' NOT NULL AFTER technique");
				echo "Mots Techniques install&eacute;<br/>";
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
		}
	}

	function mots_techniques_vider_tables($nom_meta_base_version) {
		sql_alter("TABLE spip_groupes_mots DROP technique");
		sql_alter("TABLE spip_groupes_mots DROP affiche_formulaire");
		effacer_meta($nom_meta_base_version);
	}

	/*
	function mots_techniques_install($action){
		$nom_meta = "mots_techniques_base";
		
		$version_base = $GLOBALS['mots_techniques_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta'][$nom_meta]) 
					AND ($GLOBALS['meta'][$nom_meta]>=$version_base));
				break;
			case 'install':
				mots_techniques_upgrade($nom_meta, $version_base);
				break;
			case 'uninstall':
				mots_techniques_vider_tables($nom_meta);
				break;
		}
	}*/
?>
