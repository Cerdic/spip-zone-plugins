<?php

/**
 * Prendre en compte les criteres "technique" et "tout"
 */
function boucle_MOTS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	
	// Restreindre aux mots cles non techniques
	if (!isset($boucle->modificateur['criteres']['technique']) && 
		!isset($boucle->modificateur['tout'])) {
			$boucle->from["groupes"] =  "spip_groupes_mots";
			$boucle->where[]= array("'='", "'groupes.id_groupe'", "'$id_table.id_groupe'");
			$boucle->where[]= array("'='", "'groupes.technique'", "'\"\"'");	
	} 

	return calculer_boucle($id_boucle, $boucles); 
}

/**
 * Prendre en compte les criteres "technique" et "tout"
 */
function boucle_GROUPES_MOTS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mtechnique = $id_table .'.technique';

	// Restreindre aux mots cles non techniques
	if (!isset($boucle->modificateur['criteres']['technique'])&&!isset($boucle->modificateur['tout'])) {
		$boucle->where[]= array("'='", "'$mtechnique'", "'\"\"'");
	}

	return calculer_boucle($id_boucle, $boucles); 
}





?>
