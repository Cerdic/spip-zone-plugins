<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Installation du Plugin Palette
 */
function palette_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (function_exists('ecrire_config')){
			if(is_null(lire_config('palette/palette_public')))
				ecrire_config('palette/palette_public','');
			if(is_null(lire_config('palette/palette_ecrire')))
				ecrire_config('palette/palette_ecrire','on');
		}else{
				$config = @unserialize($GLOBALS['meta']['palette']);
				if (!is_array($config))
					$config = 'a:2:{s:14:"palette_public";N;s:14:"palette_ecrire";s:2:"on";}';
					ecrire_meta('palette', $config);		
		}
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible, 'non');
	}
}

function palette_vider_tables($nom_meta_base_version) {
	if (function_exists('effacer_config')){
		effacer_config('palette');
	}else{
		effacer_meta('palette');
	}
	effacer_meta($nom_meta_base_version);
}
?>