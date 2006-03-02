<?php

/* Code modifié à partir de:
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
  $chaine = str_replace("[code]","<html><code>",$chaine);	
  $chaine = str_replace("[/code]","</code></html>",$chaine);
  $chaine = eregi_replace("\\[url]([^\\[]*)\\[/url\\]","<html><a href=\"\\1\" title=\"\\1\">\\1</a></html>",$chaine);
  $chaine = eregi_replace("\\[url=([^\\[]*)\\]([^\\[]*)\\[/url\\]","<html><a href=\"\\1\" title=\"\\2\">\\2</a></html>",$chaine);
  $chaine = eregi_replace("\\[email\\]([^\\[]*)\\[/email\\]","<html><a href=mailto:\"\\1\">\\1</a></html>",$chaine);
  $chaine = eregi_replace("\\[email=([^\\[]*)\\]([^\\[]*)\\[/email\\]","<html><a href=mailto:\"\\1\">\\2</a></html>",$chaine);
  $chaine = eregi_replace("\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]","<html><span style=\"color:\\1\">\\2</span></html>",$chaine);
  $chaine = eregi_replace("\\[size=([^\\[]*)\\]([^\\[]*)\\[/size\\]","<html><span style=\"font-size:\\1px\">\\2</span></html>",$chaine);
  $chaine = preg_replace("!\[list\](.+)\[/list\]!Umi","<html><ul> $1 </ul></html>",$chaine);
  $chaine = preg_replace("!\[list=1\](.+)\[/list\]!Umi","<html><ol> $1 </ol></html>",$chaine);  
  $chaine = preg_replace("!\[list=a\](.+)\[/list\]!Umi","<html><ol type='a'> $1 </ol></html>",$chaine);
  $chaine = preg_replace("!\[\*\](.+)(?=(\[\*\]|</ul>))!Umi","<li>$1</li>",$chaine);
  $chaine = str_replace("[b]","<html><b>",$chaine);
  $chaine = str_replace("[/b]","</b></html>",$chaine);
  $chaine = str_replace("[i]","<html><i>",$chaine);
  $chaine = str_replace("[/i]","</i></html>",$chaine);
  $chaine = str_replace("[u]","<html><span style='text-decoration:underline;'>",$chaine);
  $chaine = str_replace("[/u]","</span></html>",$chaine);
  $chaine = str_replace("[center]","<html><center>",$chaine);
  $chaine = str_replace("[/center]","</center></html>",$chaine);
  $chaine = str_replace("[img]","<html><img src=\"",$chaine);	
  $chaine = str_replace("[/img]","\" alt=\"img\" /></html>",$chaine);	
  $chaine = str_replace("[quote]","<quote>",$chaine);	
  $chaine = str_replace("[/quote]","</quote>",$chaine);
  $chaine = str_replace("[scroll]","<cadre>",$chaine);	
  $chaine = str_replace("[/scroll]","</cadre>",$chaine);
  return echappe_html($chaine);
}

// FIN du Filtre BBcode

function bbcode_propre_pre_propre($texte) {
  if($texte) {
   return bbcode($texte);
  }
}

?>
