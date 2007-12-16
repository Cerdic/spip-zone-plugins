<?php
/*
 *   +----------------------------------+
 *    Nom du Filtre :    extrait_titres,extrait_emphaseforte...
 *   +----------------------------------+
 *    Date : 19 décembre 2006
 *    Auteur :  Bertrand Marne (extraction à sciencesnat point org)
 *   +-------------------------------------+
 *    Fonctions de ces filtres :
 *   Ces filtres extraient des infos des articles comme:
 *   Les titres de parties, les mots en emphase ou les URL
 *   Il sert à faire ressortir les éléments sémantiques (taggés
 *   par les raccourcis Spip, donc s'utilise avec #TEXTE*)
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
*/
function extrait_titres($texte) {
 preg_match_all("/\{\{\{(.*?)\}\}\}/",$texte,$matches);
 $key = key($matches[1]);
 $val = current($matches[1]);
 while(list ($key, $val) = each ($matches[1]))
	{
	$sortie .= "-".$val."\n";
 };
 return $sortie;
}
function extrait_emphaseforte($texte) {
 $texte=preg_replace("/(\{\{\{)(.*?)(\}\}\})/","",$texte);
 preg_match_all("/\{\{(.*?)\}\}/",$texte,$matches);
 $key = key($matches[1]);
 $val = current($matches[1]);
 while(list ($key, $val) = each ($matches[1]))
	{
	$sortie .= "«".$val."»; ";
 };
 return $sortie;
}

function extrait_emphase($texte) {
 $texte=preg_replace("/(\{\{)(.*?)(\}\})/","",$texte);
 preg_match_all("/\{(.*?)\}/",$texte,$matches);
 $key = key($matches[1]);
 $val = current($matches[1]);
 while(list ($key, $val) = each ($matches[1]))
	{
	$sortie .= "«".$val."»; ";
 };
 return $sortie;
}
function extrait_liens($texte) {
 $texte=preg_replace("/(\[\[)(.*?)(\]\])/","",$texte);
 preg_match_all("/(\[.*?\])/",$texte,$matches);
 $key = key($matches[1]);
 $val = current($matches[1]);
 while(list ($key, $val) = each ($matches[1]))
	{
	$sortie .= $val."\n\n";
 };
 return $sortie;
}

function table_des_matieres ($texte,$tdm) {
 return IntertitresTdm_table_des_matieres($texte,$tdm);

}
?> 
