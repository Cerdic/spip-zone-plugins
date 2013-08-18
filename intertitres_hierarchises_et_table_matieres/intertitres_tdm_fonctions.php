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
	while(list ($key, $val) = each ($matches[1])){
		$sortie .= "-".$val."\n";
	};
	return $sortie;
}

function extrait_emphaseforte($texte) {
	$texte=preg_replace("/(\{\{\{)(.*?)(\}\}\})/","",$texte);
	preg_match_all("/\{\{(.*?)\}\}/",$texte,$matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	while(list ($key, $val) = each ($matches[1])){
		$sortie .= "«".$val."»; ";
	};
	return $sortie;
}

function extrait_emphase($texte) {
	$texte=preg_replace("/(\{\{)(.*?)(\}\})/","",$texte);
	preg_match_all("/\{(.*?)\}/",$texte,$matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	while(list ($key, $val) = each ($matches[1])){
		$sortie .= "«".$val."»; ";
	};
	return $sortie;
}

function extrait_liens($texte) {
	//protection de ce qui est code
	$texte=preg_replace("/(<code>)(.*?)(<\/code>)/","",$texte);
	//protection des ancres
	$texte=preg_replace("/(\[.*?\<-])/","",$texte);
	$texte=preg_replace("/(\[\[)(.*?)(\]\])/","",$texte);
	preg_match_all("/(\[.*?\])/",$texte,$matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	while(list ($key, $val) = each ($matches[1])){
		$sortie .= $val."\n\n";
	};
	return $sortie;
}

function extrait_un_titre ($texte,$ancre) {
	preg_match ("/<h(\d) class=\"spip\"><a id='a$ancre' name='a$ancre'><\/a>(.*?)<\/h\\1>/",$texte,$matches);
	$titre = textebrut($matches[2]);
	return $titre;
}

function extrait_de_texte ($texte,$debut=0,$taille=20) {
	$mots = explode (" ",textebrut($texte));
	$extrait = implode (" ", array_slice($mots,$debut,$taille));
	return $extrait;
}

function extrait_partie ($texte,$ancre,$debut=0,$taille) {
	preg_match ("/<h(\d) class=\"spip\"><a id='a$ancre' name='a$ancre'><\/a>.*?<\/h\\1>(.*?)<h\\1 class=\"spip\">/s",$texte,$matches);
	$partie = $matches[2];
	if (!$taille) $taille = str_word_count($partie);
	$extrait = extrait_de_texte ($partie,$debut,$taille);
	return $extrait;
}

function nettoie_des_modeles ($texte) {
	//retire les modeles du plugin pour éviter les plantages circulaires
	$texte=preg_replace("/<(extrait|extrait_partie|renvoi|table_des_matieres)(.*?)>/","",$texte);
	//retire les notes du texte, pour éviter les doublons de notes !
	$texte=preg_replace("/(\[\[)(.*?)(\]\])/","",$texte);
	return $texte;
}

function table_des_matieres ($texte,$tdm,$url) {
	return IntertitresTdm_table_des_matieres($texte,$tdm,$url);
}
?>