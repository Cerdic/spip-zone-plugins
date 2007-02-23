<?php

// l'argument align n'est plus jamais fourni
// http://doc.spip.org/@icone
function icone_etendue($texte, $lien, $fond, $fonction="", $align="", $afficher='oui'){
	global $spip_display;

	if ($fonction == "supprimer.gif") {
		$style = '-danger';
	} else {
		$style = '';
		if (strlen($fonction) < 3) $fonction = "rien.gif";
	}

	if ($spip_display == 1){
		$hauteur = 20;
		$largeur = 100;
		$title = $alt = "";
	}
	else if ($spip_display == 3){
		$hauteur = 30;
		$largeur = 30;
		$title = "\ntitle=\"$texte\"";
		$alt = $texte;
	}
	else {
		$hauteur = 70;
		$largeur = 100;
		$title = '';
		$alt = $texte;
	}

	$size = 24;
	if (preg_match("/-([0-9]{1,3})[.](gif|png)$/i",$fond,$match))
		$size = $match[1];
	if ($spip_display != 1 AND $spip_display != 4){
		if ($fonction != "rien.gif"){
		  $icone = http_img_pack($fonction, $alt, "$title width='$size' height='$size'\n" .
					  http_style_background($fond, "no-repeat center center"));
		}
		else {
			$icone = http_img_pack($fond, $alt, "$title width='$size' height='$size'");
		}
	} else $icone = '';

	if ($spip_display != 3){
		$icone .= "<span>$texte</span>";
	}

	// cas d'ajax_action_auteur: faut defaire le boulot 
	// (il faudrait fusionner avec le cas $javascript)
	if (preg_match(",^<a\shref='([^']*)'([^>]*)>(.*)</a>$,i",$lien,$r))
	  list($x,$lien,$atts,$texte)= $r;
	else $atts = '';
	$lien = "\nhref='$lien'$atts";

	$icone = "\n<table cellpadding='0' class='pointeur' cellspacing='0' border='0' width='$largeur'"
	. ">\n<tr><td class='icone36$style'><a"
	. $lien
	. '>'
	. $icone
	. "</a></td></tr></table>\n";

	if ($afficher == 'oui')	echo $icone; else return $icone;
}
?>