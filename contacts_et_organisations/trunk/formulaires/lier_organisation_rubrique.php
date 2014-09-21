<?php

/**
 * Gestion du formulaire de liaison d'organisations sur
 * une rubrique
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Formulaires
**/


if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Chargement du formulaire de liaison d'organisations sur une rubrique
 *
 * @param int $id_rubrique
 *     Identifiant de la rubrique
 * @param string $redirect
 *     URL de redirection après le traitement
 * @return array
 *     Environnement du formulaire
**/
function formulaires_lier_organisation_rubrique_charger_dist($id_rubrique, $redirect=''){
	$valeurs = array(
		'recherche_organisation' => '',
		'id_rubrique' => intval($id_rubrique),
		'redirect' => $redirect
	);
	return $valeurs;
}

/**
 * Vérifications du formulaire de liaison d'organisations sur une rubrique
 *
 * @note
 *     Retourne toujours une erreur : ce sont des boutons d'actions qui
 *     lient les organisations, et non le traitement de ce formulaire
 *
 * @param int $id_rubrique
 *     Identifiant de la rubrique
 * @param string $redirect
 *     URL de redirection après le traitement
 * @return array
 *     Tableau des erreurs
**/
function formulaires_lier_organisation_rubrique_verifier_dist($id_rubrique, $redirect=''){
	$erreurs = array();
	$erreurs[''] = ''; // toujours en erreur : ce sont des actions qui lient les contacts
	return $erreurs;
}

/**
 * Traitement du formulaire de liaison d'organisations sur une rubrique
 *
 * @note
 *     Cette fonction est inutilisée. Les traitements sont fait par
 *     des boutons d'actions
 *
 * @param int $id_rubrique
 *     Identifiant de la rubrique
 * @param string $redirect
 *     URL de redirection après le traitement
 * @return array
 *     Retours des traitements
**/
function formulaires_lier_organisation_rubrique_traiter_dist($id_rubrique, $redirect=''){
	return array(
		'message_ok' => '',
		'editable' => true,
	);
}

?>
