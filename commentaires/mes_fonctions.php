<?php

//
// <BOUCLE(FORUMS)>
//
// qui affiche uniquement les forums public meme en preview
function boucle_FORUMS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mstatut = $id_table .'.statut';
	// Par defaut, selectionner uniquement les forums sans mere
	// Les criteres {tout} et {plat} inversent ce choix
	if (!isset($boucle->modificateur['tout']) AND !isset($boucle->modificateur['plat'])) {
		array_unshift($boucle->where,array("'='", "'$id_table." ."id_parent'", 0));
	}
	// Restreindre aux elements publies
	if (!$boucle->modificateur['criteres']['statut']) {
			array_unshift($boucle->where,array("'='", "'$mstatut'", "'\\'publie\\''"));
	}

	return calculer_boucle($id_boucle, $boucles);
}

?>