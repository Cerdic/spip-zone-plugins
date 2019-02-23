<?php
/**
 * Préchargement des formulaires d'édition de espace
 *
 * @plugin     Espaces
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Espaces\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
  return;
}

include_spip('inc/precharger_objet');

/**
 * Retourne les valeurs à charger pour un formulaire d'édition d'un espace
 *
 * Lors d'une création, certains champs peuvent être préremplis
 * (c'est le cas des traductions) 
 *
 * @param string|int $id_espace
 *     Identifiant de espace, ou "new" pour une création
 * @param int $id_rubrique
 *     Identifiant éventuel de la rubrique parente
 * @param int $lier_trad
 *     Identifiant éventuel de la traduction de référence
 * @return array
 *     Couples clés / valeurs des champs du formulaire à charger.
**/
function inc_precharger_espace_dist($id_espace, $id_rubrique=0, $lier_trad=0) {
  return precharger_objet('espace', $id_espace, $id_rubrique, $lier_trad, 'titre');
}

/**
 * Récupère les valeurs d'une traduction de référence pour la création
 * d'un espace (préremplissage du formulaire). 
 *
 * @note
 *     Fonction facultative si pas de changement dans les traitements
 * 
 * @param string|int $id_espace
 *     Identifiant de espace, ou "new" pour une création
 * @param int $id_rubrique
 *     Identifiant éventuel de la rubrique parente
 * @param int $lier_trad
 *     Identifiant éventuel de la traduction de référence
 * @return array
 *     Couples clés / valeurs des champs du formulaire à charger
**/
function inc_precharger_traduction_espace_dist($id_espace, $id_rubrique=0, $lier_trad=0) {
  return precharger_traduction_objet('espace', $id_espace, $id_rubrique, $lier_trad, 'titre');
}
