<?php
/**
 * Ce fichier contient l'action `dupliquer_noisette` lancée par un utilisateur pour
 * copier une noisette donnée de façon sécurisée au rang suivant dans le même conteneur.
 *
 * @package SPIP\NOIZETIER\NOISETTE\ACTION
 */
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Cette action permet à l'utilisateur de copier, de façon sécurisée,
 * une noisette donnée et sa descendance si celle-ci est un conteneur au rang suivant dans le même conteneur.
 *
 * Cette action est réservée aux utilisateurs autorisés.
 * Elle nécessite l'id de la noisette comme argument unique.
 *
 * @uses noizetier_conteneur_decomposer()
 * @uses noisette_supprimer()
 *
 * @return void
 */
function action_dupliquer_noisette_dist() {

	// Sécurisation et autorisation.
	// L'id de la noisette est le seul argument attendu.
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$argument = $securiser_action();

	if ($id_noisette = intval($argument)) {
		// Récupération des informations sa noisette source
		include_spip('inc/ncore_noisette');
		$noisette = noisette_lire('noizetier', $id_noisette);

		// Décomposition de l'id du conteneur en éléments du noiZetier
		include_spip('inc/noizetier_conteneur');
		$conteneur = noizetier_conteneur_decomposer($noisette['id_conteneur']);

		// Vérification de l'autorisation associée à l'action.
		if (!autoriser('configurerpage', 'noizetier', '', 0, $conteneur)) {
			include_spip('inc/minipres');
			echo minipres();
			exit();
		}

		// Duplication de la noisette au rang suivant dans le même conteneur en copiant tous les paramètres de la
		// noisette source.
		noisette_dupliquer(
			'noizetier',
			$id_noisette,
			$noisette['id_conteneur'],
			$noisette['rang_noisette'] + 1,
			array('parametres', 'encapsulation', 'css')
		);

		// On invalide le cache
		include_spip('inc/invalideur');
		$invalideur = "id='noisette/${id_noisette}'";
		suivre_invalideur($invalideur);
	}
}
