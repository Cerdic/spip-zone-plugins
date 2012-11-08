<?php
/**
* Plugin Analyclick
*
* @author: Jean-Marc Viglino (ign.fr) V0.1
* @author: Pierre KUHN V1
*
* Copyright (c) 2011-12
* Logiciel distribue sous licence GPL.
*
**/

function boucle_DOC_COMPTEURS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	
	// On calcul les statitiques sur les documents si pas plat
	if (!isset($boucle->modificateur['plat'])) 
	{	$boucle->select[] = "SUM(telechargement) as telechargement";
		$boucle->group[] = "$id_table.id_document";
		// Modifie le critere par telechargement pour pointer sur la somme
		$c = count($boucle->order);
		for ($i=0; $i<$c; $i++)
			$boucle->order[$i] = str_replace("$id_table.telechargement",'telechargement',$boucle->order[$i]);
	}
	
	return calculer_boucle($id_boucle, $boucles); 
}
?>
