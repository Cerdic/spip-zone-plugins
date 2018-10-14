<?php
/*
 * Gestion du formulaire d'édition des rôles d'une sélection éditoriale
 *
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Chargement des valeurs
 *
 * @param string|int $id_selection
 * @param string $objet
 * @param string|int $id_objet
 *
 * @return array
 */
function formulaires_editer_roles_selection_charger_dist($id_selection, $objet, $id_objet) {

	// Récupèrer les valeurs de editer_liens
	$charger_editer_liens = charger_fonction('charger', 'formulaires/editer_liens');
	$valeurs = $charger_editer_liens('selection', $objet, $id_objet, array('id_selection' => $id_selection));

	return $valeurs;
}

/**
 * Traiter le post des informations d'édition de liens
 *
 * @param string|int $id_selection
 * @param string $objet
 * @param string|int $id_objet
 * @param array $options
 *
 * @return array
 */
function formulaires_editer_roles_selection_traiter_dist($id_selection, $objet, $id_objet) {

	// Récupérer les traitements de editer_liens
	$traiter_editer_liens = charger_fonction('traiter', 'formulaires/editer_liens');
	$retours = $traiter_editer_liens('selection', $objet, $id_objet, array('id_selection' => $id_selection));

	return $retours;
}