<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function browserid_autoriser(){}

function autoriser_browserid_configurer_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}

?>