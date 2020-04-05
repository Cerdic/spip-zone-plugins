<?php
/**
 * Faire quelques nettoyages de la bdd sur les sites de projets
 * Enumération :
 * - enlever les espaces inutiles sur les noms et versions de logiciels, les titres de sites ;
 *
 * @plugin     Sites pour projets
 * @copyright  2013-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Genie
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function genie_projetssites_nettoyage_dist($t) {
	include_spip('base/abstract_sql');
	include_spip('inc/utils');

	/**
	 * On sélectionne tous les sites projets de la bdd
	 **/
	$projets_sites = sql_allfetsel('id_projets_site, titre, logiciel_nom, logiciel_version', 'spip_projets_sites');
	$raccourcis_logiciels_noms = pipeline('lister_logiciels_noms', array(
		'args' => array(),
		'data' => array(),
	));

	if (is_array($projets_sites) and count($projets_sites) > 0) {
		foreach ($projets_sites as $projets_site) {

			$champs_update = array();
			$cas_update = array();
			$titre_new = trim($projets_site['titre']);
			$logiciel_nom_new = trim($projets_site['logiciel_nom']);
			$logiciel_version_new = trim($projets_site['logiciel_version']);

			if ($titre_new !== $projets_site['titre']) {
				$champs_update['titre'] = $titre_new;
				$cas_update[] = 'Update titre sans espace';
			}
			if ($logiciel_nom_new !== $projets_site['logiciel_nom']) {
				$champs_update['logiciel_nom'] = $logiciel_nom_new;
				$cas_update[] = 'Update logiciel_nom sans espace';
			}
			if (array_key_exists($logiciel_nom_new, $raccourcis_logiciels_noms)
				and $logiciel_nom_new !== $raccourcis_logiciels_noms[$logiciel_nom_new]
			) {
				$champs_update['logiciel_nom'] = trim($raccourcis_logiciels_noms[$logiciel_nom_new]);
				$cas_update[] = 'Update logiciel_nom selon raccourcis';
			}
			if ($logiciel_version_new != $projets_site['logiciel_version']) {
				$champs_update['logiciel_version'] = $logiciel_version_new;
				$cas_update[] = 'Update logiciel_version sans espace';
			}
			if (count($champs_update) > 0) {
				/**
				 * On a des champs à mettre à jour…
				 */
				$update = sql_updateq('spip_projets_sites', $champs_update,
					'id_projets_site=' . $projets_site['id_projets_site']);
				if ($update) {
					spip_log('Le site de projet #'
						. $projets_site['id_projets_site']
						. " a été mis à jour avec ces valeurs :\n"
						. print_r($champs_update, true)
						. "\n" . print_r($cas_update, true), 'projets_sites');
				}
			}

		}
	}

}

