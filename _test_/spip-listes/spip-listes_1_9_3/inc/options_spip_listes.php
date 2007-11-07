<?php
/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

function spiplistes_log($texte, $level = LOG_WARNING) {
	if(__server_in_private_ip_adresses()
		&& __plugin_lire_key_in_serialized_meta('opt_console_syslog', _SPIPLISTES_META_PREFERENCES)
	) {
		__syslog_trace($texte, $level);
	}
	else if($level < LOG_DEBUG) {
		// Taille du log SPIP trop courte
		// Ne pas envoyer si DEBUG sinon tronque sans cesse
		spip_log($texte, 'spiplistes');
	}
}

function spiplistes_langue_liste ($id_liste) {
	$lang=spip_query("SELECT lang FROM spip_listes WHERE id_liste="._q($id_liste)." LIMIT 0,1");
	$lang=spip_fetch_array($lang);
	$lang= $lang['lang'];
	return $lang ;
}




// Bloogletter



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
 * titre : version_texte
 * d'après Clever Mail (-> NHoizey), mais en mieux.
****/

function version_texte ($in) {
// Nettoyage des liens des notes de bas de page
$out = ereg_replace("<a href=\"#n(b|h)[0-9]+-[0-9]+\" name=\"n(b|h)[0-9]+-[0-9]+\" class=\"spip_note\">([0-9]+)</a>", "\\3", $in);

// Supprimer tous les liens internes
$patterns = array("/\<a href=['\"]#(.*?)['\"][^>]*>(.*?)<\/a>/ims");
$replacements = array("\\2");
$out = preg_replace($patterns,$replacements, $out);

// Supprime feuille style
$out = ereg_replace("<style[^>]*>[^<]*</style>", "", $out);

// les puces
$out = str_replace($GLOBALS['puce'], "\n".'-', $out);

// Remplace tous les liens	
$patterns = array(
           "/\<a href=['\"](.*?)['\"][^>]*>(.*?)<\/a>/ims"
       );
       $replacements = array(
           "\\2 (\\1)"
       );
$out = preg_replace($patterns,$replacements, $out);

$out = ereg_replace("<h1[^>]*>", "_SAUT__SAUT_--------------------------------------------------------_SAUT_", $out);
$out = str_replace("</h1>", "_SAUT__SAUT_--------------------------------------------------------_SAUT__SAUT_", $out);
$out = ereg_replace("<h2[^>]*>", "_SAUT__SAUT_............... ", $out);
$out = str_replace("</h2>", " ..............._SAUT__SAUT_", $out);
$out = ereg_replace("<h3[^>]*>", "_SAUT__SAUT_*", $out);
$out = str_replace("</h3>", "*_SAUT__SAUT_", $out);

// Les notes de bas de page
    $out = str_replace("<p class=\"spip_note\">", "\n", $out);
    $out = ereg_replace("<sup>([0-9]+)</sup>", "[\\1]", $out);

$out = str_replace("<p[^>]*>", "\n\n", $out);

    //$out = str_replace('<br /><img class=\'spip_puce\' src=\'puce.gif\' alt=\'-\' border=\'0\'>', "\n".'-', $out);
$out = ereg_replace ('<li[^>]>', "\n".'-', $out);
    //$out = str_replace('<li>', "\n".'-', $out);


    // accentuation du gras -
    // <b>texte</b> -> *texte*
    $out = ereg_replace ('<b[^>|r]*>','*' ,$out);
    $out = str_replace ('</b>','*' ,$out);

	// accentuation du gras -
    // <strong>texte</strong> -> *texte*
    $out = ereg_replace ('<strong[^>]*>','*' ,$out);
    $out = str_replace ('</strong>','*' ,$out);


    // accentuation de l'italique
    // <i>texte</i> -> *texte*
    $out = ereg_replace ('<i[^>|mg]*>','*' ,$out);
    $out = str_replace ('</i>','*' ,$out);

	$out = str_replace('&oelig;', 'oe', $out);
	$out = str_replace("&nbsp;", " ", $out);
	$out = filtrer_entites($out);

	//attention, trop brutal pour les logs irc <@RealET>
	$out = supprimer_tags($out);
	
	$out = str_replace("\x0B", "", $out); 
	$out = ereg_replace("\t", "", $out) ;
	$out = ereg_replace("[ ]{3,}", "", $out);
	
	// espace en debut de ligne
	$out = preg_replace("/(\r\n|\n|\r)[ ]+/", "\n", $out);

//marche po
	// Bring down number of empty lines to 4 max
    $out = preg_replace("/(\r\n|\n|\r){3,}/m", "\n\n", $out);
	
	//retablir les saut de ligne
	$out = preg_replace("/(_SAUT_){3,}/m", "_SAUT__SAUT__SAUT_", $out);
	$out = preg_replace("/_SAUT_/", "\n", $out);
	//saut de lignes en debut de texte
	$out = preg_replace("/^(\r\n|\n|\r)+/", "\n\n", $out);
	//saut de lignes en debut ou fin de texte
	$out = preg_replace("/(\r\n|\n|\r)+$/", "\n\n", $out);

    return $out;

}


/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
?>