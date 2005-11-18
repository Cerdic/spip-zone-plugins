<?php

//
// aucun moyen de trouver comment indiquer a un filtre
// donc on applique les deux...
//
function href_lang($texte, $balise='texte') {
	//le premier passage transforme le raccourci en lien
	$texte = appliquer_hreflang($texte);
	//le deuxieme passage retire le raccourci residuel (cas de l'intro)
	$texte = retirer_hreflang($texte);
	return $texte;
}

/***
* appliquer hreflang sur un lien
* cf. : http://www.la-grange.net/w3c/html4.01/struct/links.html#h-12.1.5
*     & http://www.la-grange.net/w3c/html4.01/struct/links.html#adef-hreflang
* prérequis: extention pcre
* remplace <a href="bla">truc|code-de-langue</a> par <a href="bla" hreflang="code-de-langue">truc</a>
* permet la notation spip [truc|code-de-langue->bla] pour les liens dans le #TEXTE
* à appliquer à un #TEXTE => [(#TEXTE|appliquer_hreflang)]
***/

function appliquer_hreflang($texte) {
	$regexp = "|<a([^>]+)>([^<]+)(\|(([a-z]+)(-([a-z]+))?))+</a>|i";
	$replace = "<a\\1 hreflang=\"\\4\">\\2</a>";
	return preg_replace($regexp, $replace, $texte);
}

/* exemple de definition css (dotclear V1.2):
a[hreflang]:after {
	content: "\0000a0(" attr(hreflang) ")";
	color : #666;
	background : transparent;
} */

/* autre exemple (pompage.net)
a[hreflang|="en"]:after {
	content: "\0000a0"url(/pompage_v3/linken.gif);
}

a[hreflang] {
	content: normal !important; #  Hack pour Opera, qui ne comprend pas la règle précédente
} */

/***
* retirer hreflang sur un lien tronqué dans une INTRODUCTION
* remplace truc|code-langue par truc
* à appliquer à un #INTRODUCTION => [(#INTRODUCTION|retirer_hreflang)]
***/

function retirer_hreflang($introduction) {
	$regexp = "|\|(([a-z]+)(-([a-z]+))?)|i";
	$replace = "";
	return preg_replace($regexp, $replace, $introduction);
}

?>
