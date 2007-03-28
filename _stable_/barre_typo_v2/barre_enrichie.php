<?php
function BarreTypoEnrichie_header_prive($texte) {
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	$texte.= '<link rel="stylesheet" type="text/css" href="' . (_DIR_PLUGINS.end($p)) . '/css/bartypenr.css" />' . "\n";
	return $texte;
}
?>
