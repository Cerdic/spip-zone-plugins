<?php

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/

include_spip('inc/affichage');
include_spip('inc/extra_plus');

// Bloogletter


// Verifier la conformite d'une ou plusieurs adresses email
function email_valide_bloog($adresse) {
	$adresses = explode(',', $adresse);
	if (is_array($adresses)) {
		while (list(, $adresse) = each($adresses)) {
			// nettoyer certains formats
			// "Marie Toto <Marie@toto.com>"
			$adresse = eregi_replace("^[^<>\"]*<([^<>\"]+)>$", "\\1", $adresse);
			// pas RFC 822  mais un truc plus restrictif pour les bounces
			if (!eregi('^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$', trim($adresse)))
				return false;
		}
		return true;
	}
	return false;
}

/****
 * titre : propre_bloog
 * Enleve les enluminures Spip pour la bloogletter
 Vincent CARON
****/

function propre_bloog($texte) {

        $texte = ereg_replace("<p class=\"spip\">(\r\n|\n|\r)?</p>",'',$texte);
        $texte = eregi_replace("\n{3}", "\n", $texte);
       
      
      // div imbrique dans un p
        $texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<div([^>]*)>" , "<div\\2>" , $texte);
        $texte = eregi_replace( "<\/div>(\r\n|\n|\r| )*<\/p>" , "</div>" , $texte);
        
        // style imbrique dans un p
        $texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<style([^>]*)>" , "<style>" , $texte);
        $texte = eregi_replace( "<\/style>(\r\n|\n|\r| )*<\/p>" , "</style>" , $texte);
      
      
        // h3 imbrique dans un p
        $texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<h3 class=\"spip\">" , "<h3>" , $texte);
        $texte = eregi_replace( "<\/h3>(\r\n|\n|\r| )*<\/p>" , "</h3>" , $texte);

	// h2 imbrique dans un p
        $texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<h2>" , "<h2>" , $texte);
        $texte = eregi_replace( "<\/h2>(\r\n|\n|\r| )*<\/p>" , "</h2>" , $texte);
        
    // h1 imbrique dans un p
        $texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<h1>" , "<h1>" , $texte);
        $texte = eregi_replace( "<\/h1>(\r\n|\n|\r| )*<\/p>" , "</h1>" , $texte);
        

	// tableaux imbriques dans p
       $texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<(table|TABLE)" , "<table" , $texte);
       $texte = eregi_replace( "<\/(table|TABLE)>(\r\n|\n|\r| )*<\/p>" , "</table>" , $texte);

	// TD imbriques dans p
       $texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<(\/td|\/TD)" , "</td" , $texte);
       //$texte = eregi_replace( "<\/(td|TD)>(\r\n|\n|\r| )*<\/p>" , "</td>" , $texte);

	// p imbriques dans p
       $texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<(p|P)" , "<p" , $texte);
       //$texte = eregi_replace( "<\/(td|TD)>(\r\n|\n|\r| )*<\/p>" , "</td>" , $texte);

         // DIV imbriques dans p
       $texte = eregi_replace( "<p class=\"spip\">(\r\n|\n|\r| )*<(div|DIV)" , "<div" , $texte);
       $texte = eregi_replace( "<\/(DIV|div)>(\r\n|\n|\r| )*<\/p>" , "</div>" , $texte);

 //$texte = PtoBR($texte);
  $texte = ereg_replace ("\.php3&nbsp;\?",".php3?", $texte);
  $texte = ereg_replace ("\.php&nbsp;\?",".php?", $texte);

  return $texte;
}
/****
 * titre : absolute_url
 * d'apres Clever Mail (-> NHoizey)
****/

function absolute_url ($chaine) {
    // TBI : quid si le href n'est pas en premier ?     
    $URL_SITE_SPIP = lire_meta ('adresse_site');

    // rajout d'un / éventuellement 
    if (substr ($URL_SITE_SPIP, strlen($URL_SITE_SPIP)-1, 1) != '/') $URL_SITE_SPIP .= '/';

    $chaine = eregi_replace ('<a href="' , '<a href="'.$URL_SITE_SPIP, $chaine); 
    $chaine = eregi_replace ('<a href="'.$URL_SITE_SPIP.'http://([^"]*)"', "<a href=\"http://\\1\"", $chaine);
    $chaine = eregi_replace ('<a href="'.$URL_SITE_SPIP.'mailto:([^"]*)"', "<a href=\"mailto:\\1\"", $chaine);
    $chaine = eregi_replace ('<a href="'.$URL_SITE_SPIP.'#([^"]*)"', "<a href=\"#\\1\"", $chaine);

    $chaine = eregi_replace ('<img src="' , '<img src="'.$URL_SITE_SPIP, $chaine); 
    $chaine = eregi_replace ('<img src="'.$URL_SITE_SPIP.'http://([^"]*)"', "<img src=\"http://\\1\"", $chaine);
    $chaine = eregi_replace ('<img src=\'' , '<img src=\''.$URL_SITE_SPIP, $chaine);  
    $chaine = eregi_replace ('<img src=\''.$URL_SITE_SPIP.'http://([^"]*)\'', "<img src=\'http://\\1\'", $chaine);

    return $chaine;
}


/****
 * titre : version_texte
 * d'après Clever Mail (-> NHoizey)
****/

function version_texte ($in) {
// Nettoyage des liens des notes de bas de page
$out = ereg_replace("<a href=\"#n(b|h)[0-9]+-[0-9]+\" name=\"n(b|h)[0-9]+-[0-9]+\" class=\"spip_note\">([0-9]+)</a>", "\\3", $in);

// Supprimer tous les liens internes
$patterns = array("/\<a href=['\"]#(.*?)['\"][^>]*>(.*?)<\/a>/");
$replacements = array("\\2");
$out = preg_replace($patterns,$replacements, $out);

// Supprime feuille style
$out = ereg_replace("<style[^>]*>[^<]*</style>", "", $out);

// les puces
$out = str_replace($GLOBALS['puce'], "\n".'-', $out);

// Remplace tous les liens	
$patterns = array(
           "/\<a href=['\"](.*?)['\"][^>]*>(.*?)<\/a>/"
       );
       $replacements = array(
           "\\2 (\\1)"
       );
$out = preg_replace($patterns,$replacements, $out);

$out = ereg_replace("<h1[^>]*>", "\n--------------------------------------------------------\n", $out);
$out = str_replace("</h1>", "\n--------------------------------------------------------\n", $out);
$out = ereg_replace("<h2[^>]*>", "\n............... ", $out);
$out = str_replace("</h2>", " ...............\n", $out);
$out = ereg_replace("<h3[^>]*>", "\n + ", $out);
$out = str_replace("</h3>", "\n", $out);
$out = str_replace("<p class=\"spip\"[^>]*>", "\n", $out);

// Les notes de bas de page
    $out = str_replace("<p class=\"spip_note\">", "\n", $out);
    $out = ereg_replace("<sup>([0-9]+)</sup>", "[\\1]", $out);

    //$out = str_replace('<br /><img class=\'spip_puce\' src=\'puce.gif\' alt=\'-\' border=\'0\'>', "\n".'-', $out);
$out = ereg_replace ('<li[^>]>', "\n".'-', $out);
    //$out = str_replace('<li>', "\n".'-', $out);


    // accentuation du gras -
    // <b>texte</b> -> *texte*
    $out = ereg_replace ('<b[^>|r]*>','*' ,$out);
    $out = str_replace ('</b>','*' ,$out);

    // accentuation de l'italique
    // <i>texte</i> -> *texte*
    $out = ereg_replace ('<i[^>|mg]*>','*' ,$out);
    $out = str_replace ('</i>','*' ,$out);

	$out = str_replace('&oelig;', 'oe', $out);
	$out = str_replace("&nbsp;", " ", $out);
	$out = filtrer_entites($out);

	$out = supprimer_tags($out);

        //$out = ereg_replace("^(\n|\r|\r\n| )+", "", $out);
		 //$out = ereg_replace("^( )", "", $out);
		 //$out = ereg_replace("(\n\n\n)+", "", $out) ;
		 //$out = ereg_replace("(\n\n)+", "", $out) ;
		 $out = str_replace(chr(160), "", $out); 
		 $out = str_replace("\x0B", "", $out); 
		 $out = ereg_replace("\t", "", $out) ;
		 //$out = ereg_replace("[\n]{3,}", "\n\n", $out);
		 $out = ereg_replace("[ ]{3,}", "", $out);
		  // Bring down number of empty lines to 2 max
        $out = preg_replace("/\n\s+\n/", "\n\n", $out);
        $out = preg_replace("/[\n]{3,}/", "\n\n", $out);
		
		//$out = trim($out) ;
		
    return $out;

}


//Balises Spip-listes

function calcul_MELEUSE_CRON() {
  global $include_ok;
   if(!$include_ok) {
include_spip("inc/meleuse-cron");
$include_ok = true;
}
   return '';
}

function balise_MELEUSE_CRON($p) {
   $p->code = "calcul_MELEUSE_CRON()";
   $p->statut = 'php';
   return $p;
}


function calcul_DATE_MODIF_SITE() {
   $date_art=spip_query("SELECT date,titre FROM spip_articles WHERE statut='publie' ORDER BY date DESC LIMIT 0,1");
   $date_art=spip_fetch_array($date_art);
   $date_art= $date_art['date'];
   
   $date_bre=spip_query("SELECT date_heure,titre FROM spip_breves WHERE statut='publie' ORDER BY date_heure DESC LIMIT 0,1");
   $date_bre=spip_fetch_array($date_bre);
   $date_bre= $date_bre['date_heure'];
   
   $date_modif= ($date_bre>$date_art)? $date_bre : $date_art ;   
   return  $date_modif;
}

function balise_DATE_MODIF_SITE($p) {
   $p->code = "calcul_DATE_MODIF_SITE()";
   $p->statut = 'php';
   return $p;
}


/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
?>
