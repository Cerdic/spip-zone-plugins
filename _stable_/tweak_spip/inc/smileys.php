<?php

// Filtre SMILEYS - 23 janvier 2003
// Modifs - 25 décembre 2006
//
// pour toute suggestion, remarque, proposition d'ajout d'un 
// smileys, etc ; reportez vous au forum de l'article :
// http://www.uzine.net/spip_contrib/article.php3?id_article=38
// http://www.spip-contrib.net/Smileys

function smileys($chaine) {
$chemin = dirname(find_in_path('img/smileys/diable.png')).'/';
$chemin = '<img ALT="smiley" src="'.$chemin;

// On peut mettre les smileys-images où l'on veut. Mais il faut 
// penser à modifier la variable $chemin de la fonction en conséquence.

         $chaine = str_replace(':->', $chemin . 'diable.png">', $chaine);

         $chaine = str_replace(':-((', $chemin . 'en_colere.png">', $chaine);
         $chaine = str_replace(':-(', $chemin . 'pas_content.png">', $chaine);
		 
         $chaine = str_replace(array(':-))', ':))', ':-D'), $chemin . 'mort_de_rire.png">', $chaine);
         $chaine = str_replace(array(':-)', ':)'), $chemin . 'sourire.png">', $chaine);
		 
         $chaine = str_replace(array(":'-))", ":'-D"), $chemin . 'pleure_de_rire.png">', $chaine);
         $chaine = str_replace(":'-(", $chemin . 'triste.png">', $chaine);

         $chaine = str_replace(':o)', $chemin . 'rigolo.png">', $chaine);
		 
         $chaine = str_replace('B-)', $chemin . 'lunettes.png">', $chaine);

         $chaine = str_replace(array(';-)',';)'), $chemin . 'clin_d-oeil.png">', $chaine);

         $chaine = str_replace(array(':-p',':-P'), $chemin . 'tire_la_langue.png">', $chaine);

         $chaine = str_replace(':-|', $chemin . 'bof.png">', $chaine);
         $chaine = str_replace(':-/', $chemin . 'mouai.png">', $chaine);

         $chaine = str_replace(array(':-o', ':-O') , $chemin . 'surprit.png">', $chaine);
		 

        return $chaine;
}
?>
