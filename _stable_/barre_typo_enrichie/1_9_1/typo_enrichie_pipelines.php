<?php
// insert le css pour les styles supplementaires de la BTE dans le <head> du document (#INSERT_HEAD)
function BarreTypoEnrichie_insert_head($flux) {
	global $BarreTypoEnrichie_Preserve_Header;
	if (!$BarreTypoEnrichie_Preserve_Header) {
		$cssFile = find_in_path('css/bartypenr.css');
		$incHead = <<<EOH
<link rel="stylesheet" href="$cssFile" type="text/css" media="all" />
EOH;
		return preg_replace('#(</head>)?$#i', $incHead . "\$1\n", $flux, 1);
	} else {
		return $flux;
	}
}

?>