<?php
/**
 * Plugin Aspirateur pour Spip 3.0
 * Licence GPL 3
 *
 * (c) 2014 Anne-lise Martenot
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/* Nettoyage du html */
/**
 * 
 * Renvoie un contenu html nettoyé
 * 
 *
 * @param string $contenu
 *	le contenu html
 *
 * @return string 
 * 	le contenu html nettoyé
 *
**/
function clean_contenu($chaine){

   //on exclut les images du chemin donné
   $motif_chemin_documents_exclure = lire_config('aspirateur/motif_chemin_documents_exclure');
   if($motif_chemin_documents_exclure)
   $chaine = preg_replace("#<img\s+[^>]*?src=[^>]*$motif_chemin_documents_exclure*/[^>]*>#Umis",'', $chaine);
		
   //parfois le à n'est pas bon!
   $chaine = preg_replace('#à#Umis','&agrave;', $chaine);
   //<a href="javascript:
   $chaine = preg_replace('#<a href="javascript(?:.*)</a>#Umis','$1',$chaine);
   //on retire tous les commentaires
   $chaine = preg_replace('#<!--.*-->#Umis','$1',$chaine);
   //on retire tous les scripts et les styles
   $chaine = preg_replace('#style="(?:.*)"#Umis','$1',$chaine);
   //on retire style script et formulaire
   $chaine = preg_replace("#<(script|style|form|link)\b.+?<\/\\1>#Umis", "", $chaine);
   //aussi ceux sans fermeture, beurk
   $chaine = preg_replace("#<(script|style|form|link)(?:.*)>#Umis", "", $chaine);
   //on réécrit les <br> en <br />
   $chaine = preg_replace('#<br>#Umis','<br />',$chaine);
   //on retire tous les border='0'
   $chaine = preg_replace('#border=\'0\'#Umis','$1',$chaine);
   //supprimer les  bgcolor
   $chaine = preg_replace('#bgcolor=(?:.*)>#Umis','>',$chaine);
   //supprimer les align="left"
   $chaine = preg_replace('#align=\"left\"#Umis','$1',$chaine);
   //supprimer toutes les class
   $chaine = preg_replace('#class="(?:.*)"#Umis','',$chaine);
   //supprimer tous les id
   $chaine = preg_replace('#id="(?:.*)"#Umis','',$chaine);
   //remplacer les &nbsp;
   $chaine = preg_replace('#&nbsp;#Umis',' ',$chaine);
   //supprimer les espaces inutiles
   $chaine = preg_replace('#[[:blank:]]>#Umis','>',$chaine);
   //supprimer les span,div etc
   $chaine = preg_replace('#<span>|</span>|<div>|</div>#Umis','$1',$chaine);
   //supprimer des liens sans texte #<a href="[^>]*"></a>#
   $chaine = preg_replace('#<a href="[^>]*"></a>#Umis','',$chaine);
   //supprimer les fonts
   $chaine = preg_replace('#<font(?:.*)>(.*)?</font>#Umis','$1',$chaine);
   $chaine = preg_replace('#<font>#Umis','',$chaine);
   $chaine = preg_replace('#</font>#Umis','',$chaine);
   //supprimer les tables
   $chaine = preg_replace('#<table(?:.*)>#Umis','<br />',$chaine);
    $chaine = preg_replace('#<table>#Umis','<br />',$chaine);
   $chaine = preg_replace('#</table>#Umis','<br />',$chaine);
   //supprimer les td
   $chaine = preg_replace('#<td(?:.*)>#Umis','<br />',$chaine);
    $chaine = preg_replace('#<td>#Umis','<br />',$chaine);
   $chaine = preg_replace('#</td>#Umis','<br />',$chaine);
   //supprimer les tr
   $chaine = preg_replace('#<tr(?:.*)>#Umis','<br />',$chaine);
   $chaine = preg_replace('#</tr>#Umis','<br />',$chaine);
   //supprimer les tbody
   $chaine = preg_replace('#<tbody>#Umis','<br />',$chaine);
   $chaine = preg_replace('#</tbody>#Umis','<br />',$chaine);
   //supprimer les espaces en trop
   $chaine = preg_replace('/\s\s+/',' ', $chaine);
   //supprimer les espaces doubles (grr)
   $chaine = preg_replace('#([[:blank:]]){2,}#Umis',' ',$chaine);
   //supprimer les espaces doubles (grr)
   $chaine = preg_replace ('#\s{2,}#Umis',' ', $chaine);
   //reduire les <p >
   $chaine = preg_replace('#<p >#Umis','<p>', $chaine);
   //saut de ligne propre
   $lapage = str_replace("\r", "\n", $lapage);
   //resserrer les tags
   $chaine = preg_replace('#> <#Umis','><', $chaine);
   //supprimer les <br /> inutiles comme <br /></li>
   $chaine = preg_replace('#<br /></li>#Umis', "</li>", $chaine);
   //supprimer les <br /> inutiles comme <li><br />
   $chaine = preg_replace('#<li><br />#Umis', "<li>", $chaine);
   //supprimer les <br /><p>
   $chaine = preg_replace('#<br /><p>#Umis', "<p>", $chaine);
   //supprimer les <br /></p>
   $chaine = preg_replace('#<br /></p>#Umis', "</p>", $chaine);
   //supprimer les <br /> en trop pour conserver l'écart des table/tr/td quand même
   $chaine = preg_replace('#(<br \/> ){1,}+#Umis', "<br />", $chaine);
   $chaine = preg_replace('#(<br \/>){1,}+#Umis', "<br />", $chaine);
   //reduire les <p><p>
   $chaine = preg_replace('#<p><p>(.*)?</p></p>#Umis', "<p>$1</p>", $chaine);  
   //preserver un blanc sur les liens
   $chaine = preg_replace('#><a#','> <a', $chaine);  
   //supprimer les liens vides
   $chaine = preg_replace("`<a[^>]*></a>`",'', $chaine);
   //supprimer les <br /> inutiles comme <br /></li>
   $chaine = preg_replace('#<br /></li>#Umis', "</li>", $chaine);
   //supprimer les <br /> inutiles comme <li><br />
   $chaine = preg_replace('#<li><br />#Umis', "<li>", $chaine);
   //supprimer les <br /><p>
   $chaine = preg_replace('#<br /><p>#Umis', "<p>", $chaine);
   //supprimer les <br /></p>
   $chaine = preg_replace('#<br /></p>#Umis', "</p>", $chaine);
   //supprimer target="rien ou un truc"
   $chaine = preg_replace('#target="(.*)?"#Umis','', $chaine);
   //supprimer les h1 h6 vides
   $chaine = preg_replace(",<(h[1-6])( [^>]*)?"."></\\1>,Uims", "", $chaine);
   return $chaine;
}

/**
 * 
 * Assurer l'utf-8 et des accents propres
 *
**/
function char($chaine){
	return preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $chaine); 
	//return html_entity_decode($chaine, ENT_NOQUOTES, "UTF-8");
}

