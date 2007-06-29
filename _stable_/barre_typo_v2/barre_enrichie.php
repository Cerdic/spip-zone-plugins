<?php
function BarreTypoEnrichie_header_prive($texte) {
	$texte.= '<link rel="stylesheet" type="text/css" href="' . _DIR_PLUGIN_BARRETYPOENRICHIE . 'css/bartypenr.css" />' . "\n";
	return $texte;
}
?>
