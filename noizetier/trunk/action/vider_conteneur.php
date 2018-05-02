<?php
/**
 * Ce fichier contient l'action `vider_conteneur` lancée par un utilisateur pour
 * supprimer de façon sécurisée les noisettes d'un conteneur quel qu'il soit (bloc ou noisette).
 *
 * @package SPIP\NOIZETIER\CONTENEUR\ACTION
 */
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Cette action permet à l'utilisateur de supprimer de sa base de données, de façon sécurisée,
 * toutes les noisettes d'un conteneur qu'il soit un bloc ou une noisette.
 * Si le conteneur contient une noisette conteneur, sa descendance sera aussi supprimée.
 *
 * Cette action est réservée aux utilisateurs autorisés.
 * Elle nécessite l'id du conteneur comme argument unique.
 *
 * @uses noizetier_conteneur_decomposer()
 * @uses conteneur_vider()
 *
 * @return void
 */
function action_vider_conteneur_dist() {

	// Sécurisation et autorisation.
	// L'id du conteneur est le seul argument attendu.
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_conteneur = $securiser_action();

	if ($id_conteneur) {
		// Décomposition de l'id du conteneur en éléments du noiZetier
		include_spip('inc/noizetier_conteneur');
		$conteneur = noizetier_conteneur_decomposer($id_conteneur);

		// Vérification de l'autorisation associée à l'action.
		if (!autoriser('configurerpage', 'noizetier', '', 0, $conteneur)) {
			include_spip('inc/minipres');
			echo minipres();
			exit();
		}

		// Suppression du conteneur : si celui-ci contient des noisettes conteneur la fonction d'API vide
		// au préalable ces conteneurs noisette éventuellement inclus.
		include_spip('inc/ncore_conteneur');
		conteneur_vider('noizetier', $id_conteneur);

		// On invalide le cache de la page ou de l'objet dans lequel est inclus le conteneur.
		$invalideur = (!empty($conteneur['objet']) and !empty($conteneur['id_objet']))
			? "id='{$conteneur['objet']}/{$conteneur['id_objet']}'"
			: "id='page/{$conteneur['page']}'";
		include_spip('inc/invalideur');
		suivre_invalideur($invalideur);
	}
}
