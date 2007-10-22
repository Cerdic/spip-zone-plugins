<?php
/**
* Plugin Notation v.0.1
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Definition des boucles
*  
**/

include_spip('base/notation');

/*
function balise_MANOTE($p) {
	$_note = champ_sql('note', $p);

	$p->code = "echo (".$_note.");";
	$p->statut = 'html';
	return $p;
}
*/

/**
* <BOUCLE(NOTATIONS)>
*/
function boucle_NOTATIONS_dist($id_boucle, &$boucles)
{	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_notations";
	
	return calculer_boucle($id_boucle, $boucles);
}

/**
* <BOUCLE(NOTATIONS_ARTICLES)>
*/
function boucle_NOTATIONS_ARTICLES_dist($id_boucle, &$boucles)
{	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_notations_articles";
	
	return calculer_boucle($id_boucle, $boucles);
}

?>