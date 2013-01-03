<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

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

	$charger = charger_fonction('charger_meteo', 'inc');
	$nom_fichier = $charger($lieu, 'infos', $service);
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
function rainette_icone_meteo($icone, $taille='petit', $service='weather', $chemin='', $extension="png"){

	$html_icone = '';

	if (is_array($icone)) {
		// Utilisation des icones natifs des services autres que weather.com
		list ($l,$h) = @getimagesize($icone['url']);
		$html_icone = '<img src="'.$icone['url'].'" alt="etat meteo" title="'.rainette_resume_meteo($icone['code']).'" width="'.$l.'" height="'.$h.'" />';
	}
	else {
		// Utilisation des icones weather.com
		$icone = ($icone AND (($icone >= 0) AND ($icone < 48))) ? strval($icone) : 'na';
		if (!$chemin) $chemin = _RAINETTE_ICONES_PATH.$taille.'/';

		// Le dossier personnalise ou le dossier passe en argument a bien l'icone requise
		if ($img = find_in_path($chemin.$icone.'.'.$extension)) {
			list ($l,$h) = @getimagesize($img);
			$html_icone = '<img src="'.$img.'" alt="etat meteo" title="'.rainette_resume_meteo($icone).'" width="'.$l.'" height="'.$h.'" />';
		}
		// Le dossier personnalise n'a pas d'image, on prend l'icone par defaut dans le repertoire img_meteo/
		elseif (($chemin = 'img_meteo/'.$taille.'/') && ($img = find_in_path($chemin.$icone.'.'.$extension))) {
			list ($l,$h) = @getimagesize($img);
			$html_icone = '<img src="'.$img.'" alt="etat meteo" title="'.rainette_resume_meteo($icone).'" width="'.$l.'" height="'.$h.'" />';
		}
	}

	return $html_icone;
}

function rainette_resume_meteo($meteo) {

	if (is_numeric($meteo)) {
		// On utilise l'option de _T permettant de savoir si un item existe ou pas
		$resume = _T('rainette:meteo_' . $meteo, array(), array('force' => false));
		if (!$resume)
			$resume = _T('rainette:meteo_na') . " ($meteo)";
	}
	else
		$resume = $meteo ? $meteo : _T('rainette:meteo_na');

	return ucfirst($resume);
}

function rainette_afficher_direction($direction) {
	static $liste_direction = 'N:NNE:NE:ENE:E:ESE:SE:SSE:S:SSW:SW:WSW:W:WNW:NW:NNW:V';

	include_spip('inc/convertir');
	$direction_abregee = (intval($direction)) ? angle2direction($direction) : $direction;
	if (!in_array($direction_abregee, explode(':', $liste_direction)))
		return _T('rainette:valeur_indeterminee');
	else
		return _T('rainette:direction_'.$direction_abregee);
}

function rainette_afficher_tendance($tendance_en, $methode='texte', $chemin='', $extension="png"){
	$html = '';

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

/**
 * Affiche toute donnée météorologique au format numérique avec son unité.
 *
 *
 * @package	RAINETTE/AFFICHAGE
 * @api
 *
 * @param int/float	$valeur			La valeur à afficher
 * @param string	$type_valeur	Type de données à afficher parmi 'temperature', 'pourcentage', 'angle', 'pression', 'distance', 'vitesse', 'population'
 * @param int		$precision		Nombre de décimales à afficher pour les réels uniquement ou -1 pour utiliser le défaut
 *
 * @return string	La chaine calculée ou le texte désignant une valeur indéterminée
 */
function rainette_afficher_unite($valeur, $type_valeur='', $precision=-1) {

	static $precision_defaut = array(
						'pression' => 1,
						'distance' => 1,
						'angle' => 0,
						'vitesse' => 0);

	if (!$service) $service = 'weather';
	include_spip('inc/config');
	$unite = lire_config("rainette/${service}/unite", 'm');

	$valeur_affichee = _T('rainette:valeur_indeterminee');
	if ($valeur) {
		// Détermination de l'arrondi si la donnée est stockée sous format réel
		if (array_key_exists($type_valeur, $precision_defaut)) {
			$precision = ($precision < 0) ? $precision_defaut[$type_valeur] : $precision;
			$valeur = round($valeur, $precision);
		}
		$suffixe = ($type_valeur == 'population')
					? ''
					: (($unite == 'm') ? 'metrique' : 'standard');
		$espace = (($type_valeur == 'temperature') ||
				   ($type_valeur == 'pourcentage') || ($type_valeur == 'angle')) ? '' : '&nbsp;';
		$item = 'rainette:unite_' . $type_valeur . ($suffixe ? '_' . $suffixe : '');
		$valeur_affichee = strval($valeur) . $espace . _T($item);
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

	if ($type == '1_jour') {
		$charger = charger_fonction('charger_meteo', 'inc');
		$nom_fichier = $charger($lieu, 'previsions', $service);
		lire_fichier($nom_fichier,$tableau);
		$tableau = unserialize($tableau);

		$nb_jours_previsions = count($tableau) - 1;
		$jour = min($jour, $nb_jours_previsions);

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
		$tableau[$jour]['derniere_maj'] = $tableau[$nb_jours_previsions]['derniere_maj'];
		$page = recuperer_fond("modeles/$modele", $tableau[$jour]);
		$texte = $page;
	}
	else if ($type == 'x_jours') {
		$charger = charger_fonction('charger_meteo', 'inc');
		$nom_fichier = $charger($lieu, 'previsions', $service);
		lire_fichier($nom_fichier,$tableau);
		$tableau = unserialize($tableau);

		$nb_jours_previsions = count($tableau) - 1;
		if ($jour == 0) $jour = $nb_jours_previsions;
		$jour = min($jour, $nb_jours_previsions);

		$texte = "";
		while (count($tableau) && $jour--){
			$page = recuperer_fond("modeles/$modele", array_shift($tableau));
			$texte .= $page;
		}
	}
	return $texte;
}

function rainette_coasse_conditions($lieu, $modele='conditions_tempsreel', $service='weather'){

	// Recuperation du tableau des conditions courantes
	$charger = charger_fonction('charger_meteo', 'inc');
	$nom_fichier = $charger($lieu, 'conditions', $service);
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);

	// On ajoute le lieu, le mode et le service au contexte fourni au modele
	$tableau['lieu'] = $lieu;
	$tableau['mode'] = 'conditions';
	$tableau['service'] = $service;

	if ($tableau['erreur'])
		$texte = recuperer_fond("modeles/erreur", $tableau);
	else
		$texte = recuperer_fond("modeles/$modele", $tableau);

	return $texte;
}

function rainette_coasse_infos($lieu, $modele='infos_ville', $service='weather'){

	// Recuperation du tableau des conditions courantes
	$charger = charger_fonction('charger_meteo', 'inc');
	$nom_fichier = $charger($lieu, 'infos', $service);
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);

	// On ajoute le lieu, le mode et le service au contexte fourni au modele
	$tableau['lieu'] = $lieu;
	$tableau['mode'] = 'infos';
	$tableau['service'] = $service;

	if ($tableau['erreur'])
		$texte = recuperer_fond("modeles/erreur", $tableau);
	else
		$texte = recuperer_fond("modeles/$modele", $tableau);

	return $texte;
}

function rainette_debug($lieu, $mode='previsions', $service='weather') {

	// Recuperation du tableau des conditions courantes
	$charger = charger_fonction('charger_meteo', 'inc');
	$nom_fichier = $charger($lieu, $mode, $service);
	if ($nom_fichier) {
		lire_fichier($nom_fichier,$tableau);
		$tableau = unserialize($tableau);

		// On ajoute le lieu, le mode et le service au contexte fourni au modele
		if ($mode == 'previsions') {
			// Pour les prévisions les informations communes sont stockées dans un index supplémentaire en fin de tableau
			$index = count($tableau)-1;
			$tableau[$index]['lieu'] = $lieu;
			$tableau[$index]['mode'] = $mode;
			$tableau[$index]['service'] = $service;
		}
		else {
			$tableau['lieu'] = $lieu;
			$tableau['mode'] = $mode;
			$tableau['service'] = $service;
		}

		var_dump($tableau);
	}
}

?>