<?php
/**
 * Ce fichier contient la fonction de requêtage vers un service Web.
 *
 * @package SPIP\GEOGRAPHIE\REQUETE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Renvoie, à partir de l'url du service, le tableau des données demandées.
 * Le service utilise dans ce cas une chaine JSON qui est décodée pour fournir
 * le tableau de sortie. Le flux retourné par le service est systématiquement
 * transcodé dans le charset du site avant d'être décodé.
 *
 * @uses recuperer_url()
 *
 * @param string   $url
 *        URL complète de la requête au service web concerné.
 * @param int|null $taille_max
 *        Taille maximale du flux récupéré suite à la requête.
 *        `null` désigne la taille par défaut.
 *
 * @return array
 */
function inc_geographie_requeter_dist($url, $taille_max = null) {

	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$options = array(
		'transcoder' => true,
		'taille_max' => $taille_max);
	$flux = recuperer_url($url, $options);

	$reponse = array();
	if (empty($flux['page'])) {
		spip_log("URL indiponible : ${url}", 'geographie');
		$reponse['erreur'] = 'url_indisponible';
	} else {
		// Transformation de la chaîne json reçue en tableau associatif
		try {
			$reponse = json_decode($flux['page'], true);
		} catch (Exception $erreur) {
			$reponse['erreur'] = 'analyse_json';
			spip_log("Erreur d'analyse JSON pour l'URL `${url}` : " . $erreur->getMessage(), 'geographie' . _LOG_ERREUR);
		}
	}

	return $reponse;
}
