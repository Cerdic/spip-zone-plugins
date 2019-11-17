<?php
/**
 * Ce fichier contient l'action `objet_modifier_archivage` utilisée par un adminitrateur pour
 * archiver ou retirer des archives un contenu réputé archivable.
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
};

/**
 * Cette action permet d'archiver un contenu réputé archivable.
 *
 * Cette action est réservée aux administrateurs. Elle nécessite deux arguments, le type et l'id de l'objet.
 *
 * @return void
 */
function action_objet_modifier_archivage_dist(){

	// Securisation et autorisation car c'est une action auteur:
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arguments = $securiser_action();

	// Extraction des arguments objet et id objet
	list($action, $objet, $id) = explode(':', $arguments);

	if ($id_objet = intval($id)) {
		// Verification des autorisations
		if (!autoriser('objetmodifierarchivage', $objet, $id_objet, '', array('action' =>$action))) {
			include_spip('inc/minipres');
			echo minipres();
			exit();
		}

		// Déterminer les mise à jour sur l'état d'archivage de l'objet.
		if ($action == 'archiver') {
			// Demande d'archivage
			$etat_archivage = 1;
			$date = date('Y-m-d H:i:s');
		} else {
			// Demande de désarchivage
			include_spip('inc/config');
			$consignation = lire_config('archobjet/consigner_desarchivage', 0);
			$etat_archivage = 0;
			$date = $consignation ? date('Y-m-d H:i:s') : '';
		}

		// Mettre en archive l'objet concerné
		// -- on supprime toujours la raison ce qui permettra l'affichage de la valeur par défaut
		include_spip('action/editer_objet');
		objet_modifier(
			$objet,
			$id_objet,
			array(
				'est_archive'    => $etat_archivage,
				'date_archive'   => $date,
				'raison_archive' => ''
			)
		);
	}
}
