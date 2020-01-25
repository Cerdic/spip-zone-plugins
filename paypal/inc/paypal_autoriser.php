<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function paypal_autoriser(){}


function autoriser_paypal2_bouton_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}

function autoriser_paypal_configurer_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}

?>