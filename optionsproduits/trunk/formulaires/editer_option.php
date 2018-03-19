<?php
/**
 * Gestion du formulaire de d'édition de option
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
 * @param int|string $id_option
 *     Identifiant du option. 'new' pour un nouveau option.
 * @param int        $id_optionsgroupe
 *     Identifiant de l'objet parent (si connu)
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param string     $associer_objet
 *     Éventuel `objet|x` indiquant de lier le option créé à cet objet,
 *     tel que `article|3`
 * @param int        $lier_trad
 *     Identifiant éventuel d'un option source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du option, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_option_identifier_dist(
	$id_option = 'new',
	$id_optionsgroupe = 0,
	$retour = '',
	$associer_objet = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	return serialize(array(intval($id_option), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de option
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_option
 *     Identifiant du option. 'new' pour un nouveau option.
 * @param int        $id_optionsgroupe
 *     Identifiant de l'objet parent (si connu)
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param string     $associer_objet
 *     Éventuel `objet|x` indiquant de lier le option créé à cet objet,
 *     tel que `article|3`
 * @param int        $lier_trad
 *     Identifiant éventuel d'un option source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du option, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_option_charger_dist(
	$id_option = 'new',
	$id_optionsgroupe = 0,
	$retour = '',
	$associer_objet = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$valeurs = formulaires_editer_objet_charger('option', $id_option, $id_optionsgroupe, $lier_trad, $retour, $config_fonc, $row, $hidden);
	if (!$valeurs['id_optionsgroupe']) {
		$valeurs['id_optionsgroupe'] = $id_optionsgroupe;
	}

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de option
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_option
 *     Identifiant du option. 'new' pour un nouveau option.
 * @param int        $id_optionsgroupe
 *     Identifiant de l'objet parent (si connu)
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param string     $associer_objet
 *     Éventuel `objet|x` indiquant de lier le option créé à cet objet,
 *     tel que `article|3`
 * @param int        $lier_trad
 *     Identifiant éventuel d'un option source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du option, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_option_verifier_dist(
	$id_option = 'new',
	$id_optionsgroupe = 0,
	$retour = '',
	$associer_objet = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('option', $id_option, array('id_optionsgroupe', 'titre', 'id_optionsgroupe'));
	
	if(_request('prix_defaut') && !is_numeric(_request('prix_defaut'))){
		$erreurs['prix_defaut'] = 'Saisissez un nombre décimal avec un point';
	}

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de option
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_option
 *     Identifiant du option. 'new' pour un nouveau option.
 * @param int        $id_optionsgroupe
 *     Identifiant de l'objet parent (si connu)
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param string     $associer_objet
 *     Éventuel `objet|x` indiquant de lier le option créé à cet objet,
 *     tel que `article|3`
 * @param int        $lier_trad
 *     Identifiant éventuel d'un option source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du option, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_option_traiter_dist(
	$id_option = 'new',
	$id_optionsgroupe = 0,
	$retour = '',
	$associer_objet = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$retours = formulaires_editer_objet_traiter('option', $id_option, $id_optionsgroupe, $lier_trad, $retour, $config_fonc, $row, $hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet and $id_option = $retours['id_option']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');

			objet_associer(array('option' => $id_option), array($objet => $id_objet));

			if (isset($retours['redirect'])) {
				$retours['redirect'] = parametre_url($retours['redirect'], 'id_lien_ajoute', $id_option, '&');
			}
		}
	}

	return $retours;
}
