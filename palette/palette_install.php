<?php
/**
 * Installation du Plugin Palette
 */
function palette_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (function_exists('ecrire_config')){
			if(is_null(lire_config('config_palette/palette_public')))
				ecrire_config('config_palette/palette_public','');
			if(is_null(lire_config('config_palette/palette_ecrire')))
				ecrire_config('config_palette/palette_ecrire','on');
		}
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible, 'non');
	}
}

function palette_vider_tables($nom_meta_base_version) {
	if (function_exists('effacer_config')){
		effacer_config('config_palette');
	}
	effacer_meta($nom_meta_base_version);
}
?>