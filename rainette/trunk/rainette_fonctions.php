<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_ICONES_GRANDE_TAILLE')) {
	define('_RAINETTE_ICONES_GRANDE_TAILLE', 110);
}
if (!defined('_RAINETTE_ICONES_PETITE_TAILLE')) {
	define('_RAINETTE_ICONES_PETITE_TAILLE', 28);
}

// Balises du plugin utilisables dans les squelettes et modèles
/**
 * @param $p
 *
 * @return mixed
 */
function balise_RAINETTE_INFOS($p) {

	$lieu = interprete_argument_balise(1, $p);
	$lieu = isset($lieu) ? str_replace('\'', '"', $lieu) : '""';
	$type_info = interprete_argument_balise(2, $p);
	$type_info = isset($type_info) ? str_replace('\'', '"', $type_info) : '""';
	$service = interprete_argument_balise(3, $p);
	$service = isset($service) ? str_replace('\'', '"', $service) : '"weather"';

	$p->code = 'calculer_infos(' . $lieu . ', ' . $type_info . ', ' . $service . ')';
	$p->interdire_scripts = false;

	return $p;
}

/**
 * @param string $lieu
 * @param string $type
 * @param string $service
 *
 * @return mixed
 */
function calculer_infos($lieu, $type, $service) {

	// Initialisation du retour
	$info = '';

	// Traitement des cas ou les arguments sont vides
	if ($lieu) {
		if (!$service) {
			$service = 'weather';
		}

		// Récupération des informations sur le lieu
		$charger = charger_fonction('meteo_charger', 'inc');
		$tableau = $charger($lieu, 'infos', 0, $service);
		if (!isset($type) or !$type) {
			$info = serialize($tableau);
		} else {
			if (isset($tableau['donnees'][strtolower($type)])) {
				$info = $tableau['donnees'][strtolower($type)];
			}
		}
	}

	return $info;
}

/**
 * Affiche l'icône correspondant au code météo fourni.
 *
 * @api
 * @filtre
 *
 * @param array      $icone
 * @param string|int $taille
 * @param array      $options
 *
 * @return string
 */
function rainette_afficher_icone($icone, $taille = 'petit', $options = array()) {

	// Initialisation de la balise img afin de ne rien renvoyé si l'icone est vide.
	$balise_img = '';

	if ($icone) {
		// Initialisation de la source de la balise img avec le fichier icone.
		$source = $icone['source'];

		// On retaille si nécessaire l'image pour qu'elle soit toujours de la même taille (grande ou petite).
		list($largeur, $hauteur) = @getimagesize($source);
		include_spip('filtres/images_transforme');

		// Calcul de la taille maximale de l'icone
		if ($taille == 'petit') {
			$taille_max =_RAINETTE_ICONES_PETITE_TAILLE;
		} elseif ($taille == 'grand') {
			$taille_max =_RAINETTE_ICONES_GRANDE_TAILLE;
		} else {
			$taille_max = intval($taille);
		}

		if (($largeur < $taille_max)	or ($hauteur < $taille_max)) {
			// Image plus petite que celle par défaut :
			// --> Il faut insérer et recadrer l'image dans une image plus grande à la taille par défaut
			$source = extraire_attribut(image_recadre($source, $taille_max, $taille_max, 'center', 'transparent'), 'src');
		} elseif (($largeur > $taille_max) or ($hauteur > $taille_max)) {
			// Image plus grande que celle par défaut :
			// --> Il faut réduire l'image à la taille par défaut
			$source = extraire_attribut(image_reduire($source, $taille_max), 'src');
		}

		// On construit la balise img
		$texte = $icone['code'];
		$classe = !empty($options['classe']) ? 'class="' . $options['classe'] . '" ' : '';
		$balise_img = "<img src=\"${source}\" alt=\"${texte}\" title=\"${texte}\" width=\"${taille_max}\" height=\"${taille_max}\" ${classe}/>";
	}

	return $balise_img;
}

/**
 *
 * @package    RAINETTE/AFFICHAGE
 * @api
 * @filtre
 *
 * @param string|int $resume
 *
 * @return string
 */
function rainette_afficher_resume($resume) {

	if (is_numeric($resume)) {
		// On utilise l'option de _T permettant de savoir si un item existe ou pas
		$texte = _T('rainette:meteo_' . $resume, array(), array('force' => false));
		if (!$texte) {
			$texte = _T('rainette:meteo_na') . " ($resume)";
		}
	} else {
		$texte = $resume ? $resume : _T('rainette:meteo_na');
	}

	return ucfirst($texte);
}

/**
 * Conversion d'une indication de direction en une chaine traduite pour
 * l'affichage dans les modèles.
 *
 * @package    RAINETTE/AFFICHAGE
 * @api
 * @filtre
 *
 * @param mixed $direction
 *        La direction soit sous forme d'une valeur numérique entre 0 et 360, soit sous forme
 *        d'une chaine. Certains services utilisent la chaine "V" pour indiquer une direction
 *        variable.
 *
 * @return string
 *        La chaine traduite indiquant la direction du vent.
 */
function rainette_afficher_direction($direction) {

	include_spip('inc/rainette_convertir');
	$direction_abregee = angle2direction($direction);

	if ($direction_abregee) {
		$direction_texte = _T("rainette:direction_${direction_abregee}");
	} else {
		$direction_texte = _T('rainette:valeur_indeterminee');
	}

	return $direction_texte;
}

/**
 * Affiche la tendance de pression selon la méthode demandée (texte en clair, symbole de flèche ou
 * icone).
 *
 * @package    RAINETTE/AFFICHAGE
 * @api
 * @filtre
 *
 * @param string $tendance_en
 * 		Texte anglais représentant la tendance et récupérée par le service.
 * @param string $methode
 * 		Methode d'affichage de la tendance qui prend les valeurs:
 * 		- `texte`   : pour afficher un texte en clair décrivant la tendance (méthode par défaut).
 * 		- `symbole` : pour afficher un symbole de flèche (1 caractère) décrivant la tendance.
 *
 * @return string
 */
function rainette_afficher_tendance($tendance_en, $methode = 'texte') {

	$tendance = '';

	// Certains textes sont composés de plusieurs mots comme "falling rapidly".
	// On en fait un texte unique en remplaçant les espaces par des underscores.
	$tendance_en = str_replace(' ', '_', trim($tendance_en));

	if (($tendance_en) and ($texte = _T("rainette:tendance_texte_$tendance_en", array(), array('force' => false)))) {
		if ($methode == 'texte') {
			$tendance = $texte;
		} else {
			$tendance = _T("rainette:tendance_symbole_$tendance_en");
		}
	}

	return $tendance;
}

/**
 * Affiche toute donnée météorologique au format numérique avec son unité.
 *
 * @package    RAINETTE/AFFICHAGE
 * @api
 * @filtre
 *
 * @param int/float $valeur
 * 		La valeur à afficher
 * @param string    $type_donnee
 *      Type de données à afficher parmi 'temperature', 'pourcentage', 'angle', 'pression',
 *      'distance', 'vitesse', 'population', 'precipitation'.
 * @param int       $precision
 *      Nombre de décimales à afficher pour les réels uniquement ou -1 pour utiliser le défaut.
 * @param string	$service
 *
 * @return string
 *      La chaine calculée ou le texte désignant une valeur indéterminée ou vide si la valeur est null.
 */
function rainette_afficher_unite($valeur, $type_donnee = '', $precision = -1, $service = 'weather') {

	static $precision_defaut = array(
		'temperature'   => 0,
		'pression'      => 1,
		'distance'      => 1,
		'angle'         => 0,
		'pourcentage'   => 0,
		'population'    => 0,
		'precipitation' => 1,
		'vitesse'       => 0,
		'indice'        => 0
	);

	if (!$service) {
		$service = 'weather';
	}
	include_spip('inc/config');
	$unite = lire_config("rainette/${service}/unite", 'm');

	// On distingue la valeur null qui indique que la donnée météo n'est pas fournie par le service avec
	// la valeur '' qui indique que la valeur n'est pas disponible temporairement
	// Dans le cas null on n'affiche pas la valeur, dans le cas '' on affiche la non disponibilité
	if ($valeur === null) {
		$valeur_affichee = '';
	} else {
		$valeur_affichee = _T('rainette:valeur_indeterminee');
		if ($valeur !== '') {
			// Détermination de l'arrondi si la donnée est stockée sous format réel
			if (array_key_exists($type_donnee, $precision_defaut)) {
				$precision = ($precision < 0) ? $precision_defaut[$type_donnee] : $precision;
				$valeur = round($valeur, $precision);
			}

			// Construction de la valeur affichée en fonction de son type. Un indice ne possède pas d'unité.
			$valeur_affichee = strval($valeur);
			if ($type_donnee != 'indice') {
				$suffixe = ($type_donnee == 'population')
					? ''
					: (($unite == 'm') ? 'metrique' : 'standard');
				$espace = in_array($type_donnee, array('temperature', 'pourcentage', 'angle')) ? '' : '&nbsp;';
				$item = 'rainette:unite_' . $type_donnee . ($suffixe ? '_' . $suffixe : '');
				$valeur_affichee .= $espace . _T($item);
			}
		}
	}

	return $valeur_affichee;
}


function rainette_afficher_service($service) {

	// Acquérir la configuration statique du service.
	include_spip("services/${service}");
	$configurer = "${service}_service2configuration";
	$configuration = $configurer('service');

	return $configuration['nom'];
}


/**
 * @param string $mode
 *
 * @return array|string
 */
function rainette_lister_services($mode = 'tableau') {

	static $services = array();

	if (!isset($service[$mode])) {
		// On lit les fichiers php dans répertoire services/ du plugin sachant ce répertoire
		// contient exclusivement les api de chaque service dans un fichier unique appelé
		// alias_du_service.php
		$liste = array();
		if ($fichiers_api = glob(_DIR_PLUGIN_RAINETTE . '/services/*.php')) {
			foreach ($fichiers_api as $_fichier) {
				// On détermine l'alias du service
				$service = strtolower(basename($_fichier, '.php'));

				// Acquérir la configuration statique du service.
				include_spip("services/${service}");
				$configurer = "${service}_service2configuration";
				$configuration = $configurer('service');

				$liste[$service] = $configuration['nom'];
			}
		}

		// Par défaut la liste est fournie comme un tableau.
		// Si le mode demandé est 'liste' on renvoie une chaîne énumérée des alias de service séparée par des virgules.
		$services[$mode] = ($mode == 'tableau') ? $liste : implode(',', array_keys($liste));
	}

	return $services[$mode];
}


/**
 * @param string $mode
 * @param int    $periodicite
 *
 * @return array
 */
function rainette_lister_modeles($mode = 'conditions', $periodicite = 24) {

	$modeles = array();

	// On lit les modèles suivant le mode choisi dans l'ensemble du site.
	// Ceux-ci sont toujours de la forme:
	// -- conditions_<complement>,
	// -- previsions_<periodicite>h_<complement>,
	// -- infos_<complement>.
	if (($mode == 'conditions') or ($mode == 'infos')) {
		$pattern = "${mode}.*\\.html$";
	} else {
		$pattern = "${mode}_${periodicite}h.*\\.html$";
	}
	if ($fichiers = find_all_in_path("modeles/", $pattern)) {
		foreach ($fichiers as $_fichier) {
			$modeles[] = strtolower(basename($_fichier, '.html'));
		}
	}

	return $modeles;
}


/**
 * @param string $service
 * @param string $source
 *
 * @return array
 */
function rainette_lister_themes($service, $source = 'local') {

	static $themes = array();

	if (!isset($themes[$service][$source])) {
		// La liste des thèmes n'a pas encore été enregistrée, il faut la recalculer.
		// On l'initialise à vide car il faut toujours avoir une liste.
		$themes[$service][$source] = array();

		if (strtolower($source) == 'api') {
			// Certains services proposent des thèmes d'icones accessibles via l'API.
			// C'est le cas de wunderground.
			if ($service == 'wunderground') {
				$cles = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k');
				foreach ($cles as $_cle) {
					$themes[$service][$source][$_cle] = _T("rainette:label_theme_wunderground_${_cle}");
				}
			}
		} else {
			// Les thèmes de Rainette sont toujours stockés dans l'arborescence themes/$service.
			// Chaque thème a un alias qui correspond à son dossier et un titre pour l'affichage.
			// On recherche les sous-dossiers themes/$service présents dans le path.
			include_spip('inc/utils');
			include_spip('inc/rainette_normaliser');
			foreach (creer_chemin() as $_chemin) {
				$dossier_service = $_chemin . icone_local_normaliser('', $service);
				if (@is_dir($dossier_service)) {
					if ($dossiers_theme = glob($dossier_service . '/*', GLOB_ONLYDIR)) {
						foreach ($dossiers_theme as $_theme) {
							$theme = strtolower(basename($_theme));
							// On ne garde que le premier dossier de même nom.
							if (!isset($themes[$theme])) {
								$themes[$service][$source][$theme] = $theme;
							}
						}
					}
				}
			}
		}
	}

	return $themes[$service][$source];
}


/**
 * @param string $lieu
 * @param string $mode
 * @param string $modele
 * @param string $service
 * @param array  $options
 *
 * @return array|string
 */
function rainette_coasser($lieu, $mode = 'conditions', $modele = 'conditions_tempsreel', $service = 'weather', $options = array()) {

	// Initialisation du tableau des données météorologiques
	$tableau = array();
	include_spip('inc/rainette_normaliser');

	// Détermination de la périodicité en fonction du mode et du modèle demandés
	$periodicite = 0;
	$erreur = '';
	if ($mode == 'previsions') {
		// Identification de la périodicité à partir du nom du modèle. Cela évite une configuration compliquée.
		if (preg_match(',_(1|12|24)h,is', $modele, $match)) {
			$type_modele = intval($match[1]);

			// On verifie que la périodicité demandée explicitement dans l'appel du modèle est ok
			if (isset($options['periodicite'])) {
				$periodicite_explicite = intval($options['periodicite']);
				if (periodicite_est_compatible($type_modele, $periodicite_explicite)) {
					$periodicite = $periodicite_explicite;
				} else {
					$erreur = 'modele_periodicite';
				}
			} else {
				// Dans ce cas, il faut choisir une périodicité en fonction du type du modèle et du service.
				$periodicite = periodicite_determiner($type_modele, $service);
				if (!$periodicite) {
					$erreur = 'modele_service';
				}
			}
		} else {
			// On ne connait pas le type du modèle, donc sa compatibilité.
			// Si la périodicité est passée en argument on l'utilise sans se poser de question.
			// Sinon c'est une erreur car on ne sait pas quelle périodicité est requise
			if (isset($options['periodicite'])) {
				$periodicite = intval($options['periodicite']);
			} else {
				$erreur = 'modele_inutilisable';
			}
		}
	}

	if ($erreur) {
		// Acquérir la configuration statique du service (periode, format, données...)
		include_spip("services/${service}");
		$configurer = "${service}_service2configuration";
		$configuration = $configurer($mode);

		// On prépare un contexte extras pour traiter les erreurs du modèle de façon standard comme celles
		// renvoyée par le chargement des données.
		$extras['credits'] = $configuration['credits'];
		$extras['config'] = array_merge(
			parametrage_normaliser($service, $configuration['defauts']),
			array('source' => configuration_donnees_normaliser($mode, $configuration['donnees'])),
			array('nom_service' => $configuration['nom'])
		);
		$extras['lieu'] = $lieu;
		$extras['mode'] = $mode;
		$extras['periodicite_cache'] = $periodicite;
		$extras['service'] = $service;
		$extras['erreur'] = array(
			'type' => $erreur,
			'service' => array(
				'code' => '',
				'message' => ''
			)
		);
	} else {
		// Récupération du tableau des données météo
		$charger = charger_fonction('meteo_charger', 'inc');
		$tableau = $charger($lieu, $mode, $periodicite, $service);

		// Séparation des données communes liées au service et au mode et des données météorologiques
		$extras = $tableau['extras'];
		$erreur = $extras['erreur']['type'];

		if (!$erreur and ($mode == 'previsions')) {
			// Adaptation des données en fonction de la demande et de la périodicité modèle-cache
			$nb_index = count($tableau['donnees']);

			$jour1 = 0;
			if (isset($options['premier_jour'])) {
				$jour1 = intval($options['premier_jour']) < $nb_index
					? intval($options['premier_jour'])
					: $nb_index -1;
			}

			$nb_jours = $nb_index - $jour1;
			if (isset($options['nombre_jours'])) {
				$nb_jours = ($jour1 + intval($options['nombre_jours']) <= $nb_index)
					? intval($options['nombre_jours'])
					: $nb_index - $jour1;
			}

			$tableau['premier_jour'] = $jour1;
			$tableau['nombre_jours'] = $nb_jours;
		}
	}

	// Affichage du message d'erreur ou des données
	if ($erreur) {
		$extras['erreur']['texte'] = erreur_formater_texte($extras['erreur'], $lieu, $mode, $modele, $service, $tableau['extras']['config']['nom_service']);
		$texte = recuperer_fond('modeles/erreur_rainette', $extras);
	} else {
		// Appel du modèle avec le contexte complet
		$texte = recuperer_fond("modeles/$modele", $tableau);
	}

	return $texte;
}

include_spip('inc/rainette_debusquer');
