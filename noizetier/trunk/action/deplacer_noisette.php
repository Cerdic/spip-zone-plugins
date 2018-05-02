<?php
/**
 * Ce fichier contient l'action `deplacer_noisette` lancée par un utilisateur pour
 * déplacer d'un rang vers le haut ou vers le bas de façon sécurisée une noisette donnée.
 *
 * @package SPIP\NOIZETIER\NOISETTE\ACTION
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cette action permet à l'utilisateur de déplacer une noisette d'un rang vers le haut ou
 * vers le bas, de façon sécurisée.
 *
 * Cette action est réservée aux utilisateurs autorisés à modifier la configuration de la page
 * à laquelle est rattachée la noisette. Elle nécessite des arguments dont le sens et l'id de la noisette.
 *
 * @uses noizetier_conteneur_decomposer()
 * @uses noisette_deplacer()
 *
 * @return void
 */
function action_deplacer_noisette_dist() {

	// Les arguments attendus dépendent du contexte et la chaine peut prendre les formes suivantes:
	// - bas:id_noisette:nb_noisettes_du_conteneur, pour déplacer la noisette d'un rang vers le bas.
	// - haut:id_noisette:nb_noisettes_du_conteneur, pour déplacer la noisette d'un rang vers le haut.
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arguments = $securiser_action();

	if ($arguments) {
		// Identification des arguments
		list($sens, $id_noisette, $nb_noisettes) = explode(':', $arguments);

		// Recherche des informations sur la noisette.
		if (in_array($sens, array('bas', 'haut')) and ($id_noisette = intval($id_noisette))) {
			// Récupération du conteneur de la noisette
			$select = array('id_conteneur', 'rang_noisette');
			$where = array('id_noisette=' . $id_noisette);
			$noisette = sql_fetsel($select, 'spip_noisettes', $where);

			// Décomposition de l'id du conteneur en éléments du noiZetier
			include_spip('inc/noizetier_conteneur');
			$conteneur = noizetier_conteneur_decomposer($noisette['id_conteneur']);

			// Test de l'autorisation
			if (!autoriser('configurerpage', 'noizetier', '', 0, $conteneur)) {
				include_spip('inc/minipres');
				echo minipres();
				exit();
			}

			// Détermination du rang de destination de la noisette. Les rangs des noisettes dans un conteneur
			// sont toujours compris entre 1 et le nombre de noisettes du conteneur.
			if ($sens == 'bas') {
				if ($noisette['rang_noisette'] < $nb_noisettes) {
					// La noisette peut être échangée avec la suivante
					$rang_destination = $noisette['rang_noisette'] + 1;
				} else {
					// La noisette passe en début de liste
					$rang_destination = 1;
				}
			} else {
				if ($noisette['rang_noisette'] > 1) {
					// La noisette peut être échangée avec la précédente
					$rang_destination = $noisette['rang_noisette'] - 1;
				} else {
					// La noisette passe en fin de liste
					$rang_destination = $nb_noisettes;
				}
			}

			// Déplacement de la noisette par modification de son rang en base de données.
			include_spip('inc/ncore_noisette');
			noisette_deplacer('noizetier', $id_noisette, $rang_destination);
		}
	}
}
