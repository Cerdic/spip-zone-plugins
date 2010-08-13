<?php

function palette_insert_head($flux) {
	if(version_compare($GLOBALS['spip_version_branche'],"2.1.0","<")){
		$cfg = unserialize($GLOBALS['meta']['palette']);
		if ($cfg['palette_public'] =='on')
			$flux .= palette_header_common('public');
	}
	return $flux;
}

function palette_header_prive($flux) {
	if(version_compare($GLOBALS['spip_version_branche'],"2.1.0","<")){
		$cfg = unserialize($GLOBALS['meta']['palette']);
		if ($cfg['palette_ecrire'] == 'on')
			$flux .= palette_header_common('prive');
	}
	return $flux;
}

/**
 * Retourne le code html head pour la palette
 * Cette fonction peut être surchargée (cf doc SPIP)
 *
 * @return string
 */
function palette_header_common($type) {
	$f = charger_fonction('palette_header', 'inc');
	if (is_callable($f))
		return $f($type);
	else
		return '';
}

function palette_jquery_plugins($plugins){
	if(version_compare($GLOBALS['spip_version_branche'],"2.1.0",">=")){
		$cfg = unserialize($GLOBALS['meta']['palette']);
		if((test_espace_prive() && ($cfg['palette_ecrire'] == 'on')) OR !test_espace_prive()){
			$plugins[] = _DIR_LIB_PALETTE.'farbtastic.js';
			$plugins[] = 'javascript/palette2.js';
		}
	}
	return $plugins;
}

function palette_insert_head_css($flux){
	if(version_compare($GLOBALS['spip_version_branche'],"2.1.0",">=")){
		static $done = false;
		if ($done) return $flux;
		$done = true;
		
		$css1 = _DIR_LIB_PALETTE.'farbtastic.css';
		if((test_espace_prive() && ($cfg['palette_ecrire'] == 'on'))){
			$css2 = generer_url_public('palette.css');
			$flux .= "\n<link rel='stylesheet' href='$css1' type='text/css' media='projection, screen, tv' />\n";
			$flux .= "\n<link rel='stylesheet' href='$css2' type='text/css' media='projection, screen, tv' />\n";	
		}
		else if(!test_espace_prive()){
			$flux .= "\n<link rel='stylesheet' href='$css1' type='text/css' media='projection, screen, tv' />\n";
		}
	}
	return $flux;
}
?>