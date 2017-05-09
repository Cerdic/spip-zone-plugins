<?php

/**
 * Utilisations de pipelines par Déréférencer les médias.
 *
 * @plugin     Déréférencer les médias
 *
 * @copyright  2015-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * On se greffe au pipeline taches_generales_cron pour lancer nos tâches.
 *
 * @param array $taches
 *
 * @return array
 */
function medias_dereferencer_taches_generales_cron($taches) {
	$taches['medias_dereferencer'] = 24 * 3600; // toutes les 24h
	$taches['medias_dereferencer_vu'] = 24 * 3600; // toutes les 24h
	if (defined('_DUREE_CACHE_DEFAUT')) {
		$taches['medias_dereferencer_htaccess'] = _DUREE_CACHE_DEFAUT; // On utilise le cache par défaut défini dans SPIP.
	} else {
		$taches['medias_dereferencer_htaccess'] = 24 * 3600; // toutes les 24h
	}

	return $taches;
}
