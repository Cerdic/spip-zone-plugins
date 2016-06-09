<?php

/**
 * Fonctions de pré-remplissage des traductions
 *
 * @package SPIP\Traddoc\Objets
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/precharger_objet');

/**
 * Retourne les valeurs à charger pour un formulaire d'édition d'un document
 *
 * Lors d'une création, certains champs peuvent être préremplis
 * (c'est le cas des traductions)
 *
 * @param string|int $id
 *     Identifiant du document, ou "new" pour une création
 * @param int $id_document
 *     Identifiant éventuel de la document parente
 * @param int $lier_trad
 *     Identifiant éventuel de la traduction de référence
 * @return array
 *     Couples clés / valeurs des champs du formulaire à charger.
**/
function inc_precharger_document_dist($id, $id_document = 0, $lier_trad = 0) {
	return precharger_objet('document', $id, $id_document, $lier_trad, 'titre');
}

/**
 * Récupère les valeurs d'une traduction de référence pour la création
 * d'un document (préremplissage du formulaire).
 *
 * @note Fonction facultative si pas de changement dans les traitements
 *
 * @param string|int $id
 *     Identifiant du document, ou "new" pour une création
 * @param int $id_document
 *     Identifiant éventuel de la document parente
 * @param int $lier_trad
 *     Identifiant éventuel de la traduction de référence
 * @return array
 *     Couples clés / valeurs des champs du formulaire à charger
**/
function inc_precharger_traduction_document_dist($id, $id_document = 0, $lier_trad = 0) {
	return precharger_traduction_objet('document', $id, $id_document, $lier_trad, 'titre');
}
