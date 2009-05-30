<?php

/*
  * Ce plugin rajoute des raccourcis typographique et ameliore les possibilites de la barre typographique pour les redacteurs
*/

	/*
	 *    Fonctions de ces filtres :
	 *     Ils rajoutent quelques racourcis typo a SPIP
	 *
	 *     Syntaxe des raccourcis :
	 *           [/texte/] : aligner le texte a droite
	 *           [|texte|] : centrer le texte
	 *           [(texte)] : encadrer le texte (occupe toute la largeur de la page, a mettre autour d'un paragraphe)
	 *           [*texte*] : encadrer/surligner le texte (une partie a l'interieur d'un paragraphe)
	 *           [**texte*] : variante encadrer/surligner le texte (une partie a l'interieur d'un paragraphe)
	 *           <sup>texte</sup> : mettre en exposant le texte selectionne
	 *
	 *     Styles pour les encadrements a rajouter dans votre feuille de style :
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
	// remplace les fausses listes a puce par de vraies
	// (recherche en debut de lignes - suivi d'un ou plusieurs caracteres blancs, en mode multiligne)
	// Mettre $GLOBALS['barre_typo_preserve_puces'] = true; dans mes_options.php pour ne pas avoir ce comportement
	if (!isset($GLOBALS['barre_typo_preserve_puces']))
		$texte =  preg_replace('/^-\s+/m','-* ',$texte);

	// tous les elements block doivent etre introduits ici
	// pour etre pris en charge par paragrapher

	// Definition des differents intertitres possibles, si pas deja definies
	global $debut_intertitre_1, $fin_intertitre_1;
	global $debut_intertitre_2, $fin_intertitre_2;
	global $debut_intertitre_3, $fin_intertitre_3;
	global $debut_intertitre_4, $fin_intertitre_4;
	global $debut_intertitre_5, $fin_intertitre_5;
	global $debut_intertitre_6, $fin_intertitre_6;

	$chercher_raccourcis = array(
		/* 1 */ 	"/(^|[^{])\{1\{/S",
		/* 2 */ 	"/\}1\}($|[^}])/S",
		/* 3 */ 	"/(^|[^{])\{2\{/S",
		/* 4 */ 	"/\}2\}($|[^}])/S",
		/* 5 */ 	"/(^|[^{])\{3\{/S",
		/* 6 */ 	"/\}3\}($|[^}])/S",
		/* 7 */ 	"/(^|[^{])\{4\{/S",
		/* 8 */ 	"/\}4\}($|[^}])/S",
		/* 9 */ 	"/(^|[^{])\{5\{/S",
		/* 10 */ 	"/\}5\}($|[^}])/S",
		/* 11 */ 	"/(^|[^{])\{6\{/S",
		/* 12 */ 	"/\}6\}($|[^}])/S",
		/* 13 */ 	"/\{(§|Â§)\{/S", # Pour gerer l'unicode aussi !
		/* 14 */ 	"/\}(§|Â§)\}/S",
		/* 15 */ 	"/<-->/S",
		/* 16 */ 	"/-->/S",
		/* 17 */ 	"/<--/S"
	);

	$remplacer_raccourcis = array(
		/* 1 */ 	"\$1\n\n$debut_intertitre_1",
		/* 2 */ 	"$fin_intertitre_1\n\n\$1",
		/* 3 */ 	"\$1\n\n$debut_intertitre_2",
		/* 4 */ 	"$fin_intertitre_2\n\n\$1",
		/* 5 */ 	"\$1\n\n$debut_intertitre_3",
		/* 6 */ 	"$fin_intertitre_3\n\n\$1",
		/* 7 */ 	"\$1\n\n$debut_intertitre_4",
		/* 8 */ 	"$fin_intertitre_4\n\n\$1",
		/* 9 */ 	"\$1\n\n$debut_intertitre_5",
		/* 10 */ 	"$fin_intertitre_5\n\n\$1",
		/* 11 */ 	"\$1\n\n$debut_intertitre_6",
		/* 12 */ 	"$fin_intertitre_6\n\n\$1",
		/* 13 */ 	"<span style=\"font-variant: small-caps\">",
		/* 14 */ 	"</span>",
		/* 15 */ 	"&harr;",
		/* 16 */ 	"&rarr;",
		/* 17 */ 	"&larr;"
	);

	$texte = preg_replace($chercher_raccourcis, $remplacer_raccourcis, $texte);

	// remplace les fausses listes a puce par de vraies
	// (recherche en debut de lignes - suivi d'un ou plusieurs caracteres blancs, en mode multiligne)
	// $texte =  preg_replace('/^-\s+/m','-* ',$texte); # deja fait dans post_propre

	return $texte;
}

function BarreTypoEnrichie_post_propre($texte) {

	# Le remplacement des intertitres de premier niveau a deja ete effectue dans inc/texte.php

		$cherche1 = array(
			/* 15 */ 	",\[/(.*)/\],Ums",
			/* 17 */ 	",\[\|(.*)\|\],Ums",
			/* 19 */ 	",\[\((.*)\)\],Ums",
			/* 21 */ 	"/\[\*\*/S",
			/* 21b */ 	"/\[\*/S",
			/* 22 */	"/\*\]/S",
			/* 23 */ 	"/\[\^/S",
			/* 24 */	"/\^\]/S",
			/* 40 */	"/\[([^|][^][]*)\|([^][]*)\]/S",
			/* 41 */	"/<a href=([^>]*)>([^|<]*)\|([^<]*)<\/a>/S"

		);
		$remplace1 = array(
			/* 15 */ 	"<div style=\"text-align:right;\">$1</div>",
			/* 17 */ 	"<div style=\"text-align:center;\">$1</div>",
			/* 19 */ 	"<div class=\"texteencadre-spip\">$1</div>",
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
		$texte = paragrapher($texte,$GLOBALS['toujours_paragrapher']); // il faut reparagrapher a cause des raccourcis typo que l'on a ajoute (block div)
	return $texte;
}


function BarreTypoEnrichie_post_typo($texte) {
	// Correction des & en &amp;
	$texte = preg_replace('/&([A-Za-z#0-9]*);/','@@@amp:\1:amp@@@',$texte); // echapement des entites html deja presentes
	$texte = str_replace('&','&amp;',$texte);
	$texte = preg_replace('/@@@amp:([A-Za-z#0-9]*):amp@@@/','&\1;',$texte);
	// Raccourci typographique <sc></sc>
	$texte = str_replace("<sc>",
		"<span style=\"font-variant: small-caps\">", $texte);
	$texte = str_replace("</sc>", "</span>", $texte);
	return $texte;
}

function BarreTypoEnrichie_nettoyer_raccourcis_typo($texte){
	$texte = preg_replace(',{[1-6]{,','',$texte);
	$texte = preg_replace(',}[1-6]},','',$texte);
	return $texte;
}

function BarreTypoEnrichie_header_prive($texte) {
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	$texte.= '<link rel="stylesheet" type="text/css" href="' . (_DIR_PLUGINS.end($p)) . '/css/bartypenr.css" />' . "\n";
	return $texte;
}

?>