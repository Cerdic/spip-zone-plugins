<?php

/*
 *   +----------------------------------+
 *    Nom du Filtre :    accord_pluriel
 *   +----------------------------------+
 *    Date : mercredi 16 avril 2003
 *    Auteur :  Roustoubi (roustoubi@tiscali.fr)
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *    Accorde le nom singulier passé en paramètre avec le nombre qui le quantifie
 *    Exemple : [#TOTAL_BOUCLE (#TOTAL_BOUCLE|accord_pluriel{article})]
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=111
*/

function accord_pluriel ($nombre, $nom1='', $nom2='') {
	if ($nom1=='') { return "Erreur filtre <b>&laquo; accord_pluriel &raquo;</b> : probl&egrave;me de param&egrave;tre"; }
	if ($nom2!='') {
		$nom2 = " ".$nom2;
		$nom2s = $nom2."s";
	}
	if ($nombre == "0" OR $nombre == "1") {
		$texte = $nombre."&nbsp;".$nom1.$nom2 ;
	}
	else {
		$texte = $nombre."&nbsp;".$nom1."s".$nom2s ;
	}
	return $texte ; 
}
// FIN du Filtre accord_pluriel


?>