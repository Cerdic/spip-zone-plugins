<?php

// S�curit�
if (!defined("_ECRIRE_INC_VERSION")) return;

// Fonction appel� par le pipeline
function noizetier_autoriser(){}


function autoriser_noizetier_configurer_dist($faire, $type, $id, $qui, $opt) {
	$config = unserialize($GLOBALS['meta']['noizetier']);
	return
		$qui['webmestre']=='oui'
		AND $qui['statut'] == '0minirezo'
		AND !$qui['restreint']
		;
}

?>