<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_RAINETTE_DEBUG')) {
	/**
	 * Activation ou désactivation des traces de debug.
	 */
	define('_RAINETTE_DEBUG', false);
}
if (!defined('_RAINETTE_DEBUG_CLES_PREVISIONS')) {
	/**
	 * Clé jour par défaut utilisée pour afficher les prévisions : jour suivant.
	 */
	define('_RAINETTE_DEBUG_CLES_PREVISIONS', '1');
}


/**
 * @param string $lieu
 * @param string $mode
 * @param string $service
 *
 * @return string
 */
function rainette_debug_afficher_cache($lieu, $mode = 'previsions', $service = 'weather') {
	static $cles_previsions = array();
	$debug = '';

	// Recuperation du tableau des conditions courantes
	if (_RAINETTE_DEBUG and function_exists('bel_env')) {
		// Si on est en mode prévisions, on impose la périodicité à la valeur par défaut pour le service
		$periodicite = 0;
		if ($mode == 'previsions') {
			include_spip("services/${service}");
			$configurer = "${service}_service2configuration";
			$configuration = $configurer($mode);
			$periodicite = $configuration['periodicite_defaut'];
		}

		// Chargement du cache
		$charger = charger_fonction('meteo_charger', 'inc');
		$tableau = $charger($lieu, $mode, $periodicite, $service);

		// Pour le mode prévisions, on supprime tous les jours postérieur au lendemain pour éviter d'avoir un
		// affichage trop conséquent.
		if ($mode == 'previsions') {
			// Récupérer les index de prévisions à afficher
			if (!$cles_previsions) {
				$cles_previsions = explode(',', _RAINETTE_DEBUG_CLES_PREVISIONS);
			}
			foreach ($tableau['donnees'] as $_jour => $_valeurs) {
				if (!in_array($_jour, $cles_previsions)) {
					unset($tableau['donnees'][$_jour]);
				}
			}
		}
		$debug = bel_env(serialize($tableau), true);
	}

	return $debug;
}


/**
 * @param string $lieu
 * @param string $mode
 * @param string $service
 *
 * @return string
 */
function rainette_debug_afficher_config() {
	$debug = '';

	// Recuperation du tableau des conditions courantes
	if (_RAINETTE_DEBUG and function_exists('bel_env')) {
		include_spip('inc/config');
		$execution = lire_config('rainette_execution', array());
		$debug = bel_env(serialize($execution), true);
	}

	return $debug;
}


/**
 * @param string $mode
 * @param array  $jeu
 *
 * @return array
 */
function rainette_debug_comparer_services($mode = 'conditions', $jeu = array()) {
	$debug = array();

	if (!$mode) {
		$mode = 'conditions';
	}

	// On acquiert la structure exact des tableaux de données standard
	include_spip('inc/rainette_normaliser');
	$config_donnees = $GLOBALS['rainette_config'][$mode];

	if ($config_donnees) {
		if (!$jeu) {
			$jeu = rainette_debug_jeu_defaut();
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
			$charger = charger_fonction('meteo_charger', 'inc');
			$tableau = $charger($_lieu, $mode, $periodicite, $_service);

			if (!$tableau['extras']['erreur']['type']) {
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
						$rangement = isset($_config['rangement']) ? $_config['rangement'] : '';
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
function rainette_debug_afficher_donnee($donnee, $valeur, $type_php, $type_unite, $service = 'weather') {
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
 * @return array
 */
function rainette_debug_jeu_defaut() {

	$jeu = array();

	include_spip('rainette_fonctions');
	$services = rainette_lister_services();
	if ($services) {
		foreach ($services as $_service => $_nom) {
			if (($_service == 'weather') or ($_service == 'wunderground')) {
				$jeu[$_service] = 'FRXX0076';
			} else {
				$jeu[$_service] = 'Paris,France';
			}
		}
	}

	return $jeu;
}

/**
 * @return array
 */
function rainette_debug_afficher_execution() {

	$debug = array();

	$services = rainette_lister_services();
	if ($services) {
		include_spip('inc/config');
		$execution = lire_config('rainette_execution', array());

		foreach ($services as $_service => $_nom) {
			include_spip("services/${_service}");
			$configurer = "${_service}_service2configuration";
			$configuration = $configurer('infos');

			$debug[$_service]['dernier_appel'] = isset($execution[$_service]['dernier_appel'])
				? $execution[$_service]['dernier_appel']
				: '--';
			foreach (array('year', 'month', 'day', 'hour', 'minute') as $_periode) {
				if (isset($configuration['offres']['limites'][$_periode])) {
					$compteur = isset($execution[$_service]['compteurs'][$_periode])
						? $execution[$_service]['compteurs'][$_periode]
						: '--';
					$debug[$_service][$_periode] = "${compteur} / {$configuration['offres']['limites'][$_periode]}";
				} else {
					$debug[$_service][$_periode] = '';
				}
			}
		}
	}

	return $debug;
}

/**
 * Fonction permettant de vérifier si la liste des langues de SPIP a changé et qu'il faut modifier la config rainette.
 *
 */
function rainette_debug_verifier_langue_manquante() {
	include_spip('inc/lang_liste');
	include_spip('inc/rainette_normaliser');
	foreach ($GLOBALS['codes_langues'] as $code => $langue) {
		if (!array_key_exists($code, $GLOBALS['rainette_config']['langues_alternatives'])) {
			echo "code manquant $code<br />";
		}
	}
}
