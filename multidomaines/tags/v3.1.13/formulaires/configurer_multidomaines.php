<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Vérification des valeurs postées
 *
 * Normaliser les données avant le traiter : enlever les secteurs qui n'ont pas été configurés
 * afin de ne pas avoir des clés vides dans la config
 *
 * @return array
 */
function formulaires_configurer_multidomaines_verifier_dist() {

	$erreurs = array();

	if ($secteurs = sql_allfetsel('id_rubrique', 'spip_rubriques', 'id_parent=0')) {
		foreach($secteurs as $secteur) {
			$id_rubrique = $secteur['id_rubrique'];
			$valeur = _request($id_rubrique);
			if (
				is_array($valeur)
				and !array_filter($valeur)
			) {
				set_request($id_rubrique, null);
			}
		}
	}

	return $erreurs;
}
