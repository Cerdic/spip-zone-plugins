<?php

/**
 * Aller récupérer toutes les releases de logiciel des sites de projets
 *
 * @plugin     Info Sites
 * @copyright  2014-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\RecupererSpip
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function genie_recuperer_releases_dist($t) {
	include_spip('inc/utils');
	include_spip('inc/flock');
	include_spip('info_sites_fonctions');

	$liste_logiciels = info_sites_lister_logiciels_sites();

	if (is_array($liste_logiciels) and count($liste_logiciels) > 0) {
		foreach ($liste_logiciels as $logiciel) {
			$logiciel = strtolower($logiciel); // on met tout le monde au pas… minuscule
			$logiciel = preg_replace("/(&#38;nbsp;|\p{P}|\h)+/", '', $logiciel); // Et surtout, on ne veut pas de ponctuation dans le nom du logiciel… N'est-ce pas Joomla! ?
			$fichier_fonction = find_all_in_path('recuperer/', 'releases_' . $logiciel . '.php$');
			spip_log(print_r($fichier_fonction, true), 'info_sites');
			if (is_array($fichier_fonction) and count($fichier_fonction) > 0) {
				$releases_logiciel = charger_fonction('releases_' . $logiciel, 'recuperer');
				$versions_logiciel = $releases_logiciel();
				if (is_array($versions_logiciel) and count($versions_logiciel) > 0) {
					spip_log(_DIR_TMP . 'releases_' . $logiciel, 'info_sites');
					ecrire_fichier(_DIR_TMP . 'releases_' . $logiciel . '.txt', serialize($versions_logiciel));
				}
			}
		}
	}

	return $t;
}
