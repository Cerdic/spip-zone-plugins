<?php
/**
 * Ce fichier contient les cas d'utilisation de certains pipelines par le plugin Cache Factory.
 *
 * @package    SPIP\CACHE\PIPELINE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Supprime la meta stockant la configuration des caches de tous les plugins utilisateur quand
 * la page d'administration des plugins est affiché.
 *
 * C'est un peu grossier mais il n'existe pas de pipeline pour agir à la mise à jour d'un plugin.
 * Au moins, cela permet facilement de recharger une configuration d'un plugin utilisateur qui aurait changée
 * sans être une opération trop récurrente.
 *
 * @uses configuration_cache_lire()
 * @uses configuration_cache_effacer()
 * @uses cache_cache_configurer()
 *
 * @param $flux
 *        Tableau des données permettant de caractériser la page concernée et de déclencher le traitement uniquement
 *        sur la page `admin_plugin`.
 *
 * @return mixed
 *         Le flux entrant n'est pas modifié.
 */
function cache_affiche_milieu($flux) {

	if (isset($flux['args']['exec'])) {
		// Initialisation de la page du privé
		$exec = $flux['args']['exec'];

		if ($exec == 'admin_plugin') {
			// Administration des plugins

			// Supprime la meta du plugin Cache Factory de façon à mettre à jour la configuration des
			// plugins utilisateur si besoin.
			// Recharge la configuration des plugins utilisateur :
			// -- on lit la meta pour obtenir la liste des plugins
			include_spip('inc/cache');
			$configuration = configuration_cache_lire();
			if ($configuration) {
				$plugins = array_keys($configuration);
				// -- on supprime la meta
				configuration_cache_effacer();
				// -- on reconfigure chaque plugin
				include_spip('cache/cache');
				foreach ($plugins as $_plugin) {
					if (defined('_DIR_PLUGIN_' . strtoupper($_plugin))) {
						cache_cache_configurer($_plugin);
					}
				}
			}
		}
	}

	return $flux;
}
