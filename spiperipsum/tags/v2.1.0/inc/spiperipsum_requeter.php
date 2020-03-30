<?php
/**
 * Ce fichier contient les fonctions internes de gestion des requêtes vers les services de Spiper Ipsum.
 *
 * @package SPIP\SPIPERIPSUM\REQUETE
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fait appel au service spécifié en utilisant l'URL fournie et retourne le flux brut JSON ou XML transcodé dans un tableau.
 *
 * @param string $url
 * 		URL complète de la requête formatée en fonction de la demande et du service.
 * @param string $format
 *      Format `json` (par défaut) ou `xml` du flux récupéré.
 *
 * @return array
 *      Tableau des données évangéliques retournées par le service ou tableau limité à l'index `erreur` en cas
 *      d'erreur de transcodage.
 */
function inc_spiperipsum_requeter_dist($url, $format = 'json') {

	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$flux = recuperer_url($url, array('transcoder' => true));

	// Initialisation de la réponse et du bloc d'erreur normalisé.
	$reponse = array();
	if (empty($flux['page'])) {
		spip_log("URL indiponible : ${url}", 'spiperipsum');
		$reponse['erreur'] = 'url_indisponible';
	} else {
		// Transformation de la chaîne xml reçue en tableau associatif
		if ($format == 'xml') {
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
				spip_log("Erreur d'analyse XML pour l'URL `${url}` : " . $erreur->getMessage(), 'spiperipsum' . _LOG_ERREUR);
			}

			restore_error_handler();
		} else {
			// Transformation de la chaîne json reçue en tableau associatif
			try {
				$reponse = json_decode($flux['page'], true);
			} catch (Exception $erreur) {
				$reponse['erreur'] = 'analyse_json';
				spip_log("Erreur d'analyse JSON pour l'URL `${url}` : " . $erreur->getMessage(), 'spiperipsum' . _LOG_ERREUR);
			}
		}
	}

	return $reponse;
}
