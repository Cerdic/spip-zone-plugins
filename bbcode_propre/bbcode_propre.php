<?php

/*
 *   +----------------------------------+
 *    Nom du Filtre :    BBcode                                               
 *   +----------------------------------+
 *    Date : mardi 27 d	cmbre 2005
 *    Auteur : FLORENT POINSAUT (flo.saut@wanadoo.fr)                                      
 *    Source : LAURENT STEPHANE (sl@adequates.com)
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     Rendre utilisable le BBcode dans la r	daction d'article
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.spip-contrib.net/Du-BBcode-dans-SPIP
*/

function bbcode($chaine) {
$chaine = stripslashes($chaine);
$chaine = str_replace("[code]","<code>",$chaine);	
$chaine = str_replace("[/code]","</code>",$chaine);
$chaine = eregi_replace("\\[url]([^\\[]*)\\[/url\\]","<a href=\"\\1\" title=\"\\1\">\\1</a>",$chaine);
$chaine = eregi_replace("\\[url=([^\\[]*)\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" title=\"\\2\">\\2</a>",$chaine);
$chaine = eregi_replace("\\[email\\]([^\\[]*)\\[/email\\]","<a href=mailto:\"\\1\">\\1</a>",$chaine);
$chaine = eregi_replace("\\[email=([^\\[]*)\\]([^\\[]*)\\[/email\\]","<a href=mailto:\"\\1\">\\2</a>",$chaine);
$chaine = eregi_replace("\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]","<span style=\"color:\\1\">\\2</span>",$chaine);
$chaine = eregi_replace("\\[size=([^\\[]*)\\]([^\\[]*)\\[/size\\]","<span style=\"font-size:\\1px\">\\2</span>",$chaine);
$chaine = preg_replace("!\[list\](.+)\[/list\]!Umi","<ul> $1 </ul>",$chaine);
$chaine = preg_replace("!\[\*\](.+)(?=(\[\*\]|</ul>))!Umi","<li>$1</li>",$chaine);
$chaine = str_replace("[b]","<b>",$chaine);
$chaine = str_replace("[/b]","</b>",$chaine);
$chaine = str_replace("[i]","<i>",$chaine);
$chaine = str_replace("[/i]","</i>",$chaine);
$chaine = str_replace("[center]","<center>",$chaine);
$chaine = str_replace("[/center]","</center>",$chaine);
$chaine = str_replace("[img]","<img src=\"",$chaine);	
$chaine = str_replace("[/img]","\" alt=\"img\" />",$chaine);	
$chaine = str_replace("[quote]","<quote>",$chaine);	
$chaine = str_replace("[/quote]","</quote>",$chaine);
return $chaine;
}

// FIN du Filtre BBcode

function bbcode_propre_pre_propre($texte) {
  if($texte) {
   return bbcode($texte);
  }
}

?>
