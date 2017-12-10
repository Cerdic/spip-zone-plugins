<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 *
 * @param string $url
 * @param array  $configuration
 * @param string $service
 *
 * @return array
 */
function requeter($url, $configuration, $service) {

	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$flux = recuperer_url($url, array('transcoder' => true));

	// On loge la date de l'appel et on incrémente les compteurs de requêtes du service.
	include_spip('inc/config');
	$execution = lire_config('rainette_execution', array());
	$execution[$service]['dernier_appel'] = date('Y-m-d H:i:s');
	if (!isset($execution[$service]['compteurs'])) {
		// On initialise les compteurs de requêtes.
		$execution[$service]['compteurs'] = array();
	}
	// On met à jour tous les compteurs
	if ($configuration['limites']) {
		foreach ($configuration['limites'] as $_periode => $_seuil) {
			$execution[$service]['compteurs'][$_periode] = isset($execution[$service]['compteurs'][$_periode])
				? $execution[$service]['compteurs'][$_periode] + 1
				: 1;
		}
	}
	ecrire_config('rainette_execution', $execution);

	// Initialisation de la réponse et du bloc d'erreur normalisé.
	$reponse = array();
	if (empty($flux['page'])) {
		spip_log("URL indiponible : ${url}", 'rainette');
		$reponse['erreur'] = 'url_indisponible';
	} else {
		// Tranformation de la chaine xml reçue en tableau associatif
		if ($configuration['format_flux'] == 'xml') {
			$convertir = charger_fonction('simplexml_to_array', 'inc');

			// Pouvoir attraper les erreurs de simplexml_load_string().
			// http://stackoverflow.com/questions/17009045/how-do-i-handle-warning-simplexmlelement-construct/17012247#17012247
			set_error_handler(
				function($erreur_id, $erreur_message, $erreur_fichier, $erreur_ligne) {
					throw new Exception($erreur_message, $erreur_id);
				}
			);

			try {
				$reponse = $convertir(simplexml_load_string($flux['page']), false);
				$reponse = $reponse['root'];
			} catch (Exception $erreur) {
				$reponse['erreur'] = 'analyse_xml';
				restore_error_handler();
				spip_log("Erreur d'analyse XML pour l'URL `${url}` : " . $erreur->getMessage(), 'rainette' . _LOG_ERREUR);
			}

			restore_error_handler();
		} else {
			// Tranformation de la chaine json reçue en tableau associatif
			try {
				$reponse = json_decode($flux['page'], true);
			} catch (Exception $erreur) {
				$reponse['erreur'] = 'analyse_json';
				spip_log("Erreur d'analyse JSON pour l'URL `${url}` : " . $erreur->getMessage(), 'rainette' . _LOG_ERREUR);
			}
		}
	}

	return $reponse;
}


/**
 * @param array  $limites
 * @param string $service
 *
 * @return bool
 */
function requete_autorisee($limites, $service) {

	$autorisee = true;

	// On loge la date de l'appel et on incrémente le compteur de requêtes du service.
	include_spip('inc/config');
	$execution = lire_config('rainette_execution', array());
	if (isset($execution[$service])) {
		// Si aucune information d'exécution n'a été logée on considère que c'est la mise en route du service
		// et donc que la requête est forcément autorisée.
		$dernier_appel = date_parse($execution[$service]['dernier_appel']);
		$date_courante = date_parse(date('Y-m-d H:i:s'));

		if ($limites) {
			$periode_a_change = false;
			foreach ($limites as $_periode => $_max) {
				// La date courante est un tableau indexé par la période de l'année (year) à la minute (minute) et plus.
				// La stratégie est de vérifier - de l'année à la période configurée pour le service - si un élément a changé
				// ou pas : si un élément a changé alors on est forcément ok, sinon on vérifie le nombre d'appels comparé
				// à la valeur max configurée.
				if ($periode_a_change) {
					// Toutes les autres périodes inférieures ont donc changé aussi, on remet donc leur compteur
					// à zéro.
					$execution[$service]['compteurs'][$_periode] = 0;
					ecrire_config('rainette_execution', $execution);
				} else {
					foreach ($date_courante as $_cle => $_valeur) {
						if ($_valeur != $dernier_appel[$_cle]) {
							// Période de temps supérieure ou égale à celle configurée pour le service a changé.
							// On est donc forcément dans la limite d'utilisation.
							// Il faut remettre le compteur de la période à zéro.
							$execution[$service]['compteurs'][$_periode] = 0;
							ecrire_config('rainette_execution', $execution);
							$periode_a_change = true;
							break;
						} elseif ($_cle == $_periode) {
							// On est arrivé à la période configurée pour le service et la valeur est la même :
							// il faut donc vérifier si on a atteint ou pas la limite autorisée du nombre de requête pour cette
							// période de temps.
							if ($execution[$service]['compteurs'][$_periode] == $_max) {
								$autorisee = false;
							}
							// On est arrivé à la période de la date courante au delà duquel il n'y a plus besoin de continuer.
							break;
						}
					}
				}
			}
		}
	}

	return $autorisee;
}
