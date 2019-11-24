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
function action_objet_modifier_archivage_dist($arguments = null){

	// Récupération des arguments de façon sécurisée.
	if (is_null($arguments)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arguments = $securiser_action();
	}

	// Extraction des arguments objet et id objet
	list($action, $objet, $id, $raison) = explode(':', $arguments);

	if ($id_objet = intval($id)) {
		// Verification des autorisations
		if (!autoriser('modifierarchivage', $objet, $id_objet, '', array('action' =>$action))) {
			include_spip('inc/minipres');
			echo minipres();
			exit();
		}

		// Déterminer les mise à jour sur l'état d'archivage de l'objet.
		$set = array();
		if ($action == 'archiver') {
			// Demande d'archivage
			$set = array(
				'est_archive'    => 1,
				'date_archive'   => date('Y-m-d H:i:s'),
				'raison_archive' => ''
			);
		} elseif ($action == 'desarchiver') {
			// Demande de désarchivage
			include_spip('inc/config');
			$consignation = lire_config('archobjet/consigner_desarchivage', 0);
			$set = array(
				'est_archive'    => 0,
				'date_archive'   => $consignation ? date('Y-m-d H:i:s') : '',
				'raison_archive' => ''
			);
		} elseif ($action == 'modifier_raison') {
			// Demande de modification de la raison d'archivage
			$set = array(
				'raison_archive' => $raison
			);
		}

		// Mettre à jour les données d'archive de l'objet concerné.
		if ($set) {
			include_spip('action/editer_objet');
			objet_modifier(
				$objet,
				$id_objet,
				$set
			);
		}
	}
}
