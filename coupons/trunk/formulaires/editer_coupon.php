<?php
/**
 * Gestion du formulaire de d'édition de coupon
 *
 * @plugin     Coupons de réduction
 * @copyright  2017
 * @author     Nicolas Dorigny
 * @licence    GNU/GPL
 * @package    SPIP\Coupons\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_coupon
 *     Identifiant du coupon. 'new' pour un nouveau coupon.
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param int        $lier_trad
 *     Identifiant éventuel d'un coupon source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du coupon, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_coupon_identifier_dist(
	$id_coupon = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	return serialize(array(intval($id_coupon)));
}

/**
 * Chargement du formulaire d'édition de coupon
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_coupon
 *     Identifiant du coupon. 'new' pour un nouveau coupon.
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param int        $lier_trad
 *     Identifiant éventuel d'un coupon source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du coupon, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_coupon_charger_dist(
	$id_coupon = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$valeurs = formulaires_editer_objet_charger('coupon', $id_coupon, '', $lier_trad, $retour, $config_fonc, $row,
		$hidden);

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de coupon
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_coupon
 *     Identifiant du coupon. 'new' pour un nouveau coupon.
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param int        $lier_trad
 *     Identifiant éventuel d'un coupon source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du coupon, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_coupon_verifier_dist(
	$id_coupon = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('coupon', $id_coupon, array('titre', 'code', 'montant'));

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de coupon
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_coupon
 *     Identifiant du coupon. 'new' pour un nouveau coupon.
 * @param string     $retour
 *     URL de redirection après le traitement
 * @param int        $lier_trad
 *     Identifiant éventuel d'un coupon source d'une traduction
 * @param string     $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array      $row
 *     Valeurs de la ligne SQL du coupon, si connu
 * @param string     $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 *
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_coupon_traiter_dist(
	$id_coupon = 'new',
	$retour = '',
	$lier_trad = 0,
	$config_fonc = '',
	$row = array(),
	$hidden = ''
) {
	$retours = formulaires_editer_objet_traiter('coupon', $id_coupon, '', $lier_trad, $retour, $config_fonc, $row,
		$hidden);

	return $retours;
}
