<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Inc\Facteur_mail_html2text
 */


/**
 * Transformer un mail HTML en mail Texte proprement :
 * - les tableaux de mise en page sont utilisés pour structurer le mail texte
 * - le reste du HTML est markdownifie car c'est un format texte lisible et conventionnel
 *
 * @param string $html
 * @return string
 */
function inc_facteur_mail_html2text_dist($html){
	// nettoyer les balises de mise en page html
	$html = preg_replace(",</(td|th)>,Uims","<br/>",$html);
	$html = preg_replace(",</(table)>,Uims","@@@hr@@@",$html);
	$html = preg_replace(",</?(html|body|table|td|th|tbody|thead|center|article|section|span)[^>]*>,Uims","\n\n",$html);

	// commentaires html et conditionnels
	$html = preg_replace(",<!--.*-->,Uims","\n",$html);
	$html = preg_replace(",<!\[.*\]>,Uims","\n",$html);

	$html = preg_replace(",<(/?)(div|tr|caption)([^>]*>),Uims","<\\1p>",$html);
	$html = preg_replace(",(<p>\s*)+,ims","<p>",$html);
	$html = preg_replace(",<br/?>\s*</p>,ims","</p>",$html);
	$html = preg_replace(",</p>\s*<br/?>,ims","</p>",$html);
	$html = preg_replace(",(</p>\s*(@@@hr@@@)?\s*)+,ims","</p>\\2",$html);
	$html = preg_replace(",(<p>\s*</p>),ims","",$html);

	// succession @@@hr@@@<hr> et <hr>@@@hr@@@
	$html = preg_replace(",@@@hr@@@\s*(<[^>]*>\s*)?<hr[^>]*>,ims","@@@hr@@@\n",$html);
	$html = preg_replace(",<hr[^>]*>\s*(<[^>]*>\s*)?@@@hr@@@,ims","\n@@@hr@@@",$html);

	$html = preg_replace(",<textarea[^>]*spip_cadre[^>]*>(.*)</textarea>,Uims","<code>\n\\1\n</code>",$html);

	// vider le contenu de qqunes :
	$html = preg_replace(",<head[^>]*>.*</head>,Uims","\n",$html);

	// Liens :
	// Nettoyage des liens des notes de bas de page
	$html = preg_replace("@<a href=\"#n(b|h)[0-9]+-[0-9]+\" name=\"n(b|h)[0-9]+-[0-9]+\" class=\"spip_note\">([0-9]+)</a>@", "\\3", $html);
	// Supprimer tous les liens internes
	$html = preg_replace("/\<a href=['\"]#(.*?)['\"][^>]*>(.*?)<\/a>/ims","\\2", $html);
	// Remplace tous les liens
	preg_match_all("/\<a href=['\"](.*?)['\"][^>]*>(.*?)<\/a>/ims", $html,$matches,PREG_SET_ORDER);
	$prelinks = $postlinks = array();
	if (!function_exists('url_absolue'))
		include_spip('inc/filtres');
	foreach ($matches as $k => $match){
		$link = "@@@link$k@@@";
		$url = str_replace("&amp;","&",$match[1]);
		if ($match[2]==$match[1] OR $match[2]==$url){
			// si le texte est l'url :
			$prelinks[$match[0]] = "$link";
		}
		else {
			// texte + url
			$prelinks[$match[0]] = $match[2] . " ($link)";
		}
		// passer l'url en absolu dans le texte sinon elle n'est pas clicable ni utilisable
		$postlinks[$link] = url_absolue($url);
	}
	$html = str_replace(array_keys($prelinks), array_values($prelinks),$html);

	// les images par leur alt ?
	// au moins les puces
	$html = preg_replace(',<img\s[^>]*alt="-"[^>]*>,Uims','-',$html);
	// les autres
	$html = preg_replace(',<img\s[^>]*alt=[\'"]([^\'"]*)[\'"][^>]*>,Uims',"\\1",$html);
	// on vire celles sans alt
	$html = preg_replace(",</?(img)[^>]*>,Uims","\n",$html);

	// espaces
	$html = str_replace("&nbsp;"," ",$html);
	$html = preg_replace(",<p>\s+,ims","<p>",$html);

	#return $html;
	include_spip("lib/markdownify/markdownify");
	$parser = new Markdownify('inline',false,false);
	$texte = $parser->parseString($html);

	$texte = str_replace(array_keys($postlinks), array_values($postlinks),$texte);


	// trim et sauts de ligne en trop ou pas assez
	$texte = trim($texte);
	$texte = str_replace("<br />\n","\n",$texte);
	$texte = preg_replace(",(@@@hr@@@\s*)+\Z,ims","",$texte);
	$texte = preg_replace(",(@@@hr@@@\s*\n)+,ims","\n\n\n".str_pad("-",75,"-")."\n\n\n",$texte);
	$texte = preg_replace(",(\n#+\s),ims","\n\n\\1",$texte);
	$texte = preg_replace(",(\n\s*)(\n\s*)+(\n)+,ims","\n\n\n",$texte);


	// <p> et </p> restants
	$texte = str_replace(array("<p>","</p>"),array("",""),$texte);

	// entites restantes ? (dans du code...)
	include_spip('inc/charsets');
	$texte = unicode2charset($texte);
	$texte = str_replace(array('&#039;', '&#034;'),array("'",'"'), $texte);


	// Faire des lignes de 75 caracteres maximum
	return trim(wordwrap($texte));
}
