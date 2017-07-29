<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cette action permet à l'utilisateur de déplacer une noisette d'un rang vers le haut ou
 * vers le bas, de façon sécurisée.
 *
 * Cette action est réservée aux utilisateurs autorisés à modifier la configuration de la page
 * à laquelle est rattachée la noisette. Elle nécessite des arguments dont la liste dépend
 * du contexte.
 *
 * @uses supprimer_noisettes()
 *
 * @return void
 */
function action_deplacer_noisette_dist() {

	// Les arguments attendus dépendent du contexte et la chaine peut prendre les formes suivantes:
	// - bas:id_noisette, pour déplacer la noisette d'un rang vers le bas.
	// - haut:id_noisette, pour déplacer la noisette d'un rang vers le haut.
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arguments = $securiser_action();

	if ($arguments) {
		// Identification des arguments
		list($sens, $id_noisette) = explode(':', $arguments);

		// Recherche des informations sur la noisette.
		if (in_array($sens, array('bas', 'haut')) and ($id_noisette = intval($id_noisette))) {
			$select = array('type', 'composition', 'objet', 'id_objet', 'bloc', 'rang');
			$where = array('id_noisette=' . $id_noisette);
			$noisette = sql_fetsel($select, 'spip_noizetier', $where);
			$options = array();
			if ($noisette['type']) {
				$options['page'] = $noisette['composition']
					? $noisette['type'] . '-' . $noisette['composition']
					: $noisette['type'];
			} else {
				$options['objet'] = $noisette['objet'];
				$options['id_objet'] = $noisette['id_objet'];
			}

			// Test de l'autorisation
			if (!autoriser('configurerpage', 'noizetier', '', 0, $options)) {
				include_spip('inc/minipres');
				echo minipres();
				exit();
			}

			// Déplacement de la noisette par modification de son rang en base de données.
			include_spip('noizetier_fonctions');
			noizetier_noisette_deplacer($id_noisette, $sens, $noisette);
		}
	}
}
