<?php
/**
 * Gestion du formulaire de d'édition de optionsgroupe
 *
 * @plugin     Optionsproduits
 * @copyright  2018
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Optionsproduits\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_optionsgroupe
 *     Identifiant du optionsgroupe. 'new' pour un nouveau optionsgroupe.
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param int        $lier_trad
 *     Identifiant éventuel d'un optionsgroupe source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du optionsgroupe, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_optionsgroupe_identifier_dist(
	$id_optionsgroupe = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	return serialize(array(intval($id_optionsgroupe)));
}

/**
 * Chargement du formulaire d'édition de optionsgroupe
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_optionsgroupe
 *     Identifiant du optionsgroupe. 'new' pour un nouveau optionsgroupe.
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param int        $lier_trad
 *     Identifiant éventuel d'un optionsgroupe source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du optionsgroupe, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_optionsgroupe_charger_dist(
	$id_optionsgroupe = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$valeurs = formulaires_editer_objet_charger('optionsgroupe', $id_optionsgroupe, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de optionsgroupe
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_optionsgroupe
 *     Identifiant du optionsgroupe. 'new' pour un nouveau optionsgroupe.
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param int        $lier_trad
 *     Identifiant éventuel d'un optionsgroupe source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du optionsgroupe, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_optionsgroupe_verifier_dist(
	$id_optionsgroupe = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('optionsgroupe', $id_optionsgroupe, array('titre_groupe'));

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de optionsgroupe
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_optionsgroupe
 *     Identifiant du optionsgroupe. 'new' pour un nouveau optionsgroupe.
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param int        $lier_trad
 *     Identifiant éventuel d'un optionsgroupe source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du optionsgroupe, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_optionsgroupe_traiter_dist(
	$id_optionsgroupe = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$retours = formulaires_editer_objet_traiter('optionsgroupe', $id_optionsgroupe, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

	return $retours;
}
