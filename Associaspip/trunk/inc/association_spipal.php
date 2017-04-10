<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Fonction prenant en charge la validation d'une notification de paiement Paypal
 *
 * @note
 *   Voir les specifications dans https://contrib.spip.net/Plugin-SPIPAL
 *   Actuellement on considère que tout paiement est un don.
 */
function inc_association_spipal($env) {
  	$custom = @unserialize($env['custom']);
	$id = abs(intval($custom['id_auteur']));
	$montant = intval($env['payment_fee']?$env['payment_fee']:$env['mc_fee']);
	$where = "id_auteur=$id AND argent=$montant";

	// Prendre le premier don a valider de cette personne et de ce montant
	// (il peut y en ait plusieurs en cas de multiples clics)
	$id_don = sql_getfetsel('id_don', 'spip_asso_dons AS D LEFT JOIN spip_asso_comptes AS C ON D.id_don=C.id_journal', "$where AND vu=0", '', 'date_don ASC', '1');

	if (!$id_don)
		spip_log("pas de don pour : $where",'associaspip');
	else {
		sql_updateq('spip_asso_comptes', array('vu' => 1), "id_journal=$id_don");
		spip_log("validation Paypal de don$id_don : $where",'associaspip');
	}
}

?>