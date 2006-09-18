<?php

	function changeAposToGuillInTags($texte) {
		$texte = ereg_replace("='([^']*)'", "=\"\\1\"", $texte);
		return $texte;
	}

	function changeAposToIso($texte) {
		$texte = ereg_replace("'", "&#8217;", $texte);
		return $texte;
	}
	
	function changeNbspToSpace($texte) {
		$texte = ereg_replace('&amp;nbsp;', ' ', $texte);
		return $texte;
	}

	function changeMotImgOnToOff($fichier) {
		$fichier = eregi_replace("moton(.*)", "motoff\\1", $fichier);
		return $fichier;
	}

	function crypt_mail($texte) { 
	   $s = ""; 
	   for ($i=0; $i < strlen($texte); $i++) { 
		 $s .= "&#".ord($texte{$i}).";"; 
	   } 
	   return $s; 
	} 
	
	function crypt_mail_texte($texte) {
	   return preg_replace_callback('|[-\w.]{2,}@[-\w.]{2,}|',
			  create_function('$match', 'return crypt_mail($match[0]);'),
			  $texte);
	}
	
	/*** Surcharge des fonctions de SPIP ***/
	function introduction ($type, $texte, $chapo='', $descriptif='') {
		switch ($type) {
			case 'articles':
			        if (!empty($chapo))
				     return PtoBR(propre(supprimer_tags(couper_intro($chapo,500))));
				else
				  return PtoBR(propre(supprimer_tags(couper_intro($texte, 500))));
				break;
			case 'breves':
				return PtoBR(propre(supprimer_tags(couper_intro($texte, 200))));
				break;
			case 'forums':
				return PtoBR(propre(supprimer_tags(couper_intro($texte, 500))));
				break;
			case 'rubriques':
				if ($descriptif)
					return propre($descriptif);
				else
					return PtoBR(propre(supprimer_tags(couper_intro($texte, 500))));
				break;
		}
	}


function emoticones($chaine) {
// Attention à bien vérifier le chemin
// $chemin = "/".$GLOBALS['dossier_squelettes']."/images/emoticones/";
   $chemin = "images/emoticones/";

		$img_debut = '<img src="'.$chemin;
		$img_fin = '" width="18" height="18" ';

		$chaine = str_replace(":-)", $img_debut . 'smiley-smile.png' . $img_fin . 'alt=":-)" title=":-)"/>', $chaine);
		$chaine = str_replace(":-(", $img_debut . 'smiley-frown.png' . $img_fin . 'alt=":-(" title=":-("/>', $chaine);
		$chaine = str_replace(";-)", $img_debut . 'smiley-wink.png' . $img_fin . 'alt=";-)" title=";-)"/>', $chaine);
		$chaine = str_replace(":-P", $img_debut . 'smiley-tongue-out.png' . $img_fin . 'alt=":-P" title=":-P"/>', $chaine);
		$chaine = str_replace(":-D", $img_debut . 'smiley-laughing.png' . $img_fin . 'alt=":-D" title=":-D"/>', $chaine);
		$chaine = str_replace(":-[", $img_debut . 'smiley-embarassed.png' . $img_fin . 'alt=":-[" title=":-["/>', $chaine);
		$chaine = str_replace(":-\\", $img_debut . 'smiley-undecided.png' . $img_fin . 'alt=":-\\" title=":-\\"/>', $chaine);
		$chaine = str_replace("=-O", $img_debut . 'smiley-surprised.png' . $img_fin . 'alt="=-O" title="=-O"/>', $chaine);
		$chaine = str_replace(":-*", $img_debut . 'smiley-kiss.png' . $img_fin . 'alt=":-*" title=":-*"/>', $chaine);
		$chaine = str_replace(">:-o", $img_debut . 'smiley-yell.png' . $img_fin . 'alt=">:-o" title=">:-o"/>', $chaine);
		$chaine = str_replace("8-)", $img_debut . 'smiley-cool.png' . $img_fin . 'alt="8-)" title="8-)"/>', $chaine);
		$chaine = str_replace(":-$", $img_debut . 'smiley-money-mouth.png' . $img_fin . 'alt=":-$" title=":-$"/>', $chaine);
		$chaine = str_replace(":-!", $img_debut . 'smiley-foot-in-mouth.png' . $img_fin . 'alt=":-!" title=":-!"/>', $chaine);
		$chaine = str_replace("O:-)", $img_debut . 'smiley-innocent.png' . $img_fin . 'alt="O:-)" title="O:-)"/>', $chaine);
		$chaine = str_replace(":'-(", $img_debut . 'smiley-cry.png' . $img_fin . 'alt=":\'-(" title=":\'-("/>', $chaine);
		$chaine = str_replace(":-X", $img_debut . 'smiley-sealed.png' . $img_fin . 'alt=":-X" title=":-X"/>', $chaine);
		$chaine = str_replace(":->", $img_debut . 'smiley-evil.png' . $img_fin . 'alt=":->" title=":->"/>', $chaine);	
		
		return $chaine;
	}


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

function divise($texte,$divise){
    $s="";
    $s=intval($texte / $divise);
    return $s;
    }




?>