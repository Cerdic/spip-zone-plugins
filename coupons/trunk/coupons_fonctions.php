<?php
/**
 * Fonctions utiles au plugin Coupons de réduction
 *
 * @plugin     Coupons de réduction
 * @copyright  2017
 * @author     Nicolas Dorigny
 * @licence    GNU/GPL
 * @package    SPIP\Coupons\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Une fonction qui indique si un coupon est utilisable ou pas (i.e. pas encore utilisé)
 *
 * @deprecated utiliser le champ "actif" pour déterminer si un coupon est valide
 *            
 * @param $id_coupon
 *
 * @return bool
 */
function coupon_utilisable($id_coupon) {
	$id_coupon = sql_getfetsel('id_coupon', 'spip_coupons', 'id_coupon = '.$id_coupon.' and actif = '.sql_quote('on'));
	return ($id_coupon ? false : true);
}

/**
 * Génère un code coupon aléatoire
 * On évite les I et O et 1 et 0 qui se ressemblent trop
 *
 * @return string
 */
function coupon_generer_code() {

	$chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
	return substr(str_shuffle($chars),0,10);
}
