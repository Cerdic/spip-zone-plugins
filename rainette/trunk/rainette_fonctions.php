<?php

define ('_RAINETTE_ICONES_PATH','rainette/');

// Balises du plugin utilisables dans les squelettes et modeles
function balise_RAINETTE_INFOS($p) {

	$code_meteo = interprete_argument_balise(1,$p);
	$code_meteo = isset($code_meteo) ? str_replace('\'', '"', $code_meteo) : '""';
	$type_info = interprete_argument_balise(2,$p);
	$type_info = isset($type_info) ? str_replace('\'', '"', $type_info) : '""';
	$service = interprete_argument_balise(3,$p);
	$service = isset($service) ? str_replace('\'', '"', $service) : '"weather"';

	$p->code = 'calculer_infos('.$code_meteo.', '.$type_info.', '.$service.')';
	$p->interdire_scripts = false;
	return $p;
}

function calculer_infos($lieu, $type, $service) {

	// Traitement des cas ou les arguments sont vides
	if (!$lieu) return '';
	if (!$service) $service = 'weather';

	include_spip('inc/rainette_utils');
	$nom_fichier = charger_meteo($lieu, 'infos', $service);
	lire_fichier($nom_fichier,$tableau);
	if (!isset($type) OR !$type)
		return $tableau;
	else {
		$tableau = unserialize($tableau);
		$info = $tableau[strtolower($type)];
		return $info;
	}
}

// Filtres du plugin utilisables dans les squelettes et modeles
// cf pour le choix des icones http://liquidweather.net/icons.php
function rainette_icone_meteo($meteo, $taille='petit', $service='weather', $chemin='', $extension="png"){
	$html_icone = '';
	include_spip('inc/rainette_utils');
	if (!$chemin) $chemin = _RAINETTE_ICONES_PATH.$taille.'/';
	$temps = meteo2icone($meteo, $service);

	// Le dossier personnalise ou le dossier passe en argument a bien l'icone requise
	if ($img = find_in_path($chemin.$temps.'.'.$extension)) {
		list ($l,$h) = @getimagesize($img);
		$html_icone = '<img src="'.$img.'" alt="etat meteo" title="'.rainette_resume_meteo($meteo).'" width="'.$l.'" height="'.$h.'" />';
	}
	// Le dossier personnalise n'a pas d'image, on prend l'icone par defaut dans le repertoire img_meteo/
	elseif (($chemin = 'img_meteo/'.$taille.'/') && ($img = find_in_path($chemin.$temps.'.'.$extension))) {
		list ($l,$h) = @getimagesize($img);
		$html_icone = '<img src="'.$img.'" alt="etat meteo" title="'.rainette_resume_meteo($meteo).'" width="'.$l.'" height="'.$h.'" />';
	}
	return $html_icone;
}

function rainette_resume_meteo($meteo){
	include_spip('inc/rainette_utils');

	// On utilise l'option de _T permettant de savoir si un item existe ou pas
	$resume = _T('rainette:meteo_' . $meteo, array(), array('force' => false));
	if (!$resume)
		$resume = _T('rainette:meteo_na');

	return ucfirst($resume." ($meteo)");
}

function rainette_afficher_direction($direction){
	static $liste_direction = 'N:NNE:NE:ENE:E:ESE:SE:SSE:S:SSW:SW:WSW:W:WNW:NW:NNW';

	include_spip('inc/rainette_utils');
	$direction_abregee = (intval($direction)) ? angle2direction($direction) : $direction;
	if (!in_array($direction_abregee, explode(':', $liste_direction)))
		return _T('rainette:valeur_indeterminee');
	else
		return _T('rainette:direction_'.$direction_abregee);
}

function rainette_afficher_tendance($tendance_en, $methode='texte', $chemin='', $extension="png"){
	$html = '';
	include_spip('inc/rainette_utils');

	if ($methode == 'texte') {
		$html = _T('rainette:tendance_texte_'.$tendance_en);
	}
	else if ($methode == 'symbole') {
		$html = _T('rainette:tendance_symbole_'.$tendance_en);
	}
	else if ($methode == 'icone') {
		if (!$chemin) $chemin = _RAINETTE_ICONES_PATH;

		// Le dossier personnalise ou le dossier passe en argument a bien l'icone requise
		if ($img = find_in_path($chemin.$tendance_en.'.'.$extension)) {
			list ($l,$h) = @getimagesize($img);
			$html = '<img src="'.$img.'" alt="'._T('rainette:tendance_texte_'.$tendance_en).'" title="'._T('rainette:tendance_texte_'.$tendance_en).'" width="'.$l.'" height="'.$h.'" />';
		}
		// Le dossier personnalise n'a pas d'image, on prend l'icone par defaut dans le repertoire img_meteo/
		elseif (($chemin = 'img_meteo/') && ($img = find_in_path($chemin.$tendance_en.'.'.$extension))) {
			list ($l,$h) = @getimagesize($img);
			$html = '<img src="'.$img.'" alt="'._T('rainette:tendance_texte_'.$tendance_en).'" title="'._T('rainette:tendance_texte_'.$tendance_en).'" width="'.$l.'" height="'.$h.'" />';
		}
	}
	return $html;
}

function rainette_afficher_unite($valeur, $type_valeur='', $service='weather') {

	if (!$service) $service = 'weather';
	include_spip('inc/config');
	$unite = lire_config("rainette/${service}/unite");

	$valeur_affichee = '';
	if ($valeur) {
		$suffixe = ($unite == 'm') ? 'metrique' : 'standard';
		$espace = (($type_valeur == 'temperature') ||
				   ($type_valeur == 'pourcentage') || ($type_valeur == 'angle')) ? '' : '&nbsp;';
		$valeur_affichee = strval($valeur) . $espace . _T('rainette:unite_'.$type_valeur.'_'.$suffixe);
	}
	return $valeur_affichee;
}

/**
 * Charger le fichier des infos meteos jour par jour
 * et rendre l'affichage pour les N premiers jours
 *
 * @param string $lieu
 * @param int $nb_jours_affiche
 * @return string
 */
function rainette_coasse_previsions($lieu, $type='x_jours', $jour=0, $modele='previsions_24h', $service='weather'){
	include_spip('inc/rainette_utils');

	if ($type == '1_jour') {
		$jour = min($jour, _RAINETTE_JOURS_PREVISION-1);

		$nom_fichier = charger_meteo($lieu, 'previsions', $service);
		lire_fichier($nom_fichier,$tableau);
		$tableau = unserialize($tableau);
		// Si jour=0 (aujourd'hui), on complete par le tableau du lendemain matin
		if ($jour == 0) {
			$tableau[$jour]['lever_soleil_demain'] = $tableau[$jour+1]['lever_soleil'];
			$tableau[$jour]['temperature_demain'] = $tableau[$jour+1]['temperature_jour'];
			$tableau[$jour]['code_icone_demain'] = $tableau[$jour+1]['code_icone_jour'];
			$tableau[$jour]['vitesse_vent_demain'] = $tableau[$jour+1]['vitesse_vent_jour'];
			$tableau[$jour]['angle_vent_demain'] = $tableau[$jour+1]['angle_vent_jour'];
			$tableau[$jour]['direction_vent_demain'] = $tableau[$jour+1]['direction_vent_jour'];
			$tableau[$jour]['risque_precipitation_demain'] = $tableau[$jour+1]['risque_precipitation_jour'];
			$tableau[$jour]['humidite_demain'] = $tableau[$jour+1]['humidite_jour'];
		}
		// On ajoute la date de derniere maj
		$tableau[$jour]['derniere_maj'] = $tableau[_RAINETTE_JOURS_PREVISION]['derniere_maj'];
		$page = recuperer_fond("modeles/$modele", $tableau[$jour]);
		$texte = $page;
	}
	else if ($type == 'x_jours') {
		if ($jour == 0) $jour = _RAINETTE_JOURS_PREVISION;
		$jour = min($jour, _RAINETTE_JOURS_PREVISION);

		$nom_fichier = charger_meteo($lieu, 'previsions', $service);
		lire_fichier($nom_fichier,$tableau);
		$tableau = unserialize($tableau);
		$texte = "";
		while (count($tableau) && $jour--){
			$page = recuperer_fond("modeles/$modele", array_shift($tableau));
			$texte .= $page;
		}
	}
	return $texte;
}

function rainette_coasse_conditions($lieu, $modele='conditions_tempsreel', $service='weather'){
	include_spip('inc/rainette_utils');

	// Recuperation du tableau des conditions courantes
	$nom_fichier = charger_meteo($lieu, 'conditions', $service);
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);

	// On ajoute le lieu et le service au contexte fourni au modele
	$tableau['lieu'] = $lieu;
	$tableau['service'] = $service;

	$texte = recuperer_fond("modeles/$modele", $tableau);
	return $texte;
}

function rainette_coasse_infos($lieu, $modele='infos_ville', $service='weather'){
	include_spip('inc/rainette_utils');

	// Recuperation du tableau des conditions courantes
	$nom_fichier = charger_meteo($lieu, 'infos', $service);
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);

	// On ajoute le lieu et le service au contexte fourni au modele
	$tableau['lieu'] = $lieu;
	$tableau['service'] = $service;

	$texte = recuperer_fond("modeles/$modele", $tableau);
	return $texte;
}

function rainette_debug($lieu, $mode='previsions', $service='weather') {
	include_spip('inc/rainette_utils');

	// Recuperation du tableau des conditions courantes
	$nom_fichier = charger_meteo($lieu, $mode, $service);
	if ($nom_fichier) {
		lire_fichier($nom_fichier,$tableau);
		$tableau = unserialize($tableau);

		// On ajoute le lieu et le service au contexte fourni au modele
		$tableau['lieu'] = $lieu;
		$tableau['service'] = $service;

		var_dump($tableau);
	}
}

?>