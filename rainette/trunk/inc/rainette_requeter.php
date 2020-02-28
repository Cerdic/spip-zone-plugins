<?php
/**
 * Ce fichier contient les fonctions internes de gestion des requêtes vers les services météorologiques.
 *
 * @package SPIP\RAINETTE\REQUETE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fait appel au service spécifié en utilisant l'URL fournie et retourne le flux brut JSON ou XML transcodé dans un tableau.
 * Chaque appel est comptabilisé et logé dans une meta.
 *
 * @param string $url
 *                              URL complète de la requête formatée en fonction de la demande et du service.
 * @param array  $configuration
 *                              Configuration statique et utilisateur du service nécessaire pour identifier les seuils de requêtes
 *                              par période propres au service et le format du flux pour le transcodage.
 * @param string $service
 *                              Alias du service.
 *
 * @return array
 *               Tableau des données météorologiques retournées par le service ou tableau limité à l'index `erreur` en cas
 *               d'erreur de transcodage.
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
	if ($configuration['offres']['limites']) {
		foreach ($configuration['offres']['limites'] as $_periode => $_seuil) {
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
	} elseif ($configuration['format_flux'] == 'xml') {
		// Transformation de la chaîne xml reçue en tableau associatif
		$convertir = charger_fonction('simplexml_to_array', 'inc');

		// Pouvoir attraper les erreurs de simplexml_load_string().
		// http://stackoverflow.com/questions/17009045/how-do-i-handle-warning-simplexmlelement-construct/17012247#17012247
		set_error_handler(
			function ($erreur_id, $erreur_message, $erreur_fichier, $erreur_ligne) {
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
		// Transformation de la chaîne json reçue en tableau associatif
		try {
			$reponse = json_decode($flux['page'], true);
		} catch (Exception $erreur) {
			$reponse['erreur'] = 'analyse_json';
			spip_log("Erreur d'analyse JSON pour l'URL `${url}` : " . $erreur->getMessage(), 'rainette' . _LOG_ERREUR);
		}
	}

	return $reponse;
}

/**
 * Vérifie si la requête prévue peut être adressée au service sans excéder les limites d'utilisation fixées dans
 * les conditions d'utilisation du service.
 * Si une période est échue, la fonction remet à zéro le compteur associé.
 *
 * @param array  $limites
 *                        Tableau des seuils de requêtes par période (année, mois,..., minute).
 * @param string $service
 *                        Alias du service.
 *
 * @return bool
 *              `true` si la requête est autorisée, `false`sinon.
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
			$nouvelle_periode = false;
			foreach ($limites as $_periode => $_max) {
				// La date courante est un tableau indexé par la période de l'année (year) à la minute (minute) et plus.
				// La stratégie est de vérifier - de l'année à la période configurée pour le service - si un élément a changé
				// ou pas : si un élément a changé alors on est forcément ok, sinon on vérifie le nombre d'appels comparé
				// à la valeur max configurée.
				if ($nouvelle_periode) {
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
							$nouvelle_periode = true;
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
