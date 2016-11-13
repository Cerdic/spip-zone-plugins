<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_DEBUG')) {
	define('_RAINETTE_DEBUG', false);
}


/**
 * @param string $lieu
 * @param string $mode
 * @param int    $periodicite
 * @param string $service
 *
 * @return string
 */
function rainette_dbg_afficher_cache($lieu, $mode = 'previsions', $periodicite = 0, $service = 'weather') {
	$debug = '';

	// Recuperation du tableau des conditions courantes
	if (_RAINETTE_DEBUG) {
		$charger = charger_fonction('charger_meteo', 'inc');
		$nom_cache = $charger($lieu, $mode, $periodicite, $service);
		if ($nom_cache) {
			$contenu = '';
			lire_fichier($nom_cache, $contenu);
			$tableau = unserialize($contenu);

			$debug = afficher_tableau(serialize($tableau));
		}
	}

	return $debug;
}


/**
 * @param string $mode
 * @param array  $jeu
 *
 * @return array
 */
function rainette_dbg_comparer_services($mode = 'conditions', $jeu = array()) {
	$debug = array();

	if (!$mode) {
		$mode = 'conditions';
	}

	// On acquiert la structure exact des tableaux de données standard
	include_spip('inc/rainette_normaliser');
	$config_donnees = $GLOBALS['rainette_config'][$mode];

	if ($config_donnees) {
		if (!$jeu) {
			$jeu = array(
				'weather'      => 'FRXX0076',
				'owm'          => 'Paris,Fr',
				'wwo'          => 'Paris,France',
				'wunderground' => 'Paris,France'
			);
		}

		// On boucle sur chaque jeu de demo
		include_spip('inc/utils');
		$periodicite = 0;
		foreach ($jeu as $_service => $_lieu) {
			// Si on est en mode prévisions, on impose la périodicité à la valeur par défaut pour le service
			if ($mode == 'previsions') {
				include_spip("services/${_service}");
				$configurer = "${_service}_service2configuration";
				$configuration = $configurer($mode);
				$periodicite = $configuration['periodicite_defaut'];
			}

			// Chargement des données
			$charger = charger_fonction('charger_meteo', 'inc');
			$nom_cache = $charger($_lieu, $mode, $periodicite, $_service);
			lire_fichier($nom_cache, $contenu_cache);
			$tableau = unserialize($contenu_cache);

			if (!$tableau['extras']['erreur']) {
				// Suivant le mode on extrait les données à afficher. Pour le mode prévisions, on choisit le
				// jour suivant le jour courant et pour les données heures l'index 0 qui existe toujours.
				// En outre, il faut tenir compte pour les prévisions qu'il existe des données "jour" et
				// des données "heure" alors que pour les autres modes ll n'existe que des données "jour".
				$tableau_jour = ($mode == 'previsions') ? $tableau['donnees'][1] : $tableau['donnees'];
				foreach ($config_donnees as $_donnee => $_config) {
					// On détermine la valeur et le type PHP de la données dans le tableau standard et
					// on stocke les informations de configuration rangement pour les prévisions
					if (isset($_config['rangement']) and ($_config['rangement'] == 'heure')) {
						$valeur = $tableau_jour['heure'][0][$_donnee];
						$type_php = gettype($tableau_jour['heure'][0][$_donnee]);
						$rangement = $_config['rangement'];
					} else {
						$valeur = $tableau_jour[$_donnee];
						$type_php = gettype($tableau_jour[$_donnee]);
						$rangement = '';
					}

					// On construit le tableau de debug
					$debug[$_donnee][$_service]['valeur'] = $valeur;
					if (($_donnee == 'icon_meteo') and tester_url_absolue($valeur)) {
						$debug[$_donnee][$_service]['valeur'] = dirname($valeur) . '/<br />' . basename($valeur);
					}
					$debug[$_donnee][$_service]['type_php'] = $type_php;
					$debug[$_donnee][$_service]['rangement'] = $rangement;
					$debug[$_donnee][$_service]['groupe'] = $_config['groupe'];
					$debug[$_donnee][$_service]['type_unite'] = $_config['type_unite'];
					if ($_donnee != 'erreur') {
						$debug[$_donnee][$_service]['erreur'] = ($type_php === 'NULL') ? 'nonapi' : ($valeur === '' ? 'erreur' : '');
					} else {
						$debug[$_donnee][$_service]['erreur'] = $valeur ? 'erreur' : '';
					}
				}
			}
		}
	}

	return $debug;
}


/**
 * @param string $donnee
 * @param mixed  $valeur
 * @param string $type_php
 * @param string $type_unite
 * @param string $service
 *
 * @return string
 */
function rainette_dbg_afficher_donnee($donnee, $valeur, $type_php, $type_unite, $service = 'weather') {
	$texte = '';

	if ($type_php === 'NULL') {
		$texte = '<del>API</del>';
		$type_php = 'null';
	} elseif ($valeur === '') {
		if ($donnee != 'erreur') {
			$texte = 'Indisponible';
		} else {
			$texte = $valeur ? $valeur : 'Ok';
		}
	} elseif ($type_php === 'array') {
		foreach ($valeur as $_cle => $_valeur) {
			$texte .= ($texte ? '<br />' : '') . "<strong>${_cle}</strong> : " . ((gettype($_valeur) === null) ? '<del>API</del>' : $_valeur);
		}
	} else {
		$texte = $type_unite ? rainette_afficher_unite($valeur, $type_unite, -1, $service) : $valeur;
	}
	$texte .= "<br /><em>${type_php}</em>";

	return $texte;
}

/**
 * Une fonction récursive pour joliment afficher #ENV, #GET, #SESSION...
 *		en squelette : [(#ENV|bel_env)], [(#GET|bel_env)], [(#SESSION|bel_env)]
 *		ou encore [(#ARRAY{0,1, a,#SESSION, 1,#ARRAY{x,y}}|bel_env)]
 *
 * @param string|array $env
 *		si une string est passée elle doit être le serialize d'un array
 *
 * @return string
 *		une chaîne html affichant une <table>
**/
function afficher_tableau($env) {
//	$env = str_replace(array('&quot;', '&#039;'), array('"', '\''), $env);
	if (is_array($env_tab = @unserialize($env))) {
		$env = $env_tab;
	}
	if (!is_array($env)) {
		return '';
	}
	$style = " style='border:1px solid #ddd;'";
	$res = "<table style='border-collapse:collapse;'>\n";
	foreach ($env as $nom => $val) {
		if (is_array($val) || is_array(@unserialize($val))) {
			$val = bel_env($val);
		}
		elseif ($val === null) {
			$val = '<i>null</i>';
		}
		elseif ($val === '') {
			$val = "<i>''</i>";
		}
		else {
			$val = entites_html($val);
		}
		$res .= "<tr>\n<td$style><strong>". entites_html($nom).
				"&nbsp;:&nbsp;</strong></td><td$style>" .$val. "</td>\n</tr>\n";
	}
	$res .= "</table>";
	return $res;
}
