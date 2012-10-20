<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

/**
 * Balise #MS_NOM_BOUCLE
 *
 * Récupère le nom de la boucle parente
 *
 * @param unknown_type $p
 */
function balise_BIGBROTHER_NOM_BOUCLE($p){
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	$type_pagination = $p->boucles[$b]->modificateur['debut_nom'];
	if($type_pagination){
		$b = $type_pagination;
	}
	$p->code = $b;
	return $p;
}

?>