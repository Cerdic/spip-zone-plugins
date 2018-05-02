<?php
/**
 * Ce fichier contient l'action `supprimer_noisette` lancée par un utilisateur pour
 * supprimer de façon sécurisée une noisette donnée.
 *
 * @package SPIP\NOIZETIER\NOISETTE\ACTION
 */
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Cette action permet à l'utilisateur de supprimer de sa base de données, de façon sécurisée,
 * une noisette donnée et sa descendance si celle-ci est un conteneur.
 *
 * Cette action est réservée aux utilisateurs autorisés.
 * Elle nécessite l'id de la noisette comme argument unique.
 *
 * @uses noizetier_conteneur_decomposer()
 * @uses noisette_supprimer()
 *
 * @return void
 */
function action_supprimer_noisette_dist() {

	// Sécurisation et autorisation.
	// L'id de la noisette est le seul argument attendu.
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$argument = $securiser_action();

	if ($id_noisette = intval($argument)) {
		// Récupération du conteneur de la noisette
		$select = 'id_conteneur';
		$where = array('plugin=' . sql_quote('noizetier'), 'id_noisette=' . $id_noisette);
		$id_conteneur = sql_getfetsel($select, 'spip_noisettes', $where);

		// Décomposition de l'id du conteneur en éléments du noiZetier
		include_spip('inc/noizetier_conteneur');
		$conteneur = noizetier_conteneur_decomposer($id_conteneur);

		// Vérification de l'autorisation associée à l'action.
		if (!autoriser('configurerpage', 'noizetier', '', 0, $conteneur)) {
			include_spip('inc/minipres');
			echo minipres();
			exit();
		}

		// Suppression de la noisette concernée : si la noisette est un conteneur la fonction d'API supprime
		// au préalable les noisettes éventuellement incluses.
		include_spip('inc/ncore_noisette');
		noisette_supprimer('noizetier', $id_noisette);

		// On invalide le cache
		include_spip('inc/invalideur');
		$invalideur = "id='noisette/${id_noisette}'";
		suivre_invalideur($invalideur);
	}
}
