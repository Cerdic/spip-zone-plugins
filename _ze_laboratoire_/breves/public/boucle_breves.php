<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// <BOUCLE(BREVES)>
//
// http://code.spip.net/@boucle_BREVES_dist
function boucle_BREVES_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mstatut = $id_table .'.statut';

	// Restreindre aux elements publies
	if (!isset($boucle->modificateur['criteres']['statut'])) {
		if (!$GLOBALS['var_preview'])
			array_unshift($boucle->where,array("'='", "'$mstatut'", "'\\'publie\\''"));
		else
			array_unshift($boucle->where,array("'IN'", "'$mstatut'", "'(\\'publie\\',\\'prop\\')'"));
	}

	return calculer_boucle($id_boucle, $boucles); 
}


?>
