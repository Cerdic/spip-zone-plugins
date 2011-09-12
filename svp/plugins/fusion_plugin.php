<?php

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/plugin');
include_spip('inc/svp_outiller');

// Fusion des informations de chaque balise plugin en considerant la compatibilite SPIP
function plugins_fusion_plugin($plugins) {
#var_dump('$plugins', $plugins);
	$fusion = array();
	if (!$plugins)
		return $fusion;

	// On initialise les informations a retourner avec le bloc a priori le plus recent determine par la compatibilite SPIP :
	// On selectionne le bloc dont la borne min de compatibilite SPIP est la plus elevee
	$cle_min_max = -1;
	$borne_min_max = _SVP_VERSION_SPIP_MIN;
	foreach ($plugins as $_cle => $_plugin) {
		if (!$_plugin['compatibilite'])
			$borne_min = _SVP_VERSION_SPIP_MIN;
		$bornes_spip = extraire_bornes($_plugin['compatibilite']);
		$borne_min = ($bornes_spip['min']['valeur']) ? $bornes_spip['min']['valeur'] : _SVP_VERSION_SPIP_MIN;
		if (spip_version_compare($borne_min_max, $borne_min, '<=')) {
			$cle_min_max = $_cle;
			$borne_min_max = $borne_min;
		}
	}
	$fusion = $plugins[$cle_min_max];

	// On relit les autres blocs que celui venant d'etre selectionne et on fusionne les informations necessaires
	// les traitements effectues sont les suivants :
	// -- nom, prefix, documentation, version, etat, version_base, description : *rien*, on conserve ces informations en l'etat
	// -- options, fonctions, install, path, pipeline, bouton, onglet : *rien*, meme si certaines pourraient etre fusionnees ces infos ne sont pas stockees
	// -- auteur, licence : *rien*, l'heuristique pour fusionner ces infos est trop compliquee aujourdhui car c'est du texte libre
	// -- categorie, logo : si la valeur du bloc selectionne est vide on essaye d'en trouver une non vide dans les autres blocs
	// -- compatible : on constuit l'intervalle global de compatibilite SPIP
	// -- necessite, utilise, lib : on construit le tableau par intervalle de compatibilite SPIP
	foreach ($plugins as $_cle => $_plugin) {
		if ($_cle <> $cle_min_max) {
			// categorie et icon
			if (!$fusion['categorie'] AND $_plugin['categorie']) 
				$fusion['categorie'] = $_plugin['categorie'];
			if (!$fusion['logo'] AND $_plugin['logo']) 
				$fusion['logo'] = $_plugin['logo'];
			// compatible
			$fusion['compatibilite'] = fusionner_intervalles(
													$fusion['compatibilite'], 
													$_plugin['compatibilite']);
			// necessite, utilise
		}
	}
#var_dump('$fusion', $fusion);

	return $fusion;
}

?>
