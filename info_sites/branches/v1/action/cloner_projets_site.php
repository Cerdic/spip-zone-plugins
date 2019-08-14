<?php

/**
 * Cloner un site de projet
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

function action_cloner_projets_site_dist($arg = null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if (intval($arg)) {
		include_spip('base/abstract_sql');
		/* récupérer tous les champs du site */
		$site_a_cloner = sql_fetsel('*', 'spip_projets_sites', 'id_projets_site='.$arg);
		/* récupérer tous les liens du site à cloner */
		$site_liens_a_cloner = sql_allfetsel('objet,id_objet,vu', 'spip_projets_sites_liens', 'id_projets_site='.$arg);
		/* on ne garde pas l'id du site d'origine ni la date de maj */
		unset($site_a_cloner['id_projets_site']);
		unset($site_a_cloner['maj']);
		/* On ajoute en début de titre le terme "Clone" pour identification rapide */
		$site_a_cloner['titre'] = "Clone: " . $site_a_cloner['titre'];
		/* On insère tout ça dans la table */
		$site_new = sql_insertq('spip_projets_sites', $site_a_cloner);
		/* Si on a bien des liens et un id_new, on peut recréer les liens vers les objets dans spip_projets_sites_liens */
		if (is_array($site_liens_a_cloner) and count($site_liens_a_cloner) and is_int($site_new)) {
			foreach ($site_liens_a_cloner as $lien) {
				$lien['id_projets_site'] = $site_new;
				sql_insertq('spip_projets_sites_liens', $lien);
			}
		}
		/**
		 * On redirige vers la fiche du site cloné.
		 */
		include_spip('inc/headers');
		$redirect = generer_url_entite($site_new, 'spip_projets_sites');
		redirige_par_entete($redirect);
	}
}
