<?php
/**
 * Scripts liés à l'administration des base de donées pour Réservation Événements.
 *
 * @plugin     Réservation Événements
 * @copyright  2013 -
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
  return;

/**
 * Actualisation du champs donnees_auteur pour l'harmoniser avc la nouvelle manière d'encodage.
 *
 * @return void
 */
function update_donnees_auteurs() {

	//les champs extras auteur
	include_spip('cextras_pipelines');

	//les remplacements
	if (function_exists('champs_extras_objet')) {
		$label_nom = array();
		$champs_extras_auteurs = champs_extras_objet(table_objet_sql('auteur'));

		foreach ($champs_extras_auteurs as $value) {
			$label_nom[$value['options']['label']] = $value['options']['nom'];
		}

	}
	// Rechercher les reservations avec des champs donnes_auteurs et remplace les index si nécessaire
	$sql = sql_select('id_reservation,donnees_auteur', 'spip_reservations', 'id_auteur = 0');
	while ($data = sql_fetch($sql)) {

		$donnees_auteur = unserialize($data['donnees_auteur']);
		$update = FALSE;

		foreach ($label_nom as $label => $nom) {
			if (isset($donnees_auteur[$label])) {
				$donnees_auteur[$nom] = $donnees_auteur[$label];
				unset($donnees_auteur[$label]);
				$update = TRUE;
			}
		}

		if ($update) {
			sql_updateq('spip_reservations', array('donnees_auteur' => serialize($donnees_auteur)), 'id_reservation=' . $data['id_reservation']);
		}

	}

	return;
}
