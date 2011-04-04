<?php

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/plugin');

// Fusion des informations de chaque balise plugin en considerant la compatibilite SPIP
function plugins_fusion_plugin($plugins) {
	$fusion = array();
	if (!$plugins)
		return $fusion;

	// On initialise les informations a retourner avec le bloc a priori le plus recent determine par la compatibilite SPIP :
	// On selectionne le bloc dont la borne min de compatibilite SPIP est la plus elevee
	$cle_min_max = -1;
	$borne_min_max = _SVP_VERSION_SPIP_MIN;
	foreach ($plugins as $_cle => $_plugin) {
		if (!$_plugin['necessite']['compatible'])
			$borne_min = _SVP_VERSION_SPIP_MIN;
		$bornes_spip = extraire_bornes($_plugin['necessite']['compatible']);
		$borne_min = ($bornes_spip['min']['valeur']) ? $bornes_spip['min']['valeur'] : _SVP_VERSION_SPIP_MIN;
		if (spip_version_compare($borne_min_max, $borne_min, '<=')) {
			$cle_min_max = $_cle;
			$borne_min_max = $borne_min;
		}
	}
	$fusion = $plugins[$cle_min_max];

	// On relit les autres blocs que celui venant d'etre selectionne et on fusionne les informations necessaires
	// les traitements effectues sont les suivants :
	// -- nom, prefix, lien, version, etat, version_base, description : *rien*, on conserve ces informations en l'etat
	// -- options, fonctions, install, path, pipeline, bouton, onglet : *rien*, meme si certaines pourraient etre fusionnees ces infos ne sont pas stockees
	// -- auteur, licence : *rien*, l'heuristique pour fusionner ces infos est trop compliquee aujourdhui car c'est du texte libre
	// -- categorie, icon : si la valeur du bloc selectionne est vide on essaye d'en trouver une non vide dans les autres blocs
	// -- compatible : on constuit l'intervalle global de compatibilite SPIP
	// -- necessite, utilise : on construit le tableau par intervalle de compatibilite
	foreach ($plugins as $_cle => $_plugin) {
		if ($_cle <> $cle_min_max) {
			// categorie et icon
			if (!$fusion['categorie'] AND $_plugin['categorie']) 
				$fusion['categorie'] = $_plugin['categorie'];
			if (!$fusion['icon'] AND $_plugin['icon']) 
				$fusion['icon'] = $_plugin['icon'];
			// compatible
			$fusion['necessite']['compatible'] = fusionner_intervalles(
													$fusion['necessite']['compatible'], 
													$_plugin['necessite']['compatible']);
			// necessite, utilise
			// --> a faire !!
		}
	}
	
	return $fusion;
}

function fusionner_intervalles($intervalle_a, $intervalle_b) {

	// On recupere les bornes de chaque intervalle
	$borne_a = extraire_bornes($intervalle_a);
	$borne_b = extraire_bornes($intervalle_b);

	// On initialise la borne min de chaque intervalle a 1.9.0 si vide
	if (!$borne_a['min']['valeur'])
		$borne_a['min']['valeur'] = _SVP_VERSION_SPIP_MIN;
	if (!$borne_b['min']['valeur'])
		$borne_b['min']['valeur'] = _SVP_VERSION_SPIP_MIN;

	// On calcul maintenant :
	// -- la borne min de l'intervalle fusionne = min(min_a, min_b)
	// -- suivant l'intervalle retenu la borne max est forcement dans l'autre intervalle = max(autre intervalle)
	//    On presuppose evidemment que les intervalles ne sont pas disjoints et coherents entre eux
	if (spip_version_compare($borne_a['min']['valeur'], $borne_b['min']['valeur'], '<=')) {
		$bornes_fusionnees['min'] = $borne_a['min'];
		$bornes_fusionnees['max'] = $borne_b['max'];
	}
	else {
		$bornes_fusionnees['min'] = $borne_b['min'];
		$bornes_fusionnees['max'] = $borne_a['max'];
	}

	return contruire_intervalle($bornes_fusionnees);
}

function extraire_bornes($intervalle) {
	static $borne_vide = array('valeur' => '', 'incluse' => false);
	$bornes = array('min' => $borne_vide, 'max' => $borne_vide);

	if ($intervalle
	AND preg_match(',^[\[\(]([0-9.a-zRC\s\-]*)[;]([0-9.a-zRC\s\-]*)[\]\)]$,Uis', $intervalle, $matches)) {
		if ($matches[1]) {
			$bornes['min']['valeur'] = trim($matches[1]);
			$bornes['min']['incluse'] = ($intervalle{0} == "[");
		}
		if ($matches[2]) {
			$bornes['max']['valeur'] = trim($matches[2]);
			$bornes['max']['incluse'] = (substr($intervalle,-1) == "]");
		}
	}
	
	return $bornes;
}

function contruire_intervalle($bornes) {
	return ($bornes['min']['incluse'] ? '[' : '(')
			. $bornes['min']['valeur'] . ';' . $bornes['max']['valeur']
			. ($bornes['max']['incluse'] ? ']' : ')');
}
?>
