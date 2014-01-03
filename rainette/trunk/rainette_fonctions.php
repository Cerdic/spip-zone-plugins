<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_RAINETTE_ICONES_PATH'))
	define ('_RAINETTE_ICONES_PATH','rainette/');
if (!defined('_RAINETTE_ICONES_GRANDE_TAILLE'))
	define ('_RAINETTE_ICONES_GRANDE_TAILLE', 110);
if (!defined('_RAINETTE_ICONES_PETITE_TAILLE'))
	define ('_RAINETTE_ICONES_PETITE_TAILLE', 28);
if (!defined('_RAINETTE_DEBUG'))
	define ('_RAINETTE_DEBUG', false);

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
function rainette_afficher_icone($meteo, $taille='petit', $chemin='', $extension='png'){
	$taille_defaut = ($taille == 'petit') ? _RAINETTE_ICONES_PETITE_TAILLE : _RAINETTE_ICONES_GRANDE_TAILLE;

	if (is_array($meteo)) {
		// Utilisation des icones natifs des services autres que weather.com
		$resume = attribut_html(rainette_afficher_resume($meteo['code']));
		$source = $meteo['url'];
	}
	else {
		// Utilisation des icones weather.com
		$resume = rainette_afficher_resume($meteo);

		$meteo = ($meteo AND (($meteo >= 0) AND ($meteo < 48))) ? strval($meteo) : 'na';
		$chemin = (!$chemin) ? _RAINETTE_ICONES_PATH . $taille . '/' : rtrim($chemin, '/') . '/';
		$fichier = $meteo . '.' . $extension;
		// Le dossier personnalise ou le dossier passe en argument
		// a-t-il bien l'icone requise ?
		$source = find_in_path($fichier, $chemin);
		if (!$source) {
			// Non, il faut donc prendre l'icone par defaut dans le repertoire img_meteo qui existe toujours
			$source = find_in_path($fichier, "img_meteo/$taille/");
		}
	}

	// On retaille si nécessaire l'image pour qu'elle soit toujours de la même taille (grande ou petite)
	list($largeur, $hauteur) = @getimagesize($source);
	include_spip('filtres/images_transforme');
	if (($largeur < $taille_defaut)
	OR  ($hauteur < $taille_defaut)) {
		// Image plus petite que celle par défaut :
		// --> Il faut insérer et recadrer l'image dans une image plus grande à la taille par défaut
		$source = extraire_attribut(image_recadre($source, $taille_defaut, $taille_defaut, 'center', 'transparent'), 'src');
	}
	elseif  (($largeur > $taille_defaut)
	OR		 ($hauteur > $taille_defaut)) {
		// Image plus grande que celle par défaut :
		// --> Il faut reduire l'image à la taille par défaut
		$source = extraire_attribut(image_reduire($source, $taille_defaut), 'src');
	}

	// On construit la balise img
	$texte = attribut_html($resume);
	$balise_img = "<img src=\"$source\" alt=\"$texte\" title=\"$texte\" width=\"$taille_defaut\" height=\"$taille_defaut\" />";

	return $balise_img;
}

function rainette_afficher_resume($meteo) {

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

/**
 * Conversion une indication de direction en une chaine traduite pour
 * l'affichage dans les modèles.
 *
 * @param	mixed	$direction
 * 		La direction soit sous forme d'une valeur numérique entre 0 et 360, soit sous forme
 * 		d'une chaine. Certains services utilisent la chaine "V" pour indiquer une direction
 * 		variable.
 * @return	string
 * 		La chaine traduite indiquant la direction du vent.
 */
function rainette_afficher_direction($direction) {

	include_spip('inc/convertir');
	$direction = angle2direction($direction);

	if ($direction)
		return _T("rainette:direction_$direction");
	else
		return _T('rainette:valeur_indeterminee');
}

function rainette_afficher_tendance($tendance_en, $methode='texte', $chemin='', $extension="png"){

	$tendance = '';

	if (($tendance_en)
	AND ($texte = _T("rainette:tendance_texte_$tendance_en", array(), array('force' => false)))) {
		if ($methode == 'texte') {
			$tendance = $texte;
		}
		else if ($methode == 'symbole') {
			$tendance = _T("rainette:tendance_symbole_$tendance_en");
		}
		else if ($methode == 'icone') {
			$chemin = (!$chemin) ? _RAINETTE_ICONES_PATH : rtrim($chemin, '/') . '/';
			$fichier = $tendance_en . '.' . $extension;
			// Le dossier personnalise ou le dossier passe en argument
			// a-t-il bien l'icone requise ?
			$source = find_in_path($fichier, $chemin);
			if (!$source) {
				// Non, il faut donc prendre l'icone par defaut dans le repertoire img_meteo qui existe toujours
				$source = find_in_path($fichier, "img_meteo/");
			}

			list($largeur, $hauteur) = @getimagesize($source);
			$tendance = "<img src=\"$source\" alt=\"$texte\" title=\"$texte\" width=\"$largeur\" height=\"$hauteur\" />";
		}
	}

	return $tendance;
}

/**
 * Affiche toute donnée météorologique au format numérique avec son unité.
 *
 *
 * @package	RAINETTE/AFFICHAGE
 * @api
 *
 * @param int/float	$valeur			La valeur à afficher
 * @param string	$type_valeur	Type de données à afficher parmi 'temperature', 'pourcentage', 'angle', 'pression',
 * 									'distance', 'vitesse', 'population', 'precipitation'
 * @param int		$precision		Nombre de décimales à afficher pour les réels uniquement ou -1 pour utiliser le défaut
 *
 * @return string	La chaine calculée ou le texte désignant une valeur indéterminée
 */
function rainette_afficher_unite($valeur, $type_valeur='', $precision=-1) {

	static $precision_defaut = array(
						'temperature' => 0,
						'pression' => 1,
						'distance' => 1,
						'angle' => 0,
						'pourcentage' => 0,
						'population' => 0,
						'precipitation' => 1,
						'vitesse' => 0);

	if (!$service) $service = 'weather';
	include_spip('inc/config');
	$unite = lire_config("rainette/${service}/unite", 'm');

	// On distingue la valeur NULL qui indique que la donnée météo n'est pas fournie par le service avec
	// la valeur '' qui indique que la valeur n'est pas disponible temporairement
	// Dans le cas NULL on n'affiche pas la valeur, dans le cas '' on affiche la non disponibilité
	if ($valeur === NULL) {
		$valeur_affichee = '';
	}
	else {
		$valeur_affichee = _T('rainette:valeur_indeterminee');
		if ($valeur !== '') {
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
function rainette_coasser_previsions($lieu, $type='1_jour', $jour=0, $modele='previsions_2x12h', $service='weather'){

	// Recuperation du tableau des prévisions pour tous les jours disponibles
	$charger = charger_fonction('charger_meteo', 'inc');
	$nom_fichier = $charger($lieu, 'previsions', $service);
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);

	// Détermination de l'index final contenant les extra (erreur, date...)
	$index_extra = count($tableau) - 1;

	// On ajoute le lieu, le mode et le service au contexte fourni au modele
	$tableau[$index_extra]['lieu'] = $lieu;
	$tableau[$index_extra]['mode'] = 'previsions';
	$tableau[$index_extra]['service'] = $service;

	if (($tableau[$index_extra]['erreur'])) {
		// Affichage du message d'erreur
		$texte = recuperer_fond("modeles/erreur", $tableau[$index_extra]);
	}
	else {
		if ($type == '1_jour') {
			// Dans ce cas la variable $jour indique le numéro du jour demandé (0 pour aujourd'hui)
			// Plutôt que de renvoyer une erreur si le numéro du jour est supérieur au nombre de jours en prévisions
			// on renvoie au moins le jour max
			// Sinon, si $jour désigne un jour précis on renvoie une erreur si ce jour ne fait pas partie des
			// prévisions disponibles
			if (($d = intval(strtotime(strval($jour)))) <= 0)
				$index_jour = min($jour, $tableau[$index_extra]['max_jours']-1);
			else {
				$d = intval(ceil(($d-time())/(24*3600)));
				if (($d < 0) OR ($d >= $tableau[$index_extra]['max_jours'])) {
					$tableau[$index_extra]['erreur'] = 'jour';
					$tableau[$index_extra]['date_erreur'] = affdate($jour);
					$texte = recuperer_fond("modeles/erreur", $tableau[$index_extra]);
					return $texte;
				}
				$index_jour = $d;
			}

			// Si jour=0 (aujourd'hui), on complete par le tableau du lendemain matin
			// afin de gérer le passage des prévisions jour à celles de la nuit
			if ($index_jour == 0) {
				$tableau[$index_jour]['lever_soleil'] = $tableau[$index_jour+1]['lever_soleil'];
				foreach ($tableau[$index_jour][0] as $_cle => $_valeur) {
					$tableau[$index_jour][2][$_cle] = $tableau[$index_jour+1][0][$_cle];
				}
			}

			// On ajoute les informations extra (date et crédits)
			$contexte = array_merge($tableau[$index_jour], $tableau[$index_extra]);
			$texte = recuperer_fond("modeles/$modele", $contexte);
		}
		else if ($type == 'x_jours') {
			if ($jour == 0) $jour = $index_extra;
			$nb_jours = min($jour, $index_extra);

			$texte = '';
			for ($i = 0; $i < $nb_jours; $i++) {
				$contexte = array_merge($tableau[$i], $tableau[$index_extra]);
				$texte .= recuperer_fond("modeles/$modele", $contexte);
			}
		}
	}

	return $texte;
}

function rainette_coasser_conditions($lieu, $modele='conditions_tempsreel', $service='weather'){

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

function rainette_coasser_infos($lieu, $modele='infos_ville', $service='weather'){

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
	$debug = '';

	// Recuperation du tableau des conditions courantes
	if (_RAINETTE_DEBUG AND function_exists('bel_env')) {
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

			$debug = bel_env(serialize($tableau));
		}
	}

	return $debug;
}

?>