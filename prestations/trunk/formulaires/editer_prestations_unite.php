<?php
/**
 * Gestion du formulaire de d'édition de prestations_unite
 *
 * @plugin     Prestations
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Prestations\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_prestations_unite
 *     Identifiant du prestations_unite. 'new' pour un nouveau prestations_unite.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un prestations_unite source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du prestations_unite, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_prestations_unite_identifier_dist($id_prestations_unite = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_prestations_unite)));
}

/**
 * Chargement du formulaire d'édition de prestations_unite
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_prestations_unite
 *     Identifiant du prestations_unite. 'new' pour un nouveau prestations_unite.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un prestations_unite source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du prestations_unite, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_prestations_unite_charger_dist($id_prestations_unite = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$valeurs = formulaires_editer_objet_charger('prestations_unite', $id_prestations_unite, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de prestations_unite
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_prestations_unite
 *     Identifiant du prestations_unite. 'new' pour un nouveau prestations_unite.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un prestations_unite source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du prestations_unite, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_prestations_unite_verifier_dist($id_prestations_unite = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('prestations_unite', $id_prestations_unite, array('titre'));

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de prestations_unite
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_prestations_unite
 *     Identifiant du prestations_unite. 'new' pour un nouveau prestations_unite.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un prestations_unite source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du prestations_unite, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_prestations_unite_traiter_dist($id_prestations_unite = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$retours = formulaires_editer_objet_traiter('prestations_unite', $id_prestations_unite, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	// Virer le id_prestations_unite du redirect
	if (!empty($retours['redirect'])) {
		$retours['redirect'] = parametre_url($retours['redirect'], 'id_prestations_unite', '');
	}
	return $retours;
}
