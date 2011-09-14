<?php

//juste pour E dans l'O
function oeuf($str){
	$pattern = "(Œ|œ|&#338;)";

if($str!="") {
$str= preg_replace( "$pattern" , "Oe" , $str);
}
 return $str;

}

  // supprimer le # dans une chaine (cf couleurs)
function no_diese($txt) {
        return str_replace('#', '', $txt);
}


/*
 *   +----------------------------------+
 *    Nom des Filtres :    noop, filtre_max, coef et repeat
 *   +----------------------------------+
 *    Date : 23 Mars 2005
 *    Auteur :  Pierre Andrews (mortimer.pa@free.fr)
 *   +-------------------------------------+
 *    Fonctions de ces filtres : ces filtres permettent
 *   de faire un affichage variant en fonction de l'importance
 *    de l'objet.  Vois la contrib pour plus d'informations.
 *   +-------------------------------------+
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.spip_contrib.net/article.php3?id_article=879
*/

function noop($texte) {
  return '';
}

function filtre_max($texte, $id='tout') {
  static $max = array();
  if($max[$id] < $texte) {
    $max[$id] = $texte;
  }
  return $max[$id];
}

function coef($max,$nbr,$nbrMax=6) {
  return 1+($nbr/$max*$nbrMax);
}

function repeat($nombre,$texte,$avant,$apres,$min = 0) {
  if($nombre > $min) {
    for($i=0;$i < $nombre;$i++) {
      $texte = $avant.$texte.$apres;
    }
    return $texte;
  } else
    return '';
}
?>