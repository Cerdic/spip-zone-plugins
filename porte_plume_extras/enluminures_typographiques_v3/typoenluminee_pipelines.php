<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// insert le css pour les styles supplementaires de la BTE dans le <head> du document (#INSERT_HEAD)
function TypoEnluminee_insert_head_css($flux) {
	static $done = false;
	if (!$done) {
		$done = true;
		if (isset($GLOBALS['BarreTypoEnrichie_Preserve_Header']) OR !function_exists('lire_config'))
			global $BarreTypoEnrichie_Preserve_Header;
		else
			$BarreTypoEnrichie_Preserve_Header = lire_config('bte/insertcss','Oui');
		if ($BarreTypoEnrichie_Preserve_Header == 'Oui')
			$flux .= "\n".'<link rel="stylesheet" href="'.find_in_path('css/enluminurestypo.css').'" type="text/css" media="all" />';
	}
	return $flux;
}

// insert le css pour les styles supplementaires de la BTE dans le <head> du document (#INSERT_HEAD)
function TypoEnluminee_insert_head($flux) {
	$flux = TypoEnluminee_insert_head_css($flux);
	return $flux;
}
function TypoEnluminee_header_prive($texte) {
	$texte.= '<link rel="stylesheet" type="text/css" href="' . _DIR_PLUGIN_TYPOENLUMINEE . 'css/enluminurestypo.css" />' . "\n";
	$texte.= '<link rel="stylesheet" type="text/css" href="' . _DIR_PLUGIN_TYPOENLUMINEE . 'css/enluminurestypo_prive.css" />' . "\n";
	return $texte;
}

?>