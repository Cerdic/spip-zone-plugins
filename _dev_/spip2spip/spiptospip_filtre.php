<?php

/*
 *   +----------------------------------+
 *    Nom du Filtre :   spip2spip_respecte_ln                                            
 *   +----------------------------------+
 *    Date :    20060327
 *    Auteur :  erational - http://www.erational.org
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     remplace les retours type line break \n  par le motif __LN__
 *     pour les conserver même après l'application du filtre texte_backend
 *   +-------------------------------------+ 
 *  
 *   Ce filtre est utilisé par la contrib SPIP2SPIP
*/

function spip2spip_respecte_ln($texte) {
	$texte = preg_replace("/\s\s+/$u", "__LN__", $texte); 
	return $texte;
}

/*
 *   +----------------------------------+
 *    Nom du Filtre :   spip2spip_respecte_img                                            
 *   +----------------------------------+
 *    Date :    20060515
 *    Auteur :  erational - http://www.erational.org
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     remplace les documents et imags de type <doc34|left> par le motif __DOC34|left__
 *     pour les conserver même après l'application du filtre texte_backend
 *   +-------------------------------------+ 
 *  
 *   Ce filtre est utilisé par la contrib SPIP2SPIP
*/


function spip2spip_respecte_img($texte) {
	$texte = preg_replace("/<img(.*?)>/i", "__IMG$1__",$texte);
	$texte = preg_replace("/<doc(.*?)>/i", "__DOC$1__",$texte);
	return $texte;
}


?>
