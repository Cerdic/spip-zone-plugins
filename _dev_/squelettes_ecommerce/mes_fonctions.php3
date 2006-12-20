<?php
$GLOBALS[ 'dossier_squelettes' ] = "squelettes.spip" ;

/*
 *   +----------------------------------+
 *    Nom du Filtre :    pagination                                               
 *   +----------------------------------+
 *    Date : dimanche 22 août 2004
 *    Auteur :  James (klike<at>free.fr)
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     affiche la liste des pages d'une boucle contenant
 *     un critère de limite du type {debut_xxx, yyy}
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=663
*/

function pagination($total, $position=0, $pas=1, $fonction='') {
  global $clean_link;
  global $pagination_item_avant, $pagination_item_apres, $pagination_separateur;
  tester_variable('pagination_separateur', '&nbsp;| ');
  if (ereg('^debut([-_a-zA-Z0-9]+)$', $position, $match)) {
    $debut_lim = "debut".$match[1];
    $position = intval($GLOBALS['HTTP_GET_VARS'][$debut_lim]);
  }
  $nombre_pages = floor(($total-1)/$pas)+1;
  $texte = '';
  if($nombre_pages>1) {
    $i = 0;
    while($i<$nombre_pages) {
      $clean_link->delVar($debut_lim);
      $clean_link->addVar($debut_lim, strval($i*$pas));
      $url = $clean_link->getUrl();
      if(function_exists($fonction)) $item = call_user_func($fonction, $i+1);
      else $item = strval($i+1);
      if(($i*$pas) != $position) {
        if(function_exists('lien_pagination')) $item = lien_pagination($url, $item, $i+1);
        else $item = "<a href=\"".$url."\">".$item."</a>";
      }
      $texte .= $pagination_item_avant.$item.$pagination_item_apres;
      if($i<($nombre_pages-1)) $texte .= $pagination_separateur;
      $i++;
    }
    //Correction bug: $clean_link doit revenir à son état initial
    $clean_link->delVar($debut_lim);
    if($position) $clean_link->addVar($debut_lim, $position);
     return $texte;
  }
  return '';
}

// FIN du Filtre pagination

/*
 *   +----------------------------------+
 *    Nom du Filtre : Couleur                                               
 *   +----------------------------------+
 *    Date : Vendredi 11 août 2003
 *    Auteur :  Aurélien PIERARD : aurelien.pierard(a)dsaf.pm.gouv.fr
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *		Permet de modifier la couleur du texte
 *		Utilisation pour le rédacteur : [rouge]Lorem ipsum dolor sit amet[/rouge]
 * 		Utilisation pour le webmlaster : [(#TEXTE|couleur)]
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/article.php3?id_article=652
*/
function couleur($texte) {
         $texte = preg_replace("/(\[noir\])(.*?)(\[\/noir\])/", "<span style=\"color:black;\">\\2</span>", $texte);
         $texte = preg_replace("/(\[rouge\])(.*?)(\[\/rouge\])/", "<span style=\"color:red;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[marron\])(.*?)(\[\/marron\])/", "<span style=\"color:maroon;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[vert\])(.*?)(\[\/vert\])/", "<span style=\"color:green;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[vert olive\])(.*?)(\[\/vert olive\])/", "<span style=\"color:olive;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[bleu marine\])(.*?)(\[\/bleu marine\])/", "<span style=\"color:navy;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[violet\])(.*?)(\[\/violet\])/", "<span style=\"color:purple;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[gris\])(.*?)(\[\/gris\])/", "<span style=\"color:gray;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[argent\])(.*?)(\[\/argent\])/", "<span style=\"color:silver;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[vert clair\])(.*?)(\[\/vert clair\])/", "<span style=\"color:chartreuse;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[bleu\])(.*?)(\[\/bleu\])/", "<span style=\"color:yellow;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[fuchia\])(.*?)(\[\/fuchia\])/", "<span style=\"color:fuchsia;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[bleu clair\])(.*?)(\[\/bleu clair\])/", "<span style=\"color:aqua;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[blanc\])(.*?)(\[\/blanc\])/", "<span style=\"color:white;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[bleu azur\])(.*?)(\[\/bleu azur\])/", "<span style=\"color:azure;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[beige\])(.*?)(\[\/beige\])/", "<span style=\"color:bisque;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[brun\])(.*?)(\[\/brun\])/", "<span style=\"color:brown;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[bleu violet\])(.*?)(\[\/bleu violet\])/", "<span style=\"color:blueviolet;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[brun clair\])(.*?)(\[\/brun clair\])/", "<span style=\"color:chocolate;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[rose clair\])(.*?)(\[\/rose clair\])/", "<span style=\"color:cornsilk;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[vert fonce\])(.*?)(\[\/vert fonce\])/", "<span style=\"color:darkgreen;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[orange fonce\])(.*?)(\[\/orange fonce\])/", "<span style=\"color:darkorange;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[mauve fonce\])(.*?)(\[\/mauve fonce\])/", "<span style=\"color:darkorchid;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[bleu ciel\])(.*?)(\[\/bleu ciel\])/", "<span style=\"color:deepskyblue;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[or\])(.*?)(\[\/or\])/", "<span style=\"color:gold;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[ivoire\])(.*?)(\[\/ivoire\])/", "<span style=\"color:ivory;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[orange\])(.*?)(\[\/orange\])/", "<span style=\"color:orange;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[lavande\])(.*?)(\[\/lavande\])/", "<span style=\"color:lavender;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[rose\])(.*?)(\[\/rose\])/", "<span style=\"color:pink;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[prune\])(.*?)(\[\/prune\])/", "<span style=\"color:plum;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[saumon\])(.*?)(\[\/saumon\])/", "<span style=\"color:salmon;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[neige\])(.*?)(\[\/neige\])/", "<span style=\"color:snow;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[turquoise\])(.*?)(\[\/turquoise\])/", "<span style=\"color:turquoise;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[jaune paille\])(.*?)(\[\/jaune paille\])/", "<span style=\"color:wheat;\">\\2</span>", $texte);
	 $texte = preg_replace("/(\[jaune\])(.*?)(\[\/jaune\])/", "<span style=\"color:yellow;\">\\2</span>", $texte);
	 return $texte;
}
// fin couleur

/* Filtre NORM_LIENS v2.0 - 29 juillet 2003 - Par Led

   Permet de normaliser les liens lorsque ceux-ci sont orphelins (sans balise
   HREF). Par exemple:
   "http://www.url.com" deviendra "<a href="http://www.url.com">http://www.url.com</a>"

   Le filtre s'utilise avec les balises #CHAPO, #TEXTE, #PS, #NOTES,
   #INTRODUCTION, #DESCRIPTIF et #BIO.

   SYNTAXE DANS LES SQUELETTES:
   [(#TEXTE|norm_liens)]
   [(#TEXTE|norm_liens{tag}]
   Où tag doit avoir comme valeur blank, self, parent ou top.
   Si aucun tag n'est spécifié la balise HREF n'aura pas de target.

   ATTENTION: Si vous désirez utiliser ce filtre avec le filtre CIBLES_LIENS (du
              21 juillet 2003 et écrit par moi-même) sur une même balise SPIP il
              faut obligatoirement placer le filtre NORM_LIENS en premier.
              Exemples: [(#TEXTE|norm_liens|cibles_liens)]
                        [(#TEXTE|norm_liens{tag}|cibles_liens)]
*/

function norm_liens($texte, $target='') {

    $target = '_'.$target;
    if ( $target != "_" ) {
        $texte = eregi_replace(' http://([^ <]*)', ' <a href="http://\\1" target="'.$target.'">http://\\1</a>', $texte);
        $texte = eregi_replace(' ftp://([^ <]*)', ' <a href="ftp://\\1" target="'.$target.'">ftp://\\1</a>', $texte);
        $texte = eregi_replace(' www.([^ <]*)', ' <a href="http://www.\\1" target="'.$target.'">www.\\1</a>', $texte);
        $texte = eregi_replace(' ftp.([^ <]*)', ' <a href="ftp://ftp.\\1" target="'.$target.'">ftp.\\1</a>', $texte);
        $texte = eregi_replace('^http://([^ <]*)', '<a href="http://\\1" target="'.$target.'">http://\\1</a>', $texte);
        $texte = eregi_replace('^ftp://([^ <]*)', '<a href="ftp://\\1" target="'.$target.'">ftp://\\1</a>', $texte);
        $texte = eregi_replace('^www.([^ <]*)', '<a href="http://www.\\1" target="'.$target.'">www.\\1</a>', $texte);
        $texte = eregi_replace('^ftp.([^ <]*)', '<a href="ftp://ftp.\\1" target="'.$target.'">ftp.\\1</a>', $texte);
        $texte = eregi_replace('<p class="spip">http://([^ <]*)', '<p class="spip"><a href="http://\\1" target="'.$target.'">http://\\1</a>', $texte);
        $texte = eregi_replace('<p class="spip">ftp://([^ <]*)', '<p class="spip"><a href="ftp://\\1" target="'.$target.'">ftp://\\1</a>', $texte);
        $texte = eregi_replace('<p class="spip">www.([^ <]*)', '<p class="spip"><a href="http://www.\\1" target="'.$target.'">www.\\1</a>', $texte);
        $texte = eregi_replace('<p class="spip">ftp.([^ <]*)', '<p class="spip"><a href="ftp://ftp.\\1" target="'.$target.'">ftp.\\1</a>', $texte);
        }
    else {
        $texte = eregi_replace(' http://([^ <]*)', ' <a href="http://\\1">http://\\1</a>', $texte);
        $texte = eregi_replace(' ftp://([^ <]*)', ' <a href="ftp://\\1">ftp://\\1</a>', $texte);
        $texte = eregi_replace(' www.([^ <]*)', ' <a href="http://www.\\1">www.\\1</a>', $texte);
        $texte = eregi_replace(' ftp.([^ <]*)', ' <a href="ftp://ftp.\\1">ftp.\\1</a>', $texte);
        $texte = eregi_replace('^http://([^ <]*)', '<a href="http://\\1">http://\\1</a>', $texte);
        $texte = eregi_replace('^ftp://([^ <]*)', '<a href="ftp://\\1">ftp://\\1</a>', $texte);
        $texte = eregi_replace('^www.([^ <]*)', '<a href="http://www.\\1">www.\\1</a>', $texte);
        $texte = eregi_replace('^ftp.([^ <]*)', '<a href="ftp://ftp.\\1">ftp.\\1</a>', $texte);
        $texte = eregi_replace('<p class="spip">http://([^ <]*)', '<p class="spip"><a href="http://\\1">http://\\1</a>', $texte);
        $texte = eregi_replace('<p class="spip">ftp://([^ <]*)', '<p class="spip"><a href="ftp://\\1">ftp://\\1</a>', $texte);
        $texte = eregi_replace('<p class="spip">www.([^ <]*)', '<p class="spip"><a href="http://www.\\1">www.\\1</a>', $texte);
        $texte = eregi_replace('<p class="spip">ftp.([^ <]*)', '<a href="ftp://ftp.\\1">ftp.\\1</a>', $texte);
        }
    $texte = eregi_replace('([^ >]*)@([^ ,:!?&<]*)', ' <a href="mailto:\\1@\\2">\\1@\\2</a>', $texte);

    return $texte;
}


?>
