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

function TypoEnluminee_pre_propre($texte) {
	static $chercher_raccourcis=NULL;
	static $remplacer_raccourcis=NULL;
	
	if ($chercher_raccourcis===NULL) {
	
		// tous les elements block doivent etre introduits ici
		// pour etre pris en charge par paragrapher
	
		// Definition des differents intertitres possibles, si pas deja definies
		if (!function_exists('lire_config')) {
			tester_variable('debut_intertitre', '<h3 class="spip">');
			tester_variable('fin_intertitre', '</h3>');
			tester_variable('debut_intertitre_2', '<h4 class="spip">');
			tester_variable('fin_intertitre_2', '</h4>');
			tester_variable('debut_intertitre_3', '<h5 class="spip">');
			tester_variable('fin_intertitre_3', '</h5>');
			tester_variable('debut_intertitre_4', '<h6 class="spip">');
			tester_variable('fin_intertitre_4', '</h6>');
			tester_variable('debut_intertitre_5', '<strong class="spip titraille5">');
			tester_variable('fin_intertitre_5', '</strong>');
		} else {
			tester_variable('debut_intertitre', lire_config('bte/titraille1open','<h3 class="spip">'));
			tester_variable('fin_intertitre', lire_config('bte/titraille1close','</h3>'));
			tester_variable('debut_intertitre_2', lire_config('bte/titraille2open','<h4 class="spip">'));
			tester_variable('fin_intertitre_2', lire_config('bte/titraille2close','</h4>'));
			tester_variable('debut_intertitre_3', lire_config('bte/titraille3open','<h5 class="spip">'));
			tester_variable('fin_intertitre_3', lire_config('bte/titraille3close','</h5>'));
			tester_variable('debut_intertitre_4', lire_config('bte/titraille4open','<h6 class="spip">'));
			tester_variable('fin_intertitre_4', lire_config('bte/titraille4close','</h6>'));
			tester_variable('debut_intertitre_5', lire_config('bte/titraille5open','<strong class="spip titraille5">'));
			tester_variable('fin_intertitre_5', lire_config('bte/titraille5close','</strong>'));
		}
	
		tester_variable('toujours_paragrapher', false);
	
		global $debut_intertitre, $fin_intertitre;
		global $debut_intertitre_2, $fin_intertitre_2;
		global $debut_intertitre_3, $fin_intertitre_3;
		global $debut_intertitre_4, $fin_intertitre_4;
		global $debut_intertitre_5, $fin_intertitre_5;
	
		$chercher_raccourcis=array();
		$remplacer_raccourcis=array();
		global $BarreTypoEnrichie;
		if (is_array($BarreTypoEnrichie))
			foreach($BarreTypoEnrichie as $item) {
				$chercher_raccourcis[]=$item['chercher'];
				$remplacer_raccourcis[]=$item['remplacer'];	
			}
	
			/* 1 */ 	$chercher_raccourcis[]="/(^|[^{])[{][{][{]/S";
			/* 2 */ 	$chercher_raccourcis[]="/[}][}][}]($|[^}])/S";
			/* 3 */ 	$chercher_raccourcis[]="/(^|[^{])\{1\{/S";
			/* 4 */ 	$chercher_raccourcis[]="/\}1\}($|[^}])/S";
			/* 5 */ 	$chercher_raccourcis[]="/(^|[^{])\{2\{/S";
			/* 6 */ 	$chercher_raccourcis[]="/\}2\}($|[^}])/S";
			/* 7 */ 	$chercher_raccourcis[]="/(^|[^{])\{3\{/S";
			/* 8 */ 	$chercher_raccourcis[]="/\}3\}($|[^}])/S";
			/* 9 */ 	$chercher_raccourcis[]="/(^|[^{])\{4\{/S";
			/* 10 */ 	$chercher_raccourcis[]="/\}4\}($|[^}])/S";
			/* 9b */ 	$chercher_raccourcis[]="/(^|[^{])\{5\{/S";
			/* 10b */ 	$chercher_raccourcis[]="/\}5\}($|[^}])/S";
			/* 11 */ 	$chercher_raccourcis[]="/\{(ง|ยง)\{/S"; # ยง Pour gerer l'unicode aussi !
			/* 12 */ 	$chercher_raccourcis[]="/\}(ง|ยง)\}/S";
			/* 13 */ 	$chercher_raccourcis[]="/<-->/S";
			/* 14 */ 	$chercher_raccourcis[]="/-->/S";
			/* 15 */ 	$chercher_raccourcis[]="/<--/S";
			/* 16 */ 	$chercher_raccourcis[]="/<==>/S";
			/* 17 */ 	$chercher_raccourcis[]="/==>/S";
			/* 18 */ 	$chercher_raccourcis[]="/<==/S";
			/* 19 */ 	$chercher_raccourcis[]="/\([cC]\)/S";
			/* 20 */ 	$chercher_raccourcis[]="/\([rR]\)/S";
			/* 21 */ 	$chercher_raccourcis[]="/\([tT][mM]\)/S";
			/* 22 */ 	$chercher_raccourcis[]="/\.\.\./S";
	
			/*  1 */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre";
			/*  2 */	$remplacer_raccourcis[]="$fin_intertitre\n\n\$1";
			/*  3 */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre";
			/*  4 */	$remplacer_raccourcis[]="$fin_intertitre\n\n\$1";
			/*  5 */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre_2";
			/*  6 */	$remplacer_raccourcis[]="$fin_intertitre_2\n\n\$1";
			/*  7 */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre_3";
			/*  8 */	$remplacer_raccourcis[]="$fin_intertitre_3\n\n\$1";
			/*  9 */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre_4";
			/* 10 */	$remplacer_raccourcis[]="$fin_intertitre_4\n\n\$1";
			/* 9b */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre_5";
			/* 10b */	$remplacer_raccourcis[]="$fin_intertitre_5\n\n\$1";
			/* 11 */	$remplacer_raccourcis[]="<span style=\"font-variant: small-caps\">";
			/* 12 */	$remplacer_raccourcis[]="</span>";
			/* 13 */	$remplacer_raccourcis[]="&harr;";
			/* 14 */	$remplacer_raccourcis[]="&rarr;";
			/* 15 */	$remplacer_raccourcis[]="&larr;";
			/* 16 */	$remplacer_raccourcis[]="&hArr;";
			/* 17 */	$remplacer_raccourcis[]="&rArr;";
			/* 18 */	$remplacer_raccourcis[]="&lArr;";
			/* 19 */	$remplacer_raccourcis[]="&copy;";
			/* 20 */	$remplacer_raccourcis[]="&reg;";
			/* 21 */	$remplacer_raccourcis[]="&trade;";
			/* 22 */	$remplacer_raccourcis[]="&hellip;";
	}

	$texte = preg_replace($chercher_raccourcis, $remplacer_raccourcis, $texte);

	// remplace les fausses listes a puce par de vraies
	// (recherche en debut de lignes - suivi d'un ou plusieurs caracteres blancs, en mode multiligne)
	// $texte =  preg_replace('/^-\s+/m','-* ',$texte); # deja fait dans post_propre

	return $texte;
}

function TypoEnluminee_post_propre($texte) {
	static $cherche1 = NULL;
	static $remplace1 = NULL;

	if ($cherche1===NULL) {
		# Le remplacement des intertitres de premier niveau a deja ete effectue dans inc/texte.php
		$cherche1 = array();
		$remplace1 = array();
		global $BarreTypoEnrichieBlocs;
		if (is_array($BarreTypoEnrichieBlocs))
			foreach($BarreTypoEnrichieBlocs as $item) {
				$cherche1[]=$item['chercher'];
				$remplace1[]=$item['remplacer'];
			}
	
		$cherche1[] = /* 15 */ 	",\[/(.*)/\],Ums";
		$cherche1[] = /* 17 */ 	",\[\|(.*)\|\],Ums";
		$cherche1[] = /* 19 */ 	",\[\((.*)\)\],Ums";
		$cherche1[] = /* 21 */ 	"/\[\*\*/S";
		$cherche1[] = /* 21b */ 	"/\[\*/S";
		$cherche1[] = /* 22 */	"/\*\]/S";
		$cherche1[] = /* 23 */ 	"/\[\^/S";
		$cherche1[] = /* 24 */	"/\^\]/S";
		$cherche1[] = /* 40 */	"/@@acro@@([^@]*)@@([^@]*)@@acro@@/S";
	
		$remplace1[] = /* 15 */ 	"<div class=\"spip\" style=\"text-align:right;\">$1</div>";
		$remplace1[] = /* 17 */ 	"<div class=\"spip\" style=\"text-align:center;\">$1</div>";
		$remplace1[] = /* 19 */ 	"<div class=\"texteencadre-spip spip\">$1</div>";
		$remplace1[] = /* 21 */ 	"<strong class=\"caractencadre2-spip spip\">";
		$remplace1[] = /* 21b */ 	"<strong class=\"caractencadre-spip spip\">";
		$remplace1[] = /* 22 */	"</strong>";
		$remplace1[] = /* 23 */ 	"<sup>";
		$remplace1[] = /* 24 */	"</sup>";
		$remplace1[] = /* 40 */	"<acronym title='$1' class='spip_acronym spip'>$2</acronym>";
	}

	$texte = preg_replace($cherche1, $remplace1, $texte);
	$texte = paragrapher($texte,$GLOBALS['toujours_paragrapher']); // il faut reparagrapher a cause des raccourcis typo que l'on a ajoute (block div)
	return $texte;
}


function TypoEnluminee_pre_typo($texte) {
	if ($GLOBALS['barre_typo_pas_de_fork_typo'] === true)
		return $texte;
	
	// remplace les fausses listes a puce par de vraies
	// (recherche en debut de lignes - suivi d'un ou plusieurs caracteres blancs, en mode multiligne)
	// Mettre $GLOBALS['barre_typo_preserve_puces'] = true; dans mes_options.php pour ne pas avoir ce comportement
	if (!function_exists('lire_config')) {
		global $barre_typo_pas_de_fausses_puces;
	} else {
		if (lire_config('bte/puces','Non') == 'Oui') {
			$barre_typo_pas_de_fausses_puces = true;
		} else {
			$barre_typo_pas_de_fausses_puces = false;
		}
	}

	if ($barre_typo_pas_de_fausses_puces === true) {
		$texte =  preg_replace('/^-\s+/m','-* ',$texte);
	}
	
	$chercher_raccourcis = array(
		/* 9 */ 	"/(?<![{\d])[{](?![{\d])/S", // Expressions complexes car on n'a pas encore traite les titres ici
		/* 10 */	"/(?<![}\d])[}](?![}\d])/S", // puisque italique utilisent les memes caracteres en nombre inferieur
		/* 13 */ 	"/<-->/S",
		/* 14 */ 	"/-->/S",
		/* 15 */ 	"/<--/S",
		/* 16 */ 	"/<==>/S",
		/* 17 */ 	"/==>/S",
		/* 18 */ 	"/<==/S",
		/* 19 */ 	"/\(c\)/Si",
		/* 20 */ 	"/\(r\)/Si",
		/* 21 */ 	"/\(tm\)/Si",
		/* 22 */ 	"/\.\.\./S",
		/* 23 */	"/\[([^|?][^][]*)\|((?:[^][](?!->))*)\]/S"
	);

	$remplacer_raccourcis = array(
		/* 9 */ 	"<i class=\"spip\">",
		/* 10 */	"</i>",
		/* 13 */ 	"&harr;",
		/* 14 */ 	"&rarr;",
		/* 15 */ 	"&larr;",
		/* 16 */ 	"&hArr;",
		/* 17 */ 	"&rArr;",
		/* 18 */ 	"&lArr;",
		/* 19 */ 	"&copy;",
		/* 20 */ 	"&reg;",
		/* 21 */ 	"&trade;",
		/* 22 */ 	"&hellip;",
		/* 23 */	"@@acro@@$2@@$1@@acro@@"
	);

	$texte = preg_replace($chercher_raccourcis, $remplacer_raccourcis, $texte);
	
	/*
		Cas particulier pour le gras
		Il ne faut pas traiter la mise en gras ici si le texte contient un tableau
	*/
	if (!preg_match(',.(\|([[:space:]]*{{[^}]+}}[[:space:]]*|<))+.,sS', $texte)) {
		$chercher_raccourcis = array(
			/* 7 */ 	"/(?<![{])[{][{](?![{])/S", // Expressions complexes car on n'a pas encore traite les titres ici
			/* 8 */ 	"/(?<![}])[}][}](?![}])/S" // En gros, verification qu'on n'est pas a l'interieur d'un titre
		);
		$remplacer_raccourcis = array(
			/* 7 */ 	"<strong class=\"spip\">",
			/* 8 */ 	"</strong>"
		);
		$texte = preg_replace($chercher_raccourcis, $remplacer_raccourcis, $texte);
	}
	return $texte;
}

function TypoEnluminee_post_typo($texte) {
	if ($GLOBALS['barre_typo_pas_de_fork_typo'] === true)
		return $texte;
	$cherche1 = array(
		/* 21 */ 	"/\[\*\*/S",
		/* 21b */ 	"/\[\*/S",
		/* 22 */	"/\*\]/S",
		/* 23 */ 	"/\[\^/S",
		/* 24 */	"/\^\]/S",
	);

	$remplace1 = array(
		/* 21 */ 	"<strong class=\"caractencadre2-spip spip\">",
		/* 21b */ 	"<strong class=\"caractencadre-spip spip\">",
		/* 22 */	"</strong>",
		/* 23 */ 	"<sup>",
		/* 24 */	"</sup>",
	);
	$texte = preg_replace($cherche1, $remplace1, $texte);
	// Acronymes
	$texte = preg_replace('/@@acro@@([^@]*)@@([^@]*)@@acro@@/S',"<acronym title='$1' class='spip_acronym spip'>$2</acronym>",$texte);
	// Correction des & en &amp;
	$texte = preg_replace('/&([A-Za-z#0-9]*);/','@@@amp:\1:amp@@@',$texte); // echapement des entites html deja presentes
	$texte = str_replace('&','&amp;',$texte);
	$texte = preg_replace('/@@@amp:([A-Za-z#0-9]*):amp@@@/','&\1;',$texte);
	// Raccourci typographique <sc></sc>
	$texte = str_replace("<sc>",
		"<span class=\"spip\" style=\"font-variant: small-caps\">", $texte);
	$texte = str_replace("</sc>", "</span>", $texte);
	return $texte;
}

function TypoEnluminee_nettoyer_raccourcis_typo($texte){
	$texte = preg_replace(',{[1-5]{,','',$texte);
	$texte = preg_replace(',}[1-5]},','',$texte);
	$texte = str_replace('&hellip;','...',$texte);
	return $texte;
}

?>