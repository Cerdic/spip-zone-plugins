<?php

// Sécurité
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
 * @param $flux
 *
 * @return mixed
 */
function cache_affiche_milieu($flux) {

	if (isset($flux['args']['exec'])) {
		// Initialisation de la page du privé
		$exec = $flux['args']['exec'];

		if ($exec == 'admin_plugin') {
			// Administration des plugins
			// Supprime la meta du plugin Cache Factory de façon à mettre à jour la configuration des
			// plugins utilisateur si besoin.
			include_spip('inc/cache');
			cache_effacer_configuration();
		}
	}

	return $flux;
}
