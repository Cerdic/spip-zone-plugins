<?php
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}
$GLOBALS['spip_pipeline']['traitement_paypal'] = '';
define('_URL_SOUMISSION_PAYPAL_prod', 'https://www.paypal.com/cgi-bin/webscr');
define('_URL_SOUMISSION_PAYPAL_test', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
