<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function paypal_ieconfig_metas($table){
	$table['paypal']['titre'] = _T('paypal:configuration_paypal');
	$table['paypal']['icone'] = 'images/paypal-24.png';
	$table['paypal']['metas_serialize'] = 'paypal';
	return $table;
}

