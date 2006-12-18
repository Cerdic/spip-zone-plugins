<?php
include_spip('tweak_spip_config');

/*
paremetre $tableau : Array
	'nom' 	=> nom du tweak
	'description' 	=> description du tweak
	'auteur' 		=> auteur du tweak
	'include' 		=> fichier inc/???.php à inclure
	'pipeline' 		=> pipeline à utiliser
	'fonction' 		=> function à utiliser
*/
function add_tweak($tableau) {
	global $tweaks;
	$tweaks[] = $tableau;
}

// $pipeline ici est egal à 'options' ou 'fonctions'
function include_tweaks($pipeline) {
	global $tweaks;
	foreach ($temp=$tweaks as $tweak) if ($tweak['pipeline']==$pipeline && $tweak['actif'])
		include_spip('inc/'.$tweak['include']);
}


// passe le $flux dans le $pipeline ...
function tweak_pipeline($pipeline, $flux) {
	global $tweaks;
	foreach ($temp=$tweaks as $tweak) if ($tweak['pipeline']==$pipeline && $tweak['actif']) {
//		include_once(_DIR_PLUGIN_TWEAK_SPIP.'inc/'.$tweak['include'].'.php');
		include_spip('inc/'.$tweak['include']);
		$fonc = $tweak['fonction'];
		if (function_exists($fonc)) $flux = $fonc($flux);
	}
	return $flux;
}

?>