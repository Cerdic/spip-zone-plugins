<?php
// inc/spiplistes_api_courrier.php
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

/*
Fonctions consacrées au traitement du contenu du courrier et tampon :
- filtres, convertisseurs texte, charset, etc.
*/


/*
	function spiplistes_propre($texte)
	passe propre() sur un texte puis nettoye les trucs rajoutes par spip sur du html
	ca s'utilise pour afficher un courrier dans l espace prive
	on l'applique au courrier avant de confirmer l'envoi
*/
function spiplistes_propre($texte){
	$temp_style = ereg("<style[^>]*>[^<]*</style>", $texte, $style_reg);
	if (isset($style_reg[0])) 
		$style_str = $style_reg[0]; 
	else 
		$style_str = "";
	$texte = ereg_replace("<style[^>]*>[^<]*</style>", "__STYLE__", $texte);
	//passer propre si y'a pas de html (balises fermantes)
	if( !preg_match(',</?('._BALISES_BLOCS.')[>[:space:]],iS', $texte) ) 
	$texte = propre($texte); // pb: enleve aussi <style>...  
	$texte = spiplistes_propre_bloog($texte); //nettoyer les spip class truc en trop
	$texte = ereg_replace("__STYLE__", $style_str, $texte);
	//les liens avec double début #URL_SITE_SPIP/#URL_ARTICLE
	$texte = ereg_replace($GLOBALS['meta']['adresse_site']."/".$GLOBALS['meta']['adresse_site'], $GLOBALS['meta']['adresse_site'], $texte);
	$texte = liens_absolus($texte);
	
	return $texte;
}


/****
 * titre : spiplistes_propre_bloog
 * Enleve les enluminures Spip pour la bloogletter
 Vincent CARON
****/

function spiplistes_propre_bloog($texte) {

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
} // end spiplistes_propre_bloog()



/****
 * titre : spiplistes_version_texte
 * d'après Clever Mail (-> NHoizey), mais en mieux.
****/

function spiplistes_version_texte($in) {
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

} // end spiplistes_version_texte()

/*
	donne contenu tampon au format texte (CP-20071013)
	tampon_patron: nom du tampon (fichier, sans extension)
	tampon_html: contenu html converti en texte si pas de contenu
*/
function spiplistes_tampon_texte_get ($tampon_patron, $tampon_html) {
	$contexte_patron = array();
	$result = false;
	foreach(explode(",", _SPIPLISTES_TAMPON_CLES) as $key) {
		$contexte_patron[$key] = __plugin_lire_key_in_serialized_meta($key, _SPIPLISTES_META_PREFERENCES);
	}
	$f = _SPIPLISTES_PATRONS_TAMPON_DIR.$tampon_patron;
	if (find_in_path($f."_texte.html")){
		$result = recuperer_fond($f, $contexte_patron);
	}
	if(!$result) {
		$result = spiplistes_version_texte($tampon_html);
	}
	return($result);
}

/*
 * Ajouter les abonnes d'une liste a un envoi
 * @param : $id_courrier : reference d'un envoi
 * @param $id_liste : reference d'une liste
 */
function spiplistes_remplir_liste_envois ($id_courrier,$id_liste) {
	$id_courrier = intval($id_courrier);
	if(($id_liste = intval($id_liste)) == 0) {
		$result_m = spip_query("SELECT id_auteur FROM spip_auteurs ORDER BY id_auteur ASC");
	} else {
		$result_m = spiplistes_sql_select_simple("id_auteur", "spip_auteurs_listes", "id_liste=$id_liste", false);
	}
	while($row_ = spip_fetch_array($result_m)) {
		$id_abo = $row_['id_auteur'];
		spip_query("INSERT INTO spip_auteurs_courriers (id_auteur,id_courrier,statut,maj) VALUES (".
				   _q($id_abo).","._q($id_courrier).",'a_envoyer', NOW()) ");
	}
	
	$res = spiplistes_sql_select_simple("COUNT(id_auteur) AS n", "spip_auteurs_courriers", "id_courrier=$id_courrier AND statut='a_envoyer'", false);
	
	if ($row = spip_fetch_array($res)) {
		spip_query("UPDATE spip_courriers SET total_abonnes=".
				   _q($row['n'])." WHERE id_courrier="._q($id_courrier));
	}
}


/*
 * Supprime les abonnes d'une liste à un envoi
 */
function spiplistes_supprime_liste_envois ($id_courrier) {
	$result = false;
	$id_courrier = intval($id_courrier);
	if($id_courrier > 0) {
		$result = spip_query("DELETE FROM spip_auteurs_courriers WHERE id_courrier=$id_courrier");
	}
	return($result);
}


?>