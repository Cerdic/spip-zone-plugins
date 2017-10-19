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
 * Pour l'instant basé sur le fait que le coupon est lié à une commande.
 * Pourrait évoluer à terme (coupon utilisable plusieurs fois par exemple)
 *
 * @param $id_coupon
 *
 * @return bool
 */
function coupon_utilisable($id_coupon) {
	$id_commande = sql_getfetsel('id_commande', 'spip_coupons', 'id_coupon = ' . $id_coupon);

	return ($id_commande ? false : true);
}

function coupon_generer_code() {
	// un code aléatoire
	$chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
	return substr(str_shuffle($chars),0,10);
}
