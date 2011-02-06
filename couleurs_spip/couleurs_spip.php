<?php
function couleurs_spip_definirspan($letexte) {
	$letexte = preg_replace('`<cs_(.*)>`iU', '<span class="cs_$1">', $letexte);
	$letexte = preg_replace('`</cs(.*)>`iU', '</span>', $letexte);
	return $letexte;
}

function couleurs_spip_inclurecss($flux) {
	$flux .= '<!-- plugin couleurs_spip -->'."\n";
	$flux .= '<link href="'.find_in_path('css/couleurs_spip.css').'" rel="stylesheet" type="text/css" />'."\n";
	return $flux;
}

?>