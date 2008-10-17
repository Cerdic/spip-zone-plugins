<?php

function mots_techniques_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/mots_techniques');
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			# creer_base(); // a marche po :(
			# temporairement? mettons un alter ici
			sql_alter("TABLE spip_groupes_mots ADD technique text NOT NULL DEFAULT '' AFTER maj");
			echo "Installation des Mots Techniques r&eacute;alis&eacute;e<br/>";
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
