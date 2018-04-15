<?php
/**
 * Ce fichier contient l'action `supprimer_noisette` lancée par un utilisateur pour
 * supprimer de façon sécurisée une noisette donnée.
 *
 * @package SPIP\NOIZETIER\ACTION
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet à l'utilisateur de supprimer de sa base de données, de façon sécurisée,
 * une noisette donnée.
 * Cette action est réservée aux webmestres. Elle nécessite l'id de la noisette comme argument unique.
 *
 * @uses noisette_supprimer()
 *
 * @return void
 */
function action_supprimer_noisette_dist() {

	// Securisation et autorisation.
	// L'id de la noisette est le seul argument attendu.
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$argument = $securiser_action();

	// -- récupération de la page ou de l'objet
	if ($id_noisette = intval($argument)) {
		// Dans le cas d'une suppression d'une noisette, les données de la page ne sont
		// pas fournies en argument, il faut les lire en base de données.
		$select = array('type', 'composition', 'objet', 'id_objet');
		$where = array('id_noisette=' . $id_noisette);
		$noisette = sql_fetsel($select, 'spip_noisettes', $where);

		// Verification des autorisations
		$options = array();
		if ($noisette['type']) {
			$options['page'] = $noisette['composition']
				? $noisette['type'] . '-' . $noisette['composition']
				: $noisette['type'];
		} else {
			$options['objet'] = $noisette['objet'];
			$options['id_objet'] = $noisette['id_objet'];
		}
		if (!autoriser('configurerpage', 'noizetier', '', 0, $options)) {
			include_spip('inc/minipres');
			echo minipres();
			exit();
		}

		// Suppression des noisettes concernées. On vérifie la sécurité des id numériques.
		include_spip('inc/ncore_noisette');
		noisette_supprimer('noizetier', $id_noisette);

		// On invalide le cache
		include_spip('inc/invalideur');
		$invalideur = "id='noisette/${id_noisette}'";
		suivre_invalideur($invalideur);
	}
}
