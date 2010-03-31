<?php

// insert le css pour les styles supplementaires de la BTE dans le <head> du document (#INSERT_HEAD)
function TypoEnluminee_insert_head($flux) {
	if (isset($GLOBALS['BarreTypoEnrichie_Preserve_Header']) OR !function_exists('lire_config'))
		global $BarreTypoEnrichie_Preserve_Header;
	else
		$BarreTypoEnrichie_Preserve_Header = lire_config('bte/insertcss','Oui');
	if ($BarreTypoEnrichie_Preserve_Header == 'Oui')
		return $flux.'<link rel="stylesheet" href="'.find_in_path('css/enluminurestypo.css').'" type="text/css" media="all" />'."\n";
	return $flux;
}

function TypoEnluminee_header_prive($texte) {
	$texte.= '<link rel="stylesheet" type="text/css" href="' . _DIR_PLUGIN_TYPOENLUMINEE . 'css/enluminurestypo.css" />' . "\n";
	return $texte;
}

?>