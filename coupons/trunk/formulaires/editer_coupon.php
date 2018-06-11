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
 *
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_coupon_identifier_dist($id_coupon = 'new', $retour = '') {
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
 *
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_coupon_charger_dist($id_coupon = 'new', $retour = '') {
	$valeurs = formulaires_editer_objet_charger('coupon', $id_coupon, '', 0, $retour, '');

	if (!intval($id_coupon)) {
		$valeurs['actif']         = 'on';
		$valeurs['date_validite'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' + ' . lire_config('coupons/duree_validite') . ' days'));
	}
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
 *
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_coupon_verifier_dist($id_coupon = 'new', $retour = '') {

	$erreurs = formulaires_editer_objet_verifier('coupon', $id_coupon, array('titre', 'montant'));

	$verifier = charger_fonction('verifier', 'inc');
	foreach (array('date_validite') AS $champ) {
		$normaliser = null;
		if ($erreur = $verifier(_request($champ), 'date', array('normaliser' => 'datetime'), $normaliser)) {
			$erreurs[$champ] = $erreur;
			// si une valeur de normalisation a ete transmis, la prendre.
		} elseif (!is_null($normaliser)) {
			set_request($champ, $normaliser);
			// si pas de normalisation ET pas de date soumise, il ne faut pas tenter d'enregistrer ''
		} else {
			set_request($champ, null);
		}
	}
	if (intval($id_coupon)){
		if (_request('code') && sql_getfetsel('id_coupon', 'spip_coupons', 'id_coupon <> '.intval($id_coupon).' and code = ' . sql_quote(_request('code')))) {
			$erreurs['code'] = _T('coupon:erreur_code_deja_utilise');
		} 		
	} else {
		if (_request('code') && sql_getfetsel('id_coupon', 'spip_coupons', 'code = ' . sql_quote(_request('code')))) {
			$erreurs['code'] = _T('coupon:erreur_code_deja_utilise');
		}
	}

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
 *
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_coupon_traiter_dist($id_coupon = 'new', $retour = '') {

	if (!_request('code')) {
		$code = coupon_generer_code();
		set_request('code',$code);
	}

	$retours = formulaires_editer_objet_traiter('coupon', $id_coupon, '', 0, $retour, '');
	
	return $retours;
}
