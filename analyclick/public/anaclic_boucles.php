<?php

/** Boucle de compteur de telechargements
	#TELECHARGEMENT renvoie le nombre de telechargement par article (SUM groupby id_document).
*/
function boucle_DOC_COMPTEURS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	
	// On calcul les statitiques sur les documents si pas plat
	if (!isset($boucle->modificateur['plat'])) 
	{	$boucle->select[] = "SUM(telechargement) as telechargement";
		$boucle->group[] = "$id_table.id_document";
	}
	
	return calculer_boucle($id_boucle, $boucles); 
}
/* */
?>