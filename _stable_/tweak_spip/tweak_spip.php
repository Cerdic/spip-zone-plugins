<?php

global $tweaks;

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
	foreach ($tweaks as $tweak) if ($tweak['pipeline']==$pipeline)
		include_spip('inc/'.$module);
}


// passe le $flux dans le $pipeline ...
funtion tweak_pipeline($pipeline, &$flux) {
	global $tweaks;
	foreach ($tweaks as $tweak) if ($tweak['pipeline']==$pipeline) {
		include_spip('inc/'.$tweak['include']);
		$fonc = $tweak['fonction'];
		return function_exists($fonc)?$fonc($flux):$flux;
	}
}

?>