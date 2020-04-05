<?php
/*
 * Plugin Facteur
 * (c) 2009-2010 Collectif SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/texte');
include_spip('classes/facteur');


/**
 * facteur_addstyle
 *
 * @author Eric Dols
 **/
function facteur_addstyle($matches) {

	// $matches[1]=tag, $matches[2]=tag attributes (if any), $matches[3]=xhtml closing (if any)

	// variables values set in calling function
	global $styledefinition, $styletag, $styleclass;

	// convert the style definition to a one-liner
	$styledefinition = preg_replace ("!\s+!mi", " ", $styledefinition );
	// convert all double-quotes to single-quotes
	$styledefinition = preg_replace ('/"/','\'', $styledefinition );

	if (preg_match ("/style\=/i", $matches[2])) {
			// add styles to existing style attribute if any already in the tag
			$pattern = "!(.* style\=)[\"]([^\"]*)[\"](.*)!mi";
			$replacement = "\$1".'"'."\$2 ".$styledefinition.'"'."\$3";
			$attributes = preg_replace ($pattern, $replacement , $matches[2]);
	} else {
			// otherwise add new style attribute to tag (none was present)
			$attributes = $matches[2].' style="'.$styledefinition.'"';
	}

	if ($styleclass!="") {
		// if we were injecting a class style, remove the now useless class attribute from the html tag

		// Single class in tag case (class="classname"): remove class attribute altogether
		$pattern = "!(.*) class\=['\"]".$styleclass."['\"](.*)!mi";
		$replacement = "\$1\$2";
		$attributes = preg_replace ( $pattern, $replacement, $attributes);

		// Multiple classes in tag case (class="classname anotherclass..."): remove class name from class attribute.
		// classes are injected inline and removed by order of appearance in <head> stylesheet
		// exact same behavior as where last declared class attributes in <style> take over (IE6 tested only)
		$pattern = "!(.* class\=['\"][^\"]*)(".$styleclass." | ".$styleclass.")([^\"]*['\"].*)!mi";
		$replacement = "\$1\$3";
		$attributes = preg_replace ( $pattern, $replacement, $attributes);

	}

	return "<".$matches[1].$attributes.$matches[3].">";
}

function facteur_mail_html2text($html){
	// On remplace tous les sauts de lignes par un espace
	$html = str_replace("\n", ' ', $html);

	// Supprimer tous les liens internes
	$texte = preg_replace("/\<a href=['\"]#(.*?)['\"][^>]*>(.*?)<\/a>/ims", "\\2", $html);

	// Supprime feuille style
	$texte = preg_replace(";<style[^>]*>[^<]*</style>;i", "", $texte);

	// Remplace tous les liens	
	$texte = preg_replace("/\<a[^>]*href=['\"](.*?)['\"][^>]*>(.*?)<\/a>/ims", "\\2 (\\1)", $texte);

	// Les titres
	$texte = preg_replace(";<h1[^>]*>;i", "\n= ", $texte);
	$texte = str_replace("</h1>", " =\n\n", $texte);
	$texte = preg_replace(";<h2[^>]*>;i", "\n== ", $texte);
	$texte = str_replace("</h2>", " ==\n\n", $texte);
	$texte = preg_replace(";<h3[^>]*>;i", "\n=== ", $texte);
	$texte = str_replace("</h3>", " ===\n\n", $texte);

	// Une fin de liste
	$texte = preg_replace(";</(u|o)l>;i", "\n\n", $texte);

	// Une saut de ligne *après* le paragraphe
	$texte = preg_replace(";<p[^>]*>;i", "\n", $texte);
	$texte = preg_replace(";</p>;i", "\n\n", $texte);
	// Les sauts de ligne interne
	$texte = preg_replace(";<br[^>]*>;i", "\n", $texte);

	//$texte = str_replace('<br /><img class=\'spip_puce\' src=\'puce.gif\' alt=\'-\' border=\'0\'>', "\n".'-', $texte);
	$texte = preg_replace (';<li[^>]*>;i', "\n".'- ', $texte);


	// accentuation du gras
	// <b>texte</b> -> **texte**
	$texte = preg_replace (';<b[^>]*>;i','**' ,$texte);
	$texte = str_replace ('</b>','**' ,$texte);

	// accentuation du gras
	// <strong>texte</strong> -> **texte**
	$texte = preg_replace (';<strong[^>]*>;i','**' ,$texte);
	$texte = str_replace ('</strong>','**' ,$texte);


	// accentuation de l'italique
	// <em>texte</em> -> *texte*
	$texte = preg_replace (';<em[^>]*>;i','/' ,$texte);
	$texte = str_replace ('</em>','*' ,$texte);

	// accentuation de l'italique
	// <i>texte</i> -> *texte*
	$texte = preg_replace (';<i[^>]*>;i','/' ,$texte);
	$texte = str_replace ('</i>','*' ,$texte);

	$texte = str_replace('&oelig;', 'oe', $texte);
	$texte = str_replace("&nbsp;", " ", $texte);
	$texte = filtrer_entites($texte);

	// On supprime toutes les balises restantes
	$texte = supprimer_tags($texte);

	$texte = str_replace("\x0B", "", $texte); 
	$texte = str_replace("\t", "", $texte) ;
	$texte = preg_replace(";[ ]{3,};", "", $texte);

	// espace en debut de ligne
	$texte = preg_replace("/(\r\n|\n|\r)[ ]+/", "\n", $texte);

	//marche po
	// Bring down number of empty lines to 4 max
	$texte = preg_replace("/(\r\n|\n|\r){3,}/m", "\n\n", $texte);

	//saut de lignes en debut de texte
	$texte = preg_replace("/^(\r\n|\n|\r)*/", "\n\n", $texte);
	//saut de lignes en debut ou fin de texte
	$texte = preg_replace("/(\r\n|\n|\r)*$/", "\n\n", $texte);

	// Faire des lignes de 75 caracteres maximum
	//$texte = wordwrap($texte);

	return $texte;
}