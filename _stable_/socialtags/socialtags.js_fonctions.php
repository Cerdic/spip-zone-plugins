<?php

include_once dirname(__FILE__).'/socialtags_fonctions.php';

function socialtags_json($cfg) {
	if (!is_array($cfg))
		return '[]';

	$json = array();
	
	include_spip('socialtags_fonctions');

	foreach (socialtags_liste() as $service)
	if (in_array($a = $service['lesauteurs'], $cfg)) {
		$t = _q($service['titre']);
		$u = _q($service['url']);
		$d = _q($service['descriptif']);
		$i = _q(find_in_path('images/'.$a.'.png'));
		$json[] = "{ n: {$t}, i: {$i}, u: {$u} }";
	}

	return "[\n\t" . join(",\n\t", $json) . "\n]";
}
