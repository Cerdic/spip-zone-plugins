<?php
/**
 * Fonctions utiles au plugin Réseŕvations Crédits
 *
 * @plugin     Réseŕvations Crédits
 * @copyright  2015
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Reservations_credits\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Calcule les crédits du client
 *
 * @param  string $email L'email du client.
 *
 * @return mixed La valeur du champ ou un tableau avec tous les champs.
 */
function credit_client($credit='',$email='', $devise = '') {
	if ($credit) {
		$credit = unserialize($credit);
	}
	elseif($email) {
		$credit = unserialize(sql_getfetsel('credit', 'spip_reservation_credits', 'email LIKE "%' . $email . '%"'));
	}
	else {
		return;
	}
	if ($devise AND isset($credit[$devise])) {
		$credit = $credit[$devise];
	}
	return $credit;
}

?>