<?php

/*****************************************************************************\
 *  Decompilateur créant une syntaxe avec des <| class='' > pour GESHI 1.0.8 *
\*****************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Encadre un texte donne par un code <| style='x'|code|> ou <| class='x'|code|>
 * que GESHI remplacera ensuite par des spans
 * 
 * L'encadrement tient compte du fait que l'on veut des styles inline
 * ou des classes CSS dans le code genere par GESHI
 *
 * On indique le type de style que l'on veut (KEYWORDS) et son numero (30)
 * Il faut se referer a geshi/geshi/spip3.php pour avoir les correspondances.
 *
 * @param string $code
 * 		Le code a encadrer
 * @param string $styles_geshi_name
 * 		Un des types de styles dans Geshi ( KEYWORDS uniquement utilise pour l'instant )
 * @param int $styles_geshi_key
 * 		Le numero de style dans le groupe precedent.
 * @return string
 * 		Le code encadre.
**/
function format_to_geshi_spip($code, $styles_geshi_name, $styles_geshi_key) {
	static $use_style = null;
	static $styles = array();
	static $classes = array();

	// utiliser des styles ou des classes
	if (is_null($use_style)) {
		// geshi/spip3.php doit forcement etre deja charge !
		include_spip('geshi/geshi/spip3');
		$styles = geshi_language_data_spip3_styles();
		$use_style = (!defined('PLUGIN_COLORATION_CODE_STYLES_INLINE') OR PLUGIN_COLORATION_CODE_STYLES_INLINE);
		$classes = array(
			'KEYWORDS' => 'kw'
		);
	}

	$style  = $styles[ $styles_geshi_name ][ $styles_geshi_key ];
	$classe = $classes[ $styles_geshi_name ] . $styles_geshi_key;
	$in = $use_style ? "style=\"$style\"" : "class=\"$classe\"";
	$code = "<| $in>$code|>";
	return $code;
}




/**
 * Demande d'encadrer un texte pour geshi selon un code de couleur demande.
 * 
 *
 * @param string $code
 * 		Le code a encadrer
 * @param string $quoi
 * 		Un type de couleur plus lisible que les mots cles de GESHI
 * 		La correspondance est fait avec les tags GESHI dans cette fonction.
 * @param bool $echapper
 * 		Echappe le texte au passage selon la methode de GESHI
 * 		Evidemment, il ne faut pas le faire 2 fois pour un meme texte !
 * @return string
 * 		Le code encadre.
**/
function format_geshi_spip($code, $quoi, $echapper=true) {
	// echapper le code en mode geshi
	if ($echapper) {
		$code = geshi_hsc($code);
	}

	// ajouter l'encadrement geshi
	switch($quoi) {
		case 'boucle':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 10);
			break;
		case 'boucle_table':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 11);
			break;
		case 'boucle_critere':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 12);
			break;

		case 'balise':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 20);
			break;
		case 'balise_boucle':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 21);
			break;
		case 'balise_etoile':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 22);
			break;
			
		case 'balise_crochet':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 25);
			break;
		case 'balise_parenthese':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 26);
			break;

		case 'filtre':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 30);
			break;
		case 'parametre':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 31);
			break;
		case 'parametre_contenu':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 32);
			break;

		case 'inclure':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 40);
			break;
		case 'inclure_fichier':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 41);
			break;

		case 'polyglotte':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 50);
			break;
		case 'polyglotte_langue':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 51);
			break;
		case 'polyglotte_contenu':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 52);
			break;

		case 'idiome':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 60);
			break;
		case 'idiome_module':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 61);
			break;
		case 'idiome_chaine':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 62);
			break;

		case 'operateur':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 70);
			break;

		case 'nombre':
			$code = format_to_geshi_spip($code, 'KEYWORDS', 80);
			break;
	}
	return $code;
}


/**
 * Copie de la methode GESHI->hsc() (geshi/geshi.php)
 * servant a echapper les textes.
**/
function geshi_hsc($string, $quote_style = ENT_COMPAT) {
	// init
	static $aTransSpecchar = array(
		'&' => '&amp;',
		'"' => '&quot;',
		'<' => '&lt;',
		'>' => '&gt;',

		//This fix is related to SF#1923020, but has to be applied
		//regardless of actually highlighting symbols.

		//Circumvent a bug with symbol highlighting
		//This is required as ; would produce undesirable side-effects if it
		//was not to be processed as an entity.
		';' => '<SEMI>', // Force ; to be processed as entity
		'|' => '<PIPE>' // Force | to be processed as entity
		);                      // ENT_COMPAT set

	switch ($quote_style) {
		case ENT_NOQUOTES: // don't convert double quotes
			unset($aTransSpecchar['"']);
			break;
		case ENT_QUOTES: // convert single quotes as well
			$aTransSpecchar["'"] = '&#39;'; // (apos) htmlspecialchars() uses '&#039;'
			break;
	}

	// return translated string
	return strtr($string, $aTransSpecchar);
}




/**
 * <BOUCLES> 
 * Fonction automatiquement appelee par le decompilateur
 * pour recreer le code d'une boucle.
 *
**/
function format_boucle_html_geshi ($avant, $nom, $type, $crit, $corps, $apres, $altern, $prof)
{
	$avant = $avant ? format_geshi_spip("<B$nom>", "boucle") . "$avant" : "";
	$apres = $apres ? "$apres" . format_geshi_spip("</B$nom>", "boucle") : "";
	$altern = $altern ? "$altern" . format_geshi_spip("<//B$nom>", "boucle") : "";
	if (!$corps) {
		$corps = format_geshi_spip(" />", "boucle");
	} else {
		$corps = format_geshi_spip(">", "boucle") . $corps . format_geshi_spip("</BOUCLE$nom>", "boucle");
	}
	return "$avant"
		. format_geshi_spip("<BOUCLE$nom", "boucle")
		. format_geshi_spip("($type)", "boucle_table")
		. "$crit$corps$apres$altern";
}




// raccourcis pour {parametre}
function _format_parametre_html_geshi($param, $echapper=true) {
	if (!$param) return '';

	if (is_array($param)) {
		$param = join(format_geshi_spip(", ", 'parametre'), $param);
	}

	return   format_geshi_spip("{", 'parametre')
		   . format_geshi_spip($param, 'parametre_contenu', $echapper)
		   . format_geshi_spip("}", 'parametre');
}




/**
 * <INCLURE> 
 * Fonction automatiquement appelee par le decompilateur
 * pour recreer le code d'une inclusion.
 *
**/
function format_inclure_html_geshi ($file, $args, $prof)
{
	if (strpos($file, '#')===false)
	 	$t = $file ? (
			  format_geshi_spip("(", 'inclure')
			. format_geshi_spip($file, 'inclure_fichier')
			. format_geshi_spip(")", 'inclure')) : "" ;
	else {
		$t = _format_parametre_html_geshi("fond=" . $file);
	}
	$args = _format_parametre_html_geshi($args, false);

	return (
		  format_geshi_spip("<INCLURE", 'inclure')
		. $t . $args
		. format_geshi_spip(" />", 'inclure'));
}


/**
 * <multi>[fr]texte</multi>
 * 
 * Fonction automatiquement appelee par le decompilateur
 * pour recreer le code d'un polyglotte.
 * 
**/
function format_polyglotte_html_geshi ($args, $prof)
{
	$contenu = array(); 
	foreach($args as $l=>$t)
		$contenu[]= ($l ? format_geshi_spip("[$l]", 'polyglotte_langue') : '') . $t;

	return (
		  format_geshi_spip("<multi>", 'polyglotte')
		. format_geshi_spip(join(" ", $contenu), 'polyglotte_contenu', false)
		. format_geshi_spip("</multi>", 'polyglotte')
	);
}



/**
 * <:chaine_de_langue:>
 * 
 * Fonction automatiquement appelee par le decompilateur
 * pour recreer le code d'un idiome.
 * 
**/
function format_idiome_html_geshi ($nom, $module, $args, $filtres, $prof)
{
	foreach ($args as $k => $v) {
		$args[$k] = "$k" . format_geshi_spip("=", 'operateur') . "$v";
	}
	$args = _format_parametre_html_geshi($args, false);
	return (
		  format_geshi_spip("<:", 'idiome')
		. ($module ? format_geshi_spip($module, 'idiome_module') . format_geshi_spip(":", 'idiome') : "")
		. format_geshi_spip($nom, 'idiome_chaine')
		. $args
		. $filtres
		. format_geshi_spip(":>", 'idiome')
	);
}



/**
 * #BALISE
 * [(#BALISE)]
 * 
 * Fonction automatiquement appelee par le decompilateur
 * pour recreer le code d'une balise.
 * 
**/
function format_champ_html_geshi ($nom, $boucle, $etoile, $avant, $apres, $args, $filtres, $prof)
{
	$nom = format_geshi_spip("#", 'balise')
	. ($boucle ? format_geshi_spip($boucle . ":", 'balise_boucle') : "")
	. format_geshi_spip($nom, 'balise')
	. format_geshi_spip($etoile, 'balise_etoile')
	. $args
	. $filtres;

	// Determiner si c'est un champ etendu, 
	$s = ($avant OR $apres OR $filtres
	      OR (strpos($args, '(#') !==false)); // ## A VERIFIER !!

	// champ simple
	if (!$s) {
		return $nom;
	}

	return format_geshi_spip("[", 'balise_crochet')
			. $avant
			. format_geshi_spip("(", 'balise_parenthese')
			. $nom
			. format_geshi_spip(")", 'balise_parenthese')
			. $apres
			. format_geshi_spip("]", 'balise_crochet');
}



/**
 * {criteres de boucles}
 * 
 * Fonction automatiquement appelee par le decompilateur
 * pour recreer les criteres d'une boucle.
 * 
**/
function format_critere_html_geshi ($critere)
{
	foreach ($critere as $k => $crit) {
		$crit_s = '';
		foreach ($crit as $operande) {
			list($type, $valeur) = $operande;
			if ($type == 'champ' AND $valeur[0]=='[') {
			  $valeur = substr($valeur,1,-1);
			  if (preg_match(',^[(](#[^|]*)[)]$,sS', $valeur))
				$valeur = substr($valeur,1,-1);
			}
			$crit_s .= $valeur;
		}
		$critere[$k] = $crit_s;
	}
	return (!$critere ? "" : format_geshi_spip(
		geshi_hsc("{") . join(", ", $critere). geshi_hsc("}"),
		"boucle_critere", false));
}


/**
 * {parametres de balises}
 * |filtre{parametres de filtres}
 * 
 * Fonction automatiquement appelee par le decompilateur
 * pour recreer les parametres d'une balise,
 * les filtres et leurs parametres.
 *
**/
function format_liste_html_geshi ($fonc, $args, $prof)
{
	return ((($fonc!=='')
		? format_geshi_spip("|$fonc", 'filtre')
		: $fonc)
	. _format_parametre_html_geshi($args, false));
}


/**
 *
 * Fonction automatiquement appelee par le decompilateur
 * pour concatener les morceaux tel que "#TITRE #TITRE"
 * = "#TITRE" + " " + "#TITRE" (3 morceaux)
 *
 * Concatenation sans separateur: verifier qu'on ne cree pas de faux lexemes
 * 
**/ 
function format_suite_html_geshi ($args)
{
	for($i=0; $i < count($args)-1; $i++) {
		list($texte, $type) = $args[$i];
		list($texte2, $type2) = $args[$i+1];
		if (!$texte OR !$texte2) continue; 
		$c1 = substr($texte,-1);
		if ($type2 !== 'texte') {
		  // si un texte se termine par ( et est suivi d'un champ
		  // ou assimiles, forcer la notation pleine
			if ($c1 == '(' AND substr($texte2,0,1) == '#')
				$args[$i+1][0] = _format_suite_html_geshi_notation_balise_pleine($texte2);
		} else {
			if ($type == 'texte') continue;
			// si un champ ou assimiles est suivi d'un texte
			// et si celui-ci commence par un caractere de champ
			// forcer la notation pleine
			if (($c1 == '}' AND substr(ltrim($texte2),0,1) == '|')
			OR (preg_match('/[\w\d_*]/', $c1) AND preg_match('/^[\w\d_*{|]/', $texte2)))
				$args[$i][0] = _format_suite_html_geshi_notation_balise_pleine($texte);
		}
	}
	return join("", array_map('array_shift', $args));
}


// Raccourci pour ajouter [( et )] sur une balise
function _format_suite_html_geshi_notation_balise_pleine($balise) {
	return
		  format_geshi_spip("[", 'balise_crochet')
		. format_geshi_spip("(", 'balise_parenthese')
		. $balise
		. format_geshi_spip(")", 'balise_parenthese')
		. format_geshi_spip("]", 'balise_crochet');
}


/**
 * Du texte 
 *
**/
function format_texte_html_geshi ($texte)
{
	if (is_numeric($texte)) {
		return format_geshi_spip($texte, 'nombre');
	} else {
		return geshi_hsc($texte);
	}
}

?>
