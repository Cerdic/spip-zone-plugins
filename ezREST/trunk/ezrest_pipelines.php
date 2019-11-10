<?php
/**
 * Ce fichier contient les cas d'utilisation de certains pipelines par le plugin Cache Factory.
 *
 * @package    SPIP\EZREST\PIPELINE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ajoute ou retire le cache créé ou supprimé de l'index des caches REST Factory.
 *
 * Les informations contenues permettent l'affichage précis dans le formulaire de vidage.
 *
 * @param $flux
 *        Tableau des données permettant de caractériser la page concernée et de déclencher le traitement uniquement
 *        sur la page `admin_plugin`.
 *
 * @return mixed
 *         Le flux entrant n'est pas modifié.
 */
function ezrest_post_cache($flux) {

	// Identification du fichier d'index des caches
	$configuration = $flux['args']['configuration'];
	$fichier_index = constant($configuration['racine']) . $configuration['dossier_plugin'] . 'index.txt';

	// Lecture du fichier d'index et récupération du tableau des caches.
	$contenu_index = '';
	lire_fichier($fichier_index, $contenu_index);
	$index = $contenu_index ? unserialize($contenu_index) : array();

	// Extraction du fichier cache : on utilise juste le nom et le répertoire du plugin ce qui suffit pour être unique.
	$fichier_cache = basename(dirname($flux['args']['fichier_cache'])) . '/' . basename($flux['args']['fichier_cache']);

	if ($flux['args']['fonction'] == 'ecrire') {
		// On vient d'écrire un cache, on le loge dans l'index.
		$index[$fichier_cache] = $flux['args']['cache'];

	} elseif ($flux['args']['fonction'] == 'supprimer') {
		// On vient de supprimer un cache, on le retire de l'index.
		if ($index and isset($index[$fichier_cache])) {
			unset($index[$fichier_cache]);
		}
	}

	// Mise à jour de l'index
	ecrire_fichier($fichier_index, serialize($index));

	return $flux;
}
