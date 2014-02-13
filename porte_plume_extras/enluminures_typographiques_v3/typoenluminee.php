<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
  * Ce plugin rajoute des raccourcis typographique et ameliore les possibilites de la barre typographique pour les redacteurs
*/

	/*
	 *    Fonctions de ces filtres :
	 *     Ils rajoutent quelques racourcis typo a SPIP
	 *
	 *     Syntaxe des raccourcis :
	 *           [/texte/] : aligner le texte a droite
	 *           [!texte!] : aligner le texte a gauche
	 *           [|texte|] : centrer le texte
	 *           [(texte)] : encadrer le texte (occupe toute la largeur de la page, a mettre autour d'un paragraphe)
	 *           [*texte*] : encadrer/surligner le texte (une partie a l'interieur d'un paragraphe)
	 *           [**texte*] : variante encadrer/surligner le texte (une partie a l'interieur d'un paragraphe)
	 *           <sup>texte</sup> : mettre en exposant le texte selectionne
	 *           <sub>texte</sub> : mettre en indice le texte selectionne
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

// Gerer les variables de personnalisation, fonction depreciee sous SPIP 2.0
// A suivre sur la methode...
if(!function_exists('tester_variable')) {
	function tester_variable($var, $val){
		if (!isset($GLOBALS[$var]))	$GLOBALS[$var] = $val;
	}
}

function typoenluminee_pre_propre($texte) {
	if(!$texte) return $texte;

	static $chercher_raccourcis=NULL;
	static $remplacer_raccourcis=NULL;
	
	if ($chercher_raccourcis===NULL) {
	
		// tous les elements block doivent etre introduits ici
		// pour etre pris en charge par paragrapher
	
		// Definition des differents intertitres possibles, si pas deja definies
		if ((!function_exists('lire_config')) OR (isset($GLOBALS['config_intertitre']))) {
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
			$GLOBALS['debut_intertitre'] = lire_config('bte/titraille1open','<h3 class="spip">');
			$GLOBALS['fin_intertitre'] = lire_config('bte/titraille1close','</h3>');
			$GLOBALS['debut_intertitre_2'] = lire_config('bte/titraille2open','<h4 class="spip">');
			$GLOBALS['fin_intertitre_2'] = lire_config('bte/titraille2close','</h4>');
			$GLOBALS['debut_intertitre_3'] = lire_config('bte/titraille3open','<h5 class="spip">');
			$GLOBALS['fin_intertitre_3'] = lire_config('bte/titraille3close','</h5>');
			$GLOBALS['debut_intertitre_4'] = lire_config('bte/titraille4open','<h6 class="spip">');
			$GLOBALS['fin_intertitre_4'] = lire_config('bte/titraille4close','</h6>');
			$GLOBALS['debut_intertitre_5'] = lire_config('bte/titraille5open','<strong class="spip titraille5">');
			$GLOBALS['fin_intertitre_5'] = lire_config('bte/titraille5close','</strong>');
		}
	
		tester_variable('toujours_paragrapher', false);
	
		global $debut_intertitre, $fin_intertitre;
		global $debut_intertitre_2, $fin_intertitre_2;
		global $debut_intertitre_3, $fin_intertitre_3;
		global $debut_intertitre_4, $fin_intertitre_4;
		global $debut_intertitre_5, $fin_intertitre_5;
	
		$chercher_raccourcis=array();
		$remplacer_raccourcis=array();
	
		/* 9b */ 	$chercher_raccourcis[]="/(^|[^{])[{][{][{]\*\*\*\*\*(.*)[}][}][}]($|[^}])/SUms";
		/* 9 */ 	$chercher_raccourcis[]="/(^|[^{])[{][{][{]\*\*\*\*(.*)[}][}][}]($|[^}])/SUms";
		/* 7 */ 	$chercher_raccourcis[]="/(^|[^{])[{][{][{]\*\*\*(.*)[}][}][}]($|[^}])/SUms";
		/* 5 */ 	$chercher_raccourcis[]="/(^|[^{])[{][{][{]\*\*(.*)[}][}][}]($|[^}])/SUms";
		/* 3 */ 	$chercher_raccourcis[]="/(^|[^{])[{][{][{]\*(.*)[}][}][}]($|[^}])/SUms";
		/* 1 */ 	$chercher_raccourcis[]="/(^|[^{])[{][{][{](.*)[}][}][}]($|[^}])/SUms";
		/* 11 */ 	$chercher_raccourcis[]="/\{(�|§)\{/S"; # § Pour gerer l'unicode aussi !
		/* 12 */ 	$chercher_raccourcis[]="/\}(�|§)\}/S"; # ne pas sauvergarder ce fichier en utf8 !

		/* 9b */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre_5\$2$fin_intertitre_5\n\n\$3";
		/*  9 */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre_4\$2$fin_intertitre_4\n\n\$3";
		/*  7 */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre_3\$2$fin_intertitre_3\n\n\$3";
		/*  5 */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre_2\$2$fin_intertitre_2\n\n\$3";
		/*  3 */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre\$2$fin_intertitre\n\n\$3";
		/*  1 */	$remplacer_raccourcis[]="\$1\n\n$debut_intertitre\$2$fin_intertitre\n\n\$3";
		/* 11 */	$remplacer_raccourcis[]="<sc>";
		/* 12 */	$remplacer_raccourcis[]="</sc>";
	}

	// Conversion des intertitres d'enluminures type {n{titre}n}
	// ou n est un nombre en intertitres avec des etoiles type {{{* (avec n etoiles)
	// {1{ sera converti en {{{* ; {2{ sera converti en {{{** ; etc.
	// Ne faire la recherche que s'il y a au moins un titre ancienne mode a convertir
	if (strpos($texte, '{1{')!==false
		OR strpos($texte, '{2{')!==false
		OR strpos($texte, '{3{')!==false
		OR strpos($texte, '{4{')!==false
		OR strpos($texte, '{5{')!==false) {
			$texte=preg_replace_callback ("/\{(\d)\{(.*)\}(\\1)\}/Ums",
							create_function (
								'$matches',
								'return "{{{".str_repeat("*",$matches[1]).trim($matches[2])."}}}";'
							),
							$texte);
	}
	$texte = preg_replace($chercher_raccourcis, $remplacer_raccourcis, $texte);

	return $texte;
}

function typoenluminee_post_propre($texte) {
	if(!$texte) return $texte;
	static $cherche1 = NULL;
	static $remplace1 = NULL;
	if ($cherche1===NULL) {
		# Le remplacement des intertitres de premier niveau a deja ete effectue dans inc/texte.php
		$cherche1 = array();
		$remplace1 = array();
		$cherche1[] = /* 15 */ 	",\[/(.*)/\],Ums";
		$cherche1[] = /* 16 */ 	",\[!(.*)!\],Ums";
		$cherche1[] = /* 17 */ 	",\[\|(.*)\|\],Ums";
		$cherche1[] = /* 19 */ 	",\[\((.*)\)\],Ums";
		$cherche1[] = /* 21 */ 	"/\[\*\*/S";
		$cherche1[] = /* 21b */ 	"/\[\*/S";
		$cherche1[] = /* 22 */	"/\*\]/S";
	
		$remplace1[] = /* 15 */ 	"<div class=\"spip\" style=\"text-align:right;\">$1</div>";
		$remplace1[] = /* 16 */ 	"<div class=\"spip\" style=\"text-align:left;\">$1</div>";
		$remplace1[] = /* 17 */ 	"<div class=\"spip\" style=\"text-align:center;\">$1</div>";
		$remplace1[] = /* 19 */ 	"<div class=\"texteencadre-spip spip\">$1</div>";
		$remplace1[] = /* 21 */ 	"<strong class=\"caractencadre2-spip spip\">";
		$remplace1[] = /* 21b */ 	"<strong class=\"caractencadre-spip spip\">";
		$remplace1[] = /* 22 */	"</strong>";
	}
	$texte = preg_replace($cherche1, $remplace1, $texte);
	$texte = paragrapher($texte,$GLOBALS['toujours_paragrapher']); // il faut reparagrapher a cause des raccourcis typo que l'on a ajoute (block div)
	return $texte;
}

function typoenluminee_pre_liens($texte) {
	if (!isset($GLOBALS['barre_typo_pas_de_fork_typo']) OR $GLOBALS['barre_typo_pas_de_fork_typo'] === true)
		return $texte;

	$texte = str_replace('<-->','&harr;',$texte);
	$texte = str_replace('-->','&rarr;',$texte);

	return $texte;
}

function typoenluminee_pre_typo($texte) {
	if(!$texte) return $texte;
	static $local_barre_typo_pas_de_fausses_puces = null;
	static $chercher_raccourcis;
	static $remplacer_raccourcis;
	global $debut_italique, $fin_italique;
	if (!isset($GLOBALS['barre_typo_pas_de_fork_typo']) OR $GLOBALS['barre_typo_pas_de_fork_typo'] === true)
		return $texte;

	if ($local_barre_typo_pas_de_fausses_puces===null){
		// remplace les fausses listes a puce par de vraies ?
		// (recherche en debut de lignes - suivi d'un ou plusieurs caracteres blancs, en mode multiligne)
		// Mettre $GLOBALS['barre_typo_pas_de_fausses_puces'] = true; dans mes_options.php pour avoir ce comportement
		if (isset($GLOBALS['barre_typo_pas_de_fausses_puces'])) {
			$local_barre_typo_pas_de_fausses_puces = $GLOBALS['barre_typo_pas_de_fausses_puces'];
		} else {
			if (function_exists('lire_config')) {
				$local_barre_typo_pas_de_fausses_puces = (lire_config('bte/puces','Non') == 'Oui')?true:false;
			}
		}
		global $class_spip;
		tester_variable('debut_italique', "<i$class_spip>");
		tester_variable('fin_italique', '</i>');
		
		$chercher_raccourcis = array(
			/* 9 */ 	"/(?<![{\d])[{](?![{\d])/S", // Expressions complexes car on n'a pas encore traite les titres ici
			/* 10 */	"/(?<![}\d])[}](?![}\d])/S", // puisque italique utilisent les memes caracteres en nombre inferieur
		);
	
		$remplacer_raccourcis = array(
			/* 9 */ 	$debut_italique,
			/* 10 */	$fin_italique,
		);
	}
	if ($local_barre_typo_pas_de_fausses_puces === true) {
		$texte =  preg_replace('/^-\s+/m','-* ',$texte);
	}

	$texte = str_replace('<--','&larr;',$texte);
	$texte = str_replace('<==>','&hArr;',$texte);
	$texte = str_replace('==>','&rArr;',$texte);
	$texte = str_replace('<==','&lArr;',$texte);
	$texte = str_ireplace('(c)','&copy;',$texte);
	$texte = str_ireplace('(r)','&reg;',$texte);
	$texte = str_ireplace('(tm)','&trade;',$texte);
	$texte = str_replace('...','&hellip;',$texte);
	$texte = preg_replace($chercher_raccourcis, $remplacer_raccourcis, $texte);
	/*
		Cas particulier pour le gras
		Il ne faut pas traiter la mise en gras ici si le texte contient un tableau
	*/
	if (!preg_match(',.(\|([[:space:]]*{{[^}]+}}[[:space:]]*|<))+.,sS', $texte)) {
		$chercher_raccourcisg = array(
			/* 7 */ 	"/(?<![{])[{][{](?![{])/S", // Expressions complexes car on n'a pas encore traite les titres ici
			/* 8 */ 	"/(?<![}])[}][}](?![}])/S" // En gros, verification qu'on n'est pas a l'interieur d'un titre
		);
		$remplacer_raccourcisg = array(
			/* 7 */ 	"<strong class=\"spip\">",
			/* 8 */ 	"</strong>"
		);
		$texte = preg_replace($chercher_raccourcisg, $remplacer_raccourcisg, $texte);
	}
	return $texte;
}

function typoenluminee_post_typo($texte) {
	if(!$texte) return $texte;
	if (!isset($GLOBALS['barre_typo_pas_de_fork_typo']) OR $GLOBALS['barre_typo_pas_de_fork_typo'] === true)
		return $texte;
	$texte = str_replace('[^','<sup>',$texte);
	$texte = str_replace('^]','</sup>',$texte);
	$texte = str_replace('[**','<strong class="caractencadre2-spip spip">',$texte);
	$texte = str_replace('[*','<strong class="caractencadre-spip spip">',$texte);
	$texte = str_replace('*]','</strong>',$texte);
	
	// Correction des & en &amp;
	$texte = preg_replace('/&([A-Za-z#0-9]*);/','@@@amp:\1:amp@@@',$texte); // echapement des entites html deja presentes
	$texte = str_replace('&','&amp;',$texte);
	$texte = preg_replace('/@@@amp:([A-Za-z#0-9]*):amp@@@/','&\1;',$texte);
	// Raccourci typographique <sc></sc>
	$texte = str_replace('<sc>', '<span class="caps">', $texte);
	$texte = str_replace('</sc>', '</span>', $texte);
	return $texte;
}

function typoenluminee_nettoyer_raccourcis_typo($texte){
	$texte = preg_replace(',\{[1-5]\{,','',$texte);
	$texte = preg_replace(',\}[1-5]\},','',$texte);
	$texte = preg_replace(',\{\{\{\*+,','{{{',$texte);
	$texte = str_replace('&hellip;','...',$texte);
	return $texte;
}

?>