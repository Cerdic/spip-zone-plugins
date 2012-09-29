<?php
/***************************************************************************\
 *  Association-SPIPAL,  validation de paiement pour  plugins SPIP         *
 *                                                                         *
 *  Copyright (c) 2011 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Fonction prenant en charge la validation d'une notification de paiement Paypal
// Voir les specifications dans
// http://www.spip-contrib.net/Plugin-SPIPAL

// Actuellement on consid�re que tout paiement est un don.

function inc_association_spipal($env)
{
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