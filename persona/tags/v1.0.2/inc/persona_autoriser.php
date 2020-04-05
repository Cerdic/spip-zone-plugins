<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function persona_autoriser(){}

function autoriser_persona_configurer_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}

?>