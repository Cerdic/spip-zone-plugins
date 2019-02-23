<?php
/**
 * Préchargement des formulaires d'édition de immeuble
 *
 * @plugin     Immeubles
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Immeubles\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/precharger_objet');

/**
 * Retourne les valeurs à charger pour un formulaire d'édition d'un immeuble
 *
 * Lors d'une création, certains champs peuvent être préremplis
 * (c'est le cas des traductions) 
 *
 * @param string|int $id_immeuble
 *     Identifiant de immeuble, ou "new" pour une création
 * @param int $id_rubrique
 *     Identifiant éventuel de la rubrique parente
 * @param int $lier_trad
 *     Identifiant éventuel de la traduction de référence
 * @return array
 *     Couples clés / valeurs des champs du formulaire à charger.
**/
function inc_precharger_immeuble_dist($id_immeuble, $id_rubrique=0, $lier_trad=0) {
	return precharger_objet('immeuble', $id_immeuble, $id_rubrique, $lier_trad, 'titre');
}

/**
 * Récupère les valeurs d'une traduction de référence pour la création
 * d'un immeuble (préremplissage du formulaire). 
 *
 * @note
 *     Fonction facultative si pas de changement dans les traitements
 * 
 * @param string|int $id_immeuble
 *     Identifiant de immeuble, ou "new" pour une création
 * @param int $id_rubrique
 *     Identifiant éventuel de la rubrique parente
 * @param int $lier_trad
 *     Identifiant éventuel de la traduction de référence
 * @return array
 *     Couples clés / valeurs des champs du formulaire à charger
**/
function inc_precharger_traduction_immeuble_dist($id_immeuble, $id_rubrique=0, $lier_trad=0) {
	return precharger_traduction_objet('immeuble', $id_immeuble, $id_rubrique, $lier_trad, 'titre');
}
