<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Minima requis pour le champs password, penser a gerer le passé
//define('_PASS_MIN','8');

define('_CHAMPS_EXTRAS_SAISIES_EXTERNES', true);

/**
 * Déclaration des pipelines introduits par le plugin inscription3
 */

/**
 * Sélectionne les champs qui ne doivent pas être créés dans la tables spip_auteurs
 * Notamment l'ensemble de la table spip_auteurs d'origine, mais aussi certains autres
 * qui ne doivent pas être des champs dans la base, mais juste rester dans les métas
 */
$GLOBALS['spip_pipeline']['i3_exceptions_des_champs_auteurs_elargis'] = '';

/**
 * Sélectionne les champs qui ne doivent pas être chargés dans le formulaire
 * Garde les champs de spip_auteurs et ne prends pas en compte les autres
 */
$GLOBALS['spip_pipeline']['i3_exceptions_chargement_champs_auteurs_elargis'] = '';

/**
 * Décrit les champs spécifiques
 */
$GLOBALS['spip_pipeline']['i3_definition_champs'] = '';

$GLOBALS['spip_pipeline']['i3_verifications_specifiques'] = '';

/**
 * Chargement / vérification et traitement des champs dans les formulaires dans
 * lesquels inscription3 s'insère :
 * -* inscription
 * -* editer_auteur
 */
$GLOBALS['spip_pipeline']['i3_charger_formulaire'] = '';
$GLOBALS['spip_pipeline']['i3_verifier_formulaire'] = '';
$GLOBALS['spip_pipeline']['i3_traiter_formulaire'] = '';
$GLOBALS['spip_pipeline']['i3_confirmation'] = '';
$GLOBALS['spip_pipeline']['i3_cfg_form'] = '';

/**
 * Insertion au début et en fin de formulaire dans lequel inscription3 intervient :
 * - inscription
 * - editer_auteur
 */
$GLOBALS['spip_pipeline']['i3_form_debut'] = '';
$GLOBALS['spip_pipeline']['i3_form_fin'] = '';

/**
 * Insertion de méthodes de validation pour les crayons
 */
$GLOBALS['spip_pipeline']['i3_validation_methods'] = 'i3_validation_methods';

/**
 * Ajout du statut 8aconfirmer dans la liste des statuts possibles de SPIP
 * Des exemples dans i3_validation_methods.js.html
 * cf : crayons_validation.js.html
 */
$GLOBALS['liste_des_statuts']['inscription3:info_aconfirmer'] = "8aconfirmer";

function envoyer_inscription($desc, $nom, $mode, $id) {
	return false;
}
?>