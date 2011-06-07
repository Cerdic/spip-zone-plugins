<?php
function dida_insert_projet($texte)
{
@$urldusite=lire_config('didaspip/url');
  if (!isset($urldusite)) $urldusite=dirname($_SERVER["PHP_SELF"]);
  
@$width=lire_config('didaspip/didawidth');
	if (!isset($width)) $width="800";
	
@$height=lire_config('didaspip/didaheight');
	if (!isset($height)) $width="600";
	
@$type_affichage=lire_config('didaspip/box');

	if (!isset($type_affichage)) $type_affichage="Iframe";
	
if($type_affichage=="Thickbox"){
 while (eregi("didapages@([0-9a-zA-Z]+)@", $texte, $projetdida))
{ 
//on recherche le nom du projet que l'on stocke dans la variable projetdida
 
 $motif="didapages@".$projetdida[1]."@"; // On recherche l'emplacement du lien
 $texte = str_replace($motif,
  '<a class="thickbox" title="'.$projetdida[1].'" href="'._DIR_IMG.'didapages/'.$projetdida[1].'/index.html?keepThis=true&amp;TB_iframe=true&amp;height='.$height.'&amp;width='.$width.'"><img src="'._DIR_PLUGIN_DIDA.'dida_ico.gif" alt="'.$projetdida[1].'"></a>',
  $texte); //On remplace dans le texte le motif par le lien vers le projet
 
  }
   return $texte;
}
else{

if($type_affichage=="Iframe"){
while (eregi ("didapages@([0-9a-zA-Z]+)@", $texte, $projetdida))
 //on recherche le nom du projet que l'on stocke dans la variable projetdida
 {
 $motif="didapages@".$projetdida[1]."@"; // On recherche l'emplacement du lien
 
 $texte = str_replace($motif,
  '<iframe src="'._DIR_IMG.'didapages/'.$projetdida[1].'/index.html" width="'.$width.'" height="'.$height.'" scrolling="no" frameborder="0" align="center"></iframe></p>
<p class="spip"><a href="'._DIR_IMG.'didapages/'.$projetdida[1].'/index.html" target="_blank"><strong>Afficher en plein &eacute;cran</strong></a></p>',
  $texte);
}
return $texte;
}
else{
while (eregi ("didapages@([0-9a-zA-Z]+)@", $texte, $projetdida))
 //on recherche le nom du projet que l'on stocke dans la variable projetdida
 {
 $motif="didapages@".$projetdida[1]."@"; // On recherche l'emplacement du lien
 
 $texte = str_replace($motif,
  '<p class="spip"><a href="'._DIR_IMG.'didapages/'.$projetdida[1].'/index.html" target="_blank">
<img src="'._DIR_PLUGIN_DIDA.'dida_ico.gif" alt="'.$projetdida[1].'"></a></p>',
  $texte);
 } 
  return $texte;
}
}

}


?>