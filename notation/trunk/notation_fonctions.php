<?php
/**
 * Plugin Notation
 * par JEM (jean-marc.viglino@ign.fr) / b_b
 * 
 * Copyright (c) 2008
 * Logiciel libre distribue sous licence GNU/GPL.
 *  
 *  
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/notation_autorisations');
include_spip('inc/notation_balises');

/** 
 * Filtre pour les tableaux :
 * transforme une liste separee par des virgules en un tableau exploitable avec IN
 * 
 * @param string $tab
 * 		Liste séparée par des virgules
 * @return array $tab
 * 		Le tableau des éléments de la liste
 */
function notation_tab ($tab){
	$tab = explode(',',$tab);
	return $tab;
}


/** 
 * Gestion des notations dans les forums
 * Supprime [notation]
 * Transforme les [+] et [-] en images
 */

function notation_critique($p){
	$p = preg_replace('/\[notation\]/', '', $p);
	$p = preg_replace('/\[\+\]/', '<img src="'.find_in_path('img_pack/notation-plus.gif').'"> ', $p);
	$p = preg_replace('/\[-\]/', '<img src="'.find_in_path('img_pack/notation-moins.gif').'"> ', $p);
	return $p;
}

?>