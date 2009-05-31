<?php

function sans_guillemets($texte) {
   $texte = str_replace('"', '', $texte);
   return $texte;
}

function coupe($texte, $taille=50, $suite) {
  $texte = couper($texte, $taille);
  $texte = PtoBR(propre(supprimer_tags($texte)));
  $texte = str_replace('&nbsp;(...)', $suite, $texte);
  return $texte;
}


// filtre askeywords: transforme un texte en liste de mots-clés pour meta-tag keywords
// exemple d'usage: [<meta name="keywords" content="(#TITRE|askeywords)" />]
function askeywords($texte) {
  $texte = sans_guillemets($texte);
   $notkeys = _T('acs:meta_not_keywords');

   $notkeys = explode(',', $notkeys);
   // Transforme tous les mots inutilisables comme keywords en expression régulière "mot entier", insensible à la casse
   foreach ($notkeys as $key=>$notkey ) {
      $notkeys[$key] = '/\b('.$notkey.')\b/';
   }
   $texte = strtolower(textebrut($texte));
   // Suppression des mots qui ne conviennent pas comme keywords (liste meta_not_keywords du fichier de langue acs_xx.lang)
   $texte = preg_replace($notkeys, '*', $texte);
   // Suppression de la ponctuation et des espaces de début et de fin
   $texte = preg_replace(array('/[;:,.?!\']/', '/(\s+$)/', '/(^\s+)/'), '', $texte);
   // remplacement des espaces restants par des virgules
   $texte = preg_replace('/(\s+)/', ',', $texte);
   return $texte;
}

/*
 *   +----------------------------------+
 *    Nom du Filtre :  cm (crypt_mail)
 *   +----------------------------------+
 *    Date : dimanche 6 juillet 2003
 *    Auteur :  Jean-Pierre KUNTZ
 *        alias Coyote
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     Crypter une chaînee de texte (email, URL)
 *     sans en empêcher l'affichage à l'écran
 *     ni l'utilisation par un logiciel de messagerie
 *   +-------------------------------------+
 *
 *   exemple d'utilisation dans un squelette :
 *
 *   <a href="mailto:[(#EMAIL|cm)]">[(#EMAIL|cm)]</a>
 *
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=197
*/


function cm($texte) {
   $s = "";
   for ($i=0; $i < strlen($texte); $i++) {
      $s.="&#".ord($texte{$i}).";";
   }
   return $s;
}

?>