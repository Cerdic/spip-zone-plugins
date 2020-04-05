<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


// declaration vide pour ce pipeline.
function sms_autoriser(){}

function autoriser_texto_calculer_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo'));
}

