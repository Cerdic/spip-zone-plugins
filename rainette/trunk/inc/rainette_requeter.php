<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 *
 * @param string $url
 * @param string $format_reponse
 * @param string $service
 *
 * @return array
 */
function requeter($url, $format_reponse, $service) {

	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$flux = recuperer_url($url, array('transcoder' => true));

	// On loge l'appel et on incrémente les compteurs de requêtes du service.

	// Initialisation de la réponse et du bloc d'erreur normalisé.
	$reponse = array();
	if (empty($flux['page'])) {
		spip_log("URL indiponible : ${url}", 'rainette');
		$reponse['erreur'] = 'url_indisponible';
	} else {
		// Tranformation de la chaine xml reçue en tableau associatif
		if ($format_reponse == 'xml') {
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


function requete_autorisee($service) {

	$autorisee = true;

	return $autorisee;
}
