<?php

function palette_install($action){
	
	switch($action){
		
		case 'install':
			
			if (function_exists('ecrire_config')){
				ecrire_config('palette/palette_public','on');
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
