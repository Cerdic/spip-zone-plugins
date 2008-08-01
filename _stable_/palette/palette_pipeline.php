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

function palette_insert_head($flux) {
  $cfg = unserialize($GLOBALS['meta']['palette']);
  if ($cfg['palette_public'] =='on')
	  $flux .= palette_header_common();
	return $flux;
}

function palette_header_prive($flux) {
  $cfg = unserialize($GLOBALS['meta']['palette']);
	if ($cfg['palette_ecrire'] == 'on')
	  $flux .= palette_header_common();
	return $flux;
}

/**
 * Retourne le code html head pour la palette
 * Cette fonction peut être surchargée (cf doc SPIP)
 *
 * @return string
 */
function palette_header_common() {
	$f = charger_fonction('palette_header', 'inc');
	if (is_callable($f))
		return $f();
	else
		return '';
}
?>