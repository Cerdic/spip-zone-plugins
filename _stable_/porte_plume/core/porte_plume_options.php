<?php
/*
 * Plugin Porte Plume pour SPIP 2
 * Licence GPL
 * Auteur Matthieu Marcillaud
 */

// si les pipelines ont deja ete declarees dans les les options d'un squelette,
// ne pas les ecraser ici... sinon, les declarer.
foreach (array(
	'porte_plume_barre_declarer',
	'porte_plume_barre_pre_charger',
	'porte_plume_barre_charger',
	'porte_plume_lien_classe_vers_icone',
	) as $p) {
	if (!isset($GLOBALS['spip_pipeline'][$p])) 
		$GLOBALS['spip_pipeline'][$p] = "";
}


?>
