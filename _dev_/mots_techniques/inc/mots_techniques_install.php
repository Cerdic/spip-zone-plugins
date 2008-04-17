<?php

	include_spip('inc/meta');
	function mots_techniques_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			include_spip('base/mots_techniques');
			if ($current_version<0.1){
				sql_alter("TABLE spip_groupes_mots ADD technique text NOT NULL DEFAULT '' AFTER maj");
				sql_alter("TABLE spip_groupes_mots ADD affiche_formulaire varchar(3) DEFAULT 'oui' NOT NULL AFTER technique");
				echo "La base de Mots Techniques est install&eacute;e en version 0.1<br/>";
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
			if ($current_version<0.2){
				sql_alter("TABLE spip_groupes_mots DROP affiche_formulaire");
				echo "La base de Mots Techniques est install&eacute;e en version 0.2<br/>";
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');				
			}
		}
	}

	function mots_techniques_vider_tables($nom_meta_base_version) {
		sql_alter("TABLE spip_groupes_mots DROP technique");
		effacer_meta($nom_meta_base_version);
	}



?>
