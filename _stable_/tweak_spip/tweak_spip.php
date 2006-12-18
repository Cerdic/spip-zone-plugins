<?php
#---------------------------------------------------#
#  Plugin  : Tweak SPIP                             #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

include_spip('tweak_spip_config');

/*
paremetre $tableau : Array
	'nom' 	=> nom du tweak
	'description' 	=> description (et liens éventuels) du tweak
	'auteur' 		=> auteur(s) du tweak
	'include' 		=> fichier inc/???.php à inclure
	'options'		=> 1 si l'include doit etre place dans tweak_spip_options.php
	'fonctions'		=> 1 si l'include doit etre place dans tweak_spip_fonctions.php
sinon :	
	'pipeline_1'	=> 'function à utiliser',
	'pipeline_2'	=> 'function à utiliser',
	etc.
*/

// ajoute un tweak à $tweaks;
function add_tweak($tableau) {
	global $tweaks;
	$tweaks[] = $tableau;
}

// $type ici est egal à 'options' ou 'fonctions'
function include_tweaks($type) {
	global $tweaks_pipelines;
	foreach ($tweaks_pipelines[$type] as $inc) include_spip('inc/'.$inc);
}


// passe le $flux dans le $pipeline ...
function tweak_pipeline($pipeline, $flux) {
	global $tweaks, $tweaks_pipelines;
	if (isset($tweaks_pipelines[$pipeline])) {
		foreach ($tweaks_pipelines[$pipeline][0] as $inc) include_spip('inc/'.$inc);
		foreach ($tweaks_pipelines[$pipeline][1] as $fonc) if (function_exists($fonc)) $flux = $fonc($flux);
	}
	return $flux;
}

?>