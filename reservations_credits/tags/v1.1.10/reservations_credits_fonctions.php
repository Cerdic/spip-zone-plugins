<?php
/**
 * Fonctions utiles au plugin Réseŕvations Crédits
 *
 * @plugin     Réseŕvations Crédits
 * @copyright  2015-20
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservations_credits\Fonctions
 */
if (! defined ( '_ECRIRE_INC_VERSION' ))
	return;

/**
 * Calcule les crédits du client
 *
 * @param array $credit
 *        	un table devis => credit
 * @param string $email
 *        	L'email du client.
 * @param string $devise
 *        	Le code de la devise.
 *
 * @return mixed La valeur du champ ou un tableau avec tous les champs.
 */
function credit_client($credit = '', $email = '', $devise = '') {

	if ($credit) {
		$credit = unserialize ($credit);
	}
	elseif ($email) {
		$credit = unserialize (sql_getfetsel ('credit', 'spip_reservation_credits', 'email LIKE "%' . $email . '%"'));
	}
	else {
		return;
	}

	if ($devise AND is_array($credit) AND isset($credit[$devise])) {
		$credit = $credit [$devise];
	}

	return $credit;
}