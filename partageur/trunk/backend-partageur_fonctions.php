<?php
if (!defined('_ECRIRE_INC_VERSION')) return;
/*
 *   +----------------------------------+
 *    Nom du Filtre :   partageur_respecte_ln                                          
 *   +----------------------------------+
 *    Fonctions de ce filtre :
 *     remplace les retours type line break \n  par le motif __LN__
 *     pour les conserver même après l'application du filtre texte_backend
 *   +-------------------------------------+ 
 *  
 *   Ce filtre est utilisé par le plugin Partageur et SPIP2SPIP
*/

function partageur_respecte_ln($texte) {
  $texte = preg_replace("/\n/$u", "__LN__", $texte);  // version pour flux v1.5
	return $texte;
}

/*
 *   +----------------------------------+
 *    Nom du Filtre :   spip2spip_respecte_img                                            
 *   +----------------------------------+
 *    Fonctions de ce filtre :
 *     remplace les documents et imags de type <doc34|left> par le motif __DOC34|left__
 *     pour les conserver même après l'application du filtre texte_backend
 *   +-------------------------------------+ 
 *  
 *   Ce filtre est utilisé par le plugin Partageur et SPIP2SPIP
*/


function partageur_respecte_img($texte) {
	$texte = preg_replace("/<img(.*?)>/i", "__IMG$1__",$texte);
	$texte = preg_replace("/<doc(.*?)>/i", "__DOC$1__",$texte);
	return $texte;
}


?>