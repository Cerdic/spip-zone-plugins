<?php

include_spip('base/types_serial');

function boucle_ARTICLES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mstatut = $id_table .'.statut';
	$mtype = $id_table .'.'._TYPE;

	// Restreindre au type normal
	if (!isset($boucle->modificateur['criteres'][_TYPE])) {
		array_unshift($boucle->where,array("'='", "'$mtype'", "'\\'article\\''"));
	}

	// Restreindre aux elements publies
	if (!isset($boucle->modificateur['criteres']['statut'])) {
		if (!$GLOBALS['var_preview']) {
			if ($GLOBALS['meta']["post_dates"] == 'non')
				array_unshift($boucle->where,array("'<'", "'$id_table" . ".date'", "'NOW()'"));
			array_unshift($boucle->where,array("'='", "'$mstatut'", "'\\'publie\\''"));
		} else
			array_unshift($boucle->where,array("'IN'", "'$mstatut'", "'(\\'publie\\',\\'prop\\')'"));
	}
	return calculer_boucle($id_boucle, $boucles); 
}

function boucle_RUBRIQUES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mstatut = $id_table .'.statut';
	$mtype = $id_table .'.fonction';

	// Restreindre au type normal
	if (!isset($boucle->modificateur['criteres'][_TYPE])) {
		array_unshift($boucle->where,array("'='", "'$mtype'", "'\\'rubrique\\''"));
	}

	// Restreindre aux elements publies
	if (!isset($boucle->modificateur['criteres']['statut'])) {
		if (!$GLOBALS['var_preview'])
			if (!isset($boucle->modificateur['tout']))
				array_unshift($boucle->where,array("'='", "'$mstatut'", "'\\'publie\\''"));
	}

	return calculer_boucle($id_boucle, $boucles); 
}

?>