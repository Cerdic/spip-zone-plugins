<?php
// insert le css pour les styles supplementaires de la BTE dans le <head> du document (#INSERT_HEAD)
function TypoEnluminee_insert_head($flux) {
	if (!function_exists('lire_config')) {
		global $BarreTypoEnrichie_Preserve_Header;
	} else {
		if (lire_config('bte/insertcss','Oui') == 'Non') {
			$BarreTypoEnrichie_Preserve_Header = true;
		} else {
			$BarreTypoEnrichie_Preserve_Header = false;
		}
	}
	if (!$BarreTypoEnrichie_Preserve_Header) {
		$cssFile = find_in_path('css/enluminurestypo.css');
		$incHead = <<<EOH
<link rel="stylesheet" href="$cssFile" type="text/css" media="all" />
EOH;
		return preg_replace('#(</head>)?$#i', $incHead . "\$1\n", $flux, 1);
	} else {
		return $flux;
	}
}

function TypoEnluminee_header_prive($texte) {
	$texte.= '<link rel="stylesheet" type="text/css" href="' . _DIR_PLUGIN_TYPOENLUMINEE . 'css/enluminurestypo.css" />' . "\n";
	return $texte;
}

?>