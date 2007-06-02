<?php
include_spip('base/mots_techniques');

function boucle_MOTS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_mots";

	// Restreindre aux mots cles non techniques
	if (!isset($boucle->modificateur['tout'])&&!isset($boucle->modificateur['criteres']['technique'])) {
		$boucle->from["groupes"] =  "spip_groupes_mots";
		$boucle->where[]= array("'='", "'groupes.id_groupe'", "'$id_table.id_groupe'");
		$boucle->where[]= array("'='", "'groupes.technique'", "'\"\"'");
	}

	return calculer_boucle($id_boucle, $boucles); 
}

//
// <BOUCLE(GROUPES_MOTS)>
//
// http://doc.spip.org/@boucle_GROUPES_MOTS_dist
function boucle_GROUPES_MOTS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_groupes_mots";
	$mtechnique = $id_table .'.technique';

	// Restreindre aux mots cles non techniques
	if (!isset($boucle->modificateur['criteres']['technique'])&&!isset($boucle->modificateur['tout'])) {
		$boucle->where[]= array("'='", "'$mtechnique'", "'\"\"'");
	}

	return calculer_boucle($id_boucle, $boucles); 
}

?>