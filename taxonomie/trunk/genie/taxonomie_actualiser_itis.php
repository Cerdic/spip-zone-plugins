<?php
/**
 * CRON de mise à jour des taxons en fonction des fichiers de chaque règne embarqué.
 *
 * @package    SPIP\TAXONOMIE\SERVICES\ITIS
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ce CRON permet de vérifier si les fichiers ITIS ayant servis à créer la base de taxons initiale
 * ont été modifiés et si oui de déclencher une mise à niveau des taxons.
 *
 * Etant donné que les sha des fichiers sont enregistrés dans la meta de chaque règne, la fonction
 * compare ces valeurs aux sha des fichiers embarqués dans le plugin. Les fichiers des taxons de chaque règne
 * ainsi que les fichiers de traductions sont pris en compte.
 * Si un fichier a changé on recharge le ou les règnes concernés.
 *
 * @uses itis_review_sha()
 * @uses regne_existe()
 * @uses regne_charger()
 *
 * @param int $last
 *        Timestamp de la date de dernier appel de la tâche.
 *
 * @return int
 *        Timestamp de la date du prochain appel de la tâche.
 */
function genie_taxonomie_actualiser_itis_dist($last) {

	include_spip('inc/taxonomie');
	$regnes = regne_lister_defaut();

	include_spip('services/itis/itis_api');
	$shas = itis_review_sha();

	foreach ($regnes as $_regne) {
		$regne_a_recharger = false;
		if (regne_existe($_regne, $meta_regne)) {
			// On compare le sha du fichier des taxons
			if ($meta_regne['sha'] != $shas['taxons'][$_regne]) {
				$regne_a_recharger = true;
			} else {
				// On compare le sha des fichiers de traductions
				foreach ($meta_regne['traductions']['itis'] as $_code => $_infos) {
					if ($_infos['sha'] != $shas['traductions'][$_code]) {
						$regne_a_recharger = true;
						break;
					}
				}
			}
			if ($regne_a_recharger) {
				$langues = array_keys($meta_regne['traductions']['itis']);
				regne_charger($_regne, $langues);
			}
		}
	}

	return 1;
}
