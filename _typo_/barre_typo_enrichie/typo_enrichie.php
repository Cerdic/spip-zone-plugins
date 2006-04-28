<?php

/*
  * Ce plugin rajoute des raccourcis typographique et améliore les possibilités de la barre typographique pour les rédacteurs
*/

	/*
	 *    Fonctions de ces filtres :
	 *     Ils rajoutent quelques racourcis typo à SPIP
	 *
	 *     Syntaxe des raccourcis :
	 *           [/texte/] : aligner le texte à droite
	 *           [|texte|] : centrer le texte
	 *           [(texte)] : encadrer le texte (occupe toute la largeur de la page, à mettre autour d'un paragraphe)
	 *           [*texte*] : encadrer/surligner le texte (une partie à l'intérieur d'un paragraphe)
	 *           [**texte*] : variante encadrer/surligner le texte (une partie à l'intérieur d'un paragraphe)
	 *           <sup>texte</sup> : mettre en exposant le texte sélectionné
	 *
	 *     Styles pour les encadrements à rajouter dans votre feuille de style :
	 *            .texteencadre-spip {
	 *           	background: #FFE;
	 *           	border-bottom: 2px solid #999999;
	 *           	border-left: 1px solid #EEEEEE;
	 *           	border-right: 2px solid #999999;
	 *           	border-top: 1px solid #EEEEEE;
	 *           	padding: .25em;
	 *           }
	 *           .caractencadre-spip {
	 *           	border: 1px solid #666;
	 *           	padding: 0px .5em 0px .5em;
	 *           }
	 *
	*/

function BarreTypoEnrichie_pre_propre($texte) {
	// tous les elements block doivent etre introduits ici
	// pour etre pris en charge par paragrapher

	// Definition des différents intertitres possibles, si pas deja definies
	tester_variable('debut_intertitre', '<h2>');
	tester_variable('fin_intertitre', '</h2>');
	tester_variable('debut_intertitre_2', '<h3>');
	tester_variable('fin_intertitre_2', '</h3>');
	tester_variable('debut_intertitre_3', '<h4>');
	tester_variable('fin_intertitre_3', '</h4>');
	tester_variable('debut_intertitre_4', '<h5>');
	tester_variable('fin_intertitre_4', '</h5>');
	tester_variable('debut_intertitre_5', '<h6>');
	tester_variable('fin_intertitre_5', '</h6>');

	global $debut_intertitre_2, $fin_intertitre_2;
	global $debut_intertitre_3, $fin_intertitre_3;
	global $debut_intertitre_4, $fin_intertitre_4;
	global $debut_intertitre_5, $fin_intertitre_5;

	$chercher_raccourcis = array(
		/*  3 */ "/\{1\{/",
		/*  4 */ "/\}1\}/",
		/*  5 */ "/\{2\{/",
		/*  6 */ "/\}2\}/",
		/*  7 */ "/\{3\{/",
		/*  8 */ "/\}3\}/",
		/*  9 */ "/\{4\{/",
		/*  10 */ "/\}4\}/",
		/*  9b */ "/\{5\{/",
		/*  10b */ "/\}5\}/",
		/* 11 */ "/\{(§|Â§)\{/", # Â§ Pour gérer l'unicode aussi !
		/* 12 */ "/\}(§|Â§)\}/",
		/* 13 */ "/<-->/",
		/* 14 */ "/-->/",
		/* 15 */ "/<--/"
	);

	$remplacer_raccourcis = array(
		/*  3 */ $debut_intertitre,
		/*  4 */ $fin_intertitre,
		/*  5 */ $debut_intertitre_2,
		/*  6 */ $fin_intertitre_2,
		/*  7 */ $debut_intertitre_3,
		/*  8 */ $fin_intertitre_3,
		/*  9 */ $debut_intertitre_4,
		/*  10 */ $fin_intertitre_4,
		/*  9b */ $debut_intertitre_5,
		/*  10b */ $fin_intertitre_5,
		/* 11 */ "<span style=\"font-variant: small-caps\">",
		/* 12 */ "</span>",
		/* 13 */ "&harr;",
		/* 14 */ "&rarr;",
		/* 15 */ "&larr;"
	);

	$texte = preg_replace($chercher_raccourcis, $remplacer_raccourcis, $texte);

	// remplace les fausses listes à puce par de vraies
	// (recherche en début de lignes - suivi d'un ou plusieurs caractères blancs, en mode multiligne)
	$texte =  preg_replace('/^-\s+/m','-* ',$texte);

	return $texte;
}

function BarreTypoEnrichie_post_propre($texte) {

	// remplace les fausses listes à puce par de vraies
	// (recherche en début de lignes - suivi d'un ou plusieurs caractères blancs, en mode multiligne)
	if (!isset($GLOBALS['barre_typo_preserve_puces']))
		$texte =  preg_replace('/^-\s+/m','-* ',$texte);

	# Le remplacement des intertitres de premier niveau a déjà été effectué dans inc_texte.php

	# Intertitre de deuxième niveau
	/*global $debut_intertitre_2, $fin_intertitre_2;
	$texte = ereg_replace('(<p class="spip">)?[[:space:]]*@@SPIP_debut_intertitre_2@@', $debut_intertitre_2, $texte);
	$texte = ereg_replace('@@SPIP_fin_intertitre_2@@[[:space:]]*(</p>)?', $fin_intertitre_2, $texte);*/

	# Intertitre de troisième niveau
	/*global $debut_intertitre_3, $fin_intertitre_3;
	$texte = ereg_replace('(<p class="spip">)?[[:space:]]*@@SPIP_debut_intertitre_3@@', $debut_intertitre_3, $texte);
	$texte = ereg_replace('@@SPIP_fin_intertitre_3@@[[:space:]]*(</p>)?', $fin_intertitre_3, $texte);*/

	# Intertitre de quatrième niveau
	/*global $debut_intertitre_4, $fin_intertitre_4;
	$texte = ereg_replace('(<p class="spip">)?[[:space:]]*@@SPIP_debut_intertitre_4@@', $debut_intertitre_4, $texte);
	$texte = ereg_replace('@@SPIP_fin_intertitre_4@@[[:space:]]*(</p>)?', $fin_intertitre_4, $texte);*/

	# Intertitre de cinquième niveau
	/*global $debut_intertitre_5, $fin_intertitre_5;
	$texte = ereg_replace('(<p class="spip">)?[[:space:]]*@@SPIP_debut_intertitre_5@@', $debut_intertitre_5, $texte);
	$texte = ereg_replace('@@SPIP_fin_intertitre_5@@[[:space:]]*(</p>)?', $fin_intertitre_5, $texte);*/

		$cherche1 = array(
		/* 15 */ 	"/\[\//",
		/* 16 */	"/\/\]/",
		/* 17 */ 	"/\[\|/",
		/* 18 */	"/\|\]/",
		/* 19 */ 	"/\[\(/",
		/* 20 */	"/\)\]/",
			/* 21 */ 	"/\[\*\*/",
			/* 21b */ 	"/\[\*/",
			/* 22 */	"/\*\]/",
			/* 23 */ 	"/\[\^/",
			/* 24 */	"/\^\]/",
			/* 25 */	#"/<p class=\"spip\"><ul class=\"spip\">/",
			/* 26 */	#"/<\/ul>( *)<\/p>/",
			/* 27 */	#"/<p class=\"spip\"><ol class=\"spip\">/",
			/* 28 */	#"/<\/ol>( *)<\/p>/",
			/* 29 */	#"/<p class=\"spip\"><table class=\"spip\">/",
			/* 30 */	#"/<\/table>( *)<\/p>/",
			/* 31 */	#"/<p class=\"spip\">(\n| *)<div/",
			/* 32 */	#"/<\/div>( *)<\/p>/",
			/* 33 */	#"/<p class=\"spip\"><h([0-9])>/",
			/* 34 */	#"/<\/h([0-9])>( *)<\/p>/",
			/* 35 */	#"/<table cellpadding=5 cellspacing=0 border=0 align=''> <tr><td align='center'> <div class='spip_documents'>/",
			/* 36 */	#"/<\/div> <\/td><\/tr> <\/table>/",
			/* 37 */	#"/<p class=\"spip\"><div/",
			/* 38 */	#"/<p class=\"spip\"><blockquote class=\"spip\">/",
			/* 39 */	#"/<\/blockquote>( *)<\/p>/",
			/* 40 */	"/\[([^|][^][]*)\|([^][]*)\]/",
			/* 41 */	"/<a href=([^>]*)>([^|<]*)\|([^<]*)<\/a>/"

		);
		$remplace1 = array(
		/* 15 */ 	"<div style=\"text-align:right;\">",
		/* 16 */	"</div>",
		/* 17 */ 	"<div style=\"text-align:center;\">",
		/* 18 */	"</div>",
		/* 19 */ 	"<div class=\"texteencadre-spip\">",
		/* 20 */	"</div>",
			/* 21 */ 	"<strong class=\"caractencadre2-spip\">",
			/* 21b */ 	"<strong class=\"caractencadre-spip\">",
			/* 22 */	"</strong>",
			/* 23 */ 	"<sup>",
			/* 24 */	"</sup>",
			/* 25 */	#"<ul class=\"spip\">",
			/* 26 */	#"</ul>",
			/* 27 */	#"<ol class=\"spip\">",
			/* 28 */	#"</ol>",
			/* 29 */	#"<table class=\"spip\">",
			/* 30 */	#"</table>",
			/* 31 */	#"<div",
			/* 32 */	#"</div>",
			/* 33 */	#"<h$1>",
			/* 34 */	#"</h$1>",
			/* 35 */	#"<div class='spip_documents'>",
			/* 36 */	#"</div>",
			/* 37 */	#"<div",
			/* 38 */	#"<blockquote class=\"spip\"><p class=\"spip\">",
			/* 39 */	#"</p></blockquote>",
			/* 40 */	"<acronym title='$2' class='spip_acronym'>$1</acronym>",
			/* 41 */	"<a href=$1 title=\"$3\">$2</a>"

		);
		$texte = preg_replace($cherche1, $remplace1, $texte);
		$texte = paragrapher($texte); // il faut reparagrapher a cause des raccourcis typo que l'on a ajoute (block div)
	return $texte;
}


function BarreTypoEnrichie_post_typo($texte) {
	// Correction des & en &amp;
	$texte = preg_replace('/&([A-Za-z#0-9]*);/','@@@amp:\1:amp@@@',$texte); // échapement des entités html déjà présentes
	$texte = str_replace('&','&amp;',$texte);
	$texte = preg_replace('/@@@amp:([A-Za-z#0-9]*):amp@@@/','&\1;',$texte);
	// Raccourci typographique <sc></sc>
	$texte = str_replace("<sc>",
		"<span style=\"font-variant: small-caps\">", $texte);
	$texte = str_replace("</sc>", "</span>", $texte);
	return $texte;
}
function BarreTypoEnrichie_header_prive($texte) {
	$texte.= '<link rel="stylesheet" type="text/css" href="' . (_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))) . '/css/bartypenr.css" />' . "\n";
	return $texte;
}

?>