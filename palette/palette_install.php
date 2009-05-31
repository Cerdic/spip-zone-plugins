<?php

function palette_install($action){
	
	switch($action){
		
		case 'install':
			
			if (function_exists('ecrire_config')){
				if(is_null(lire_config('palette/palette_public')))
					ecrire_config('palette/palette_public','');
				if(is_null(lire_config('palette/palette_ecrire')))
					ecrire_config('palette/palette_ecrire','on');
				}
			break;
			
		case 'uninstall':
			
			if (function_exists('effacer_config')){
				effacer_config('palette/palette_public');
				effacer_config('palette/palette_ecrire');
			}
			break;
			
	}
}
?>
