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
	// remplace les fausses listes à puce par de vraies
	// (recherche en début de lignes - suivi d'un ou plusieurs caractères blancs, en mode multiligne)
	// Mettre $GLOBALS['barre_typo_preserve_puces'] = true; dans mes_options.php pour ne pas avoir ce comportement
	if (!isset($GLOBALS['barre_typo_preserve_puces']))
		$texte =  preg_replace('/^-\s+/m','-* ',$texte);

	// tous les elements block doivent etre introduits ici
	// pour etre pris en charge par paragrapher

	// Definition des différents intertitres possibles, si pas deja definies
	tester_variable('debut_intertitre', '<h2 class="spip">');
	tester_variable('fin_intertitre', '</h2>');
	tester_variable('debut_intertitre_2', '<h3 class="spip">');
	tester_variable('fin_intertitre_2', '</h3>');
	tester_variable('debut_intertitre_3', '<h4 class="spip">');
	tester_variable('fin_intertitre_3', '</h4>');
	tester_variable('debut_intertitre_4', '<h5 class="spip">');
	tester_variable('fin_intertitre_4', '</h5>');
	tester_variable('debut_intertitre_5', '<h6 class="spip">');
	tester_variable('fin_intertitre_5', '</h6>');

	tester_variable('toujours_paragrapher', false);

	global $debut_intertitre, $fin_intertitre;
	global $debut_intertitre_2, $fin_intertitre_2;
	global $debut_intertitre_3, $fin_intertitre_3;
	global $debut_intertitre_4, $fin_intertitre_4;
	global $debut_intertitre_5, $fin_intertitre_5;

	$chercher_raccourcis = array(
		/* 1 */ 	"/(^|[^{])[{][{][{]/S",
		/* 2 */ 	"/[}][}][}]($|[^}])/S",
		/* 3 */ 	"/(^|[^{])\{1\{/S",
		/* 4 */ 	"/\}1\}($|[^}])/S",
		/* 5 */ 	"/(^|[^{])\{2\{/S",
		/* 6 */ 	"/\}2\}($|[^}])/S",
		/* 7 */ 	"/(^|[^{])\{3\{/S",
		/* 8 */ 	"/\}3\}($|[^}])/S",
		/* 9 */ 	"/(^|[^{])\{4\{/S",
		/* 10 */ 	"/\}4\}($|[^}])/S",
		/* 9b */ 	"/(^|[^{])\{5\{/S",
		/* 10b */ 	"/\}5\}($|[^}])/S",
		/* 11 */ 	"/\{(§|Â§)\{/S", # Â§ Pour gérer l'unicode aussi !
		/* 12 */ 	"/\}(§|Â§)\}/S",
		/* 13 */ 	"/<-->/S",
		/* 14 */ 	"/-->/S",
		/* 15 */ 	"/<--/S"
	);

	$remplacer_raccourcis = array(
		/* 1 */ 	"\$1\n\n$debut_intertitre",
		/* 2 */ 	"$fin_intertitre\n\n\$1",
		/* 3 */ 	"\$1\n\n$debut_intertitre",
		/* 4 */ 	"$fin_intertitre\n\n\$1",
		/* 5 */ 	"\$1\n\n$debut_intertitre_2",
		/* 6 */ 	"$fin_intertitre_2\n\n\$1",
		/* 7 */ 	"\$1\n\n$debut_intertitre_3",
		/* 8 */ 	"$fin_intertitre_3\n\n\$1",
		/* 9 */ 	"\$1\n\n$debut_intertitre_4",
		/* 10 */ 	"$fin_intertitre_4\n\n\$1",
		/* 9b */ 	"\$1\n\n$debut_intertitre_5",
		/* 10b */ 	"$fin_intertitre_5\n\n\$1",
		/* 11 */ 	"<span style=\"font-variant: small-caps\">",
		/* 12 */ 	"</span>",
		/* 13 */ 	"&harr;",
		/* 14 */ 	"&rarr;",
		/* 15 */ 	"&larr;"
	);

	$texte = preg_replace($chercher_raccourcis, $remplacer_raccourcis, $texte);

	// remplace les fausses listes à puce par de vraies
	// (recherche en début de lignes - suivi d'un ou plusieurs caractères blancs, en mode multiligne)
	// $texte =  preg_replace('/^-\s+/m','-* ',$texte); # deja fait dans post_propre

	return $texte;
}

function BarreTypoEnrichie_post_propre($texte) {

	# Le remplacement des intertitres de premier niveau a déjà été effectué dans inc/texte.php

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
			/* 15 */ 	"<div class=\"spip\" style=\"text-align:right;\">$1</div>",
			/* 17 */ 	"<div class=\"spip\" style=\"text-align:center;\">$1</div>",
			/* 19 */ 	"<div class=\"texteencadre-spip spip\">$1</div>",
			/* 21 */ 	"<strong class=\"caractencadre2-spip spip\">",
			/* 21b */ 	"<strong class=\"caractencadre-spip spip\">",
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
			/* 40 */	"<acronym title='$2' class='spip_acronym spip'>$1</acronym>",
			/* 41 */	"<a href=$1 title=\"$3\">$2</a>"

		);
		$texte = preg_replace($cherche1, $remplace1, $texte);
		$texte = paragrapher($texte,$GLOBALS['toujours_paragrapher']); // il faut reparagrapher a cause des raccourcis typo que l'on a ajoute (block div)
	return $texte;
}


function BarreTypoEnrichie_post_typo($texte) {
	// Correction des & en &amp;
	$texte = preg_replace('/&([A-Za-z#0-9]*);/','@@@amp:\1:amp@@@',$texte); // échapement des entités html déjà présentes
	$texte = str_replace('&','&amp;',$texte);
	$texte = preg_replace('/@@@amp:([A-Za-z#0-9]*):amp@@@/','&\1;',$texte);
	// Raccourci typographique <sc></sc>
	$texte = str_replace("<sc class=\"spip\">",
		"<span class=\"spip\" style=\"font-variant: small-caps\">", $texte);
	$texte = str_replace("</sc>", "</span>", $texte);
	return $texte;
}

function BarreTypoEnrichie_nettoyer_raccourcis_typo($texte){
	$texte = preg_replace(',{[1-5]{,','',$texte);
	$texte = preg_replace(',}[1-5]},','',$texte);
	return $texte;
}

function BarreTypoEnrichie_header_prive($texte) {
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	$texte.= '<link rel="stylesheet" type="text/css" href="' . (_DIR_PLUGINS.end($p)) . '/css/bartypenr.css" />' . "\n";
	return $texte;
}

?>