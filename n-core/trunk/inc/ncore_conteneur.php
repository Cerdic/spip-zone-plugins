<?php
/**
 * Ce fichier contient l'API N-Core de gestion des conteneurs.
 *
 * @package SPIP\NCORE\CONTENEUR\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Supprime toutes les noisettes d’un conteneur.
 * L'éventuelle imbrication de conteneurs est gérée dans la fonction de service ncore_conteneur_destocker().
 *
 * @api
 * @uses ncore_conteneur_destocker()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array|string $conteneur
 *        Tableau descriptif du conteneur ou identifiant du conteneur.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé. Dans ce cas, ni celui du plugin
 *        ni celui de N-Core ne seront utilisés. En général, cet identifiant est le préfixe d'un plugin
 *        fournissant le service de stockage souhaité.
 *
 * @return bool
 */
function conteneur_vider($plugin, $conteneur, $stockage = '') {

	// Initialisation du retour
	$retour = false;

	if ($conteneur) {
		// On charge l'API de N-Core.
		// Ce sont ces fonctions qui aiguillent ou pas vers une fonction spécifique du service.
		include_spip('ncore/ncore');
		$retour = ncore_conteneur_destocker($plugin, $conteneur, $stockage);
	}

	return $retour;
}
