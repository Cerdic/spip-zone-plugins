<?php

/**
 * Fonctions de pré-remplissage des traductions
 *
 * @package SPIP\Dictionnaires\Objets
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/precharger_objet');

/**
 * Retourne les valeurs à charger pour un formulaire d'édition d'une definition
 *
 * Lors d'une création, certains champs peuvent être préremplis
 * (c'est le cas des traductions) 
 *
 * @param string|int $id
 *     Identifiant de la definition, ou "new" pour une création
 * @param int $id_dictionnaire
 *     Identifiant éventuel de la rubrique parente
 * @param int $lier_trad
 *     Identifiant éventuel de la traduction de référence
 * @return array
 *     Couples clés / valeurs des champs du formulaire à charger.
**/
function inc_precharger_definition_dist($id, $id_dictionnaire=0, $lier_trad=0) {
	return precharger_objet('definition', $id, $id_dictionnaire, $lier_trad, 'titre');
}

/**
 * Récupère les valeurs d'une traduction de référence pour la création
 * d'une definition (préremplissage du formulaire). 
 *
 * @note Fonction facultative si pas de changement dans les traitements
 * 
 * @param string|int $id
 *     Identifiant de la definition, ou "new" pour une création
 * @param int $id_dictionnaire
 *     Identifiant éventuel de la rubrique parente
 * @param int $lier_trad
 *     Identifiant éventuel de la traduction de référence
 * @return array
 *     Couples clés / valeurs des champs du formulaire à charger
**/
function inc_precharger_traduction_definition_dist($id, $id_dictionnaire=0, $lier_trad=0) {
	return precharger_traduction_objet('definition', $id, $id_dictionnaire, $lier_trad, 'titre');
}

?>