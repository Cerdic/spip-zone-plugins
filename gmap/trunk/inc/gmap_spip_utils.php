<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Fonctions helpers pour les fonctionalités SPIP
 *
 */

include_spip('inc/meta');

// Obtenir la liste des APIs
function gmap_recuperer_fond($fond, $env)
{
	$GLOBALS['lien_implicite_cible_public'] = true; // hack pour avoir les lien publics et non "privés"
	$page = recuperer_fond($fond, $env);
	unset($GLOBALS['lien_implicite_cible_public']); 

	// Autre possibilité en prenant des fonctions plus roots ?
	// 	$composer = charger_fonction('composer', 'public');
	//	$code = $composer($skel, $mime_type, $gram, $sourcefile, $connect);
	//	$page = $fonc(array('cache' => $cache), array($contexte));
	// cf. public_parametrer_dist (et il y a plusieurs autres trucs...)
	
	return $page;
}

?>