<?php

function couleurs_spip_post_propre($letexte) {
	$letexte = preg_replace('`<cs_(.*)>`iU', '<span class="cs_$1">', $letexte);
	$letexte = preg_replace('`</cs(.*)>`iU', '</span>', $letexte);
	return $letexte;
}

function couleurs_spip_css(){
	$css .= '<!-- plugin couleurs_spip -->'."\n";
	$css .= '<link href="'.find_in_path('css/couleurs_spip.css').'" rel="stylesheet" type="text/css" />'."\n";
	return $css;
}

function couleurs_spip_header_prive($flux) {
	$flux .= couleurs_spip_css();
	return $flux;
}

function couleurs_spip_insert_head_css($flux) {
	$flux .= couleurs_spip_css();
	return $flux;
}

?>