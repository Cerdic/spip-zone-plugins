<?php
/*************************************************************************************
 * spip.php
 * -------
 * Author: Collectif SPIP
 * Copyright: (c) 2004 Nigel McNie (http://qbnz.com/highlighter/)
 * Release Version: 1.0.7.6
 * CVS Revision Version: $Revision: 1.10 $
 * 
 *************************************************************************************
 *
 *     This file is part of GeSHi.
 *
 *   GeSHi is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   GeSHi is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with GeSHi; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ************************************************************************************/


// nom de la boucle x dans <BOUCLEx>
@define('REG_NOM_BOUCLE', '[a-zA-Z0-9_]+');

// table d'une boucle (TABLE) ou (connect:TABLE)
@define('REG_NOM_TABLE_BOUCLE', '\([^)]+\)');

// criteres | arguments : {critere > 0} {critere = #ENV{truc}}
@define('REG_CRITERES',           '\{(?:\s*(?:(?>[^{}]+)|(?R))*\s*)\}');
// la meme chose sans rien de capturant
@define('REG_CRITERES_TOUT',      '\{(?:\s*(?:(?>[^{}]+)|(?R))*\s*)\}');
// la meme chose en capturant {, contenu et } en 3 morceaux
@define('REG_CRITERES_ACCOLADES', '(\{)(\s*(?:(?>[^{}]+)|(?R))*\s*)(\})');

// Remplacements de Regexp par GESHI
// A chaque fois dans le tableau REGEXPS que geshi trouve quelque chose,
// il l'encadre de <|!REG3EXPn!>contenu|>
// ou n est la cle de regexp et contenu ce qui est dans "geshi_replace"
@define('REG_REMPLACEMENTS_GESHI_START', '<\|!REG3XP[0-9]+!>');
@define('REG_REMPLACEMENTS_GESHI_END', '\|>');
@define('REG_REMPLACEMENTS_GESHI', REG_REMPLACEMENTS_GESHI_START . '.*' . REG_REMPLACEMENTS_GESHI_END);

// <Bx> </Bx> <//Bx> </BOUCLEx>
@define('REG_BOUCLE_SIMPLE','(&lt;\/?\/?B(OUCLE)?' . REG_NOM_BOUCLE . '\s*&gt;)');

// Calcul des <BOUCLEx(){} />. C'est complexe
// 1) trouver la fin   />
@define('REG_FIN_BOUCLE', '\s*\/?&gt;');
@define('REG_FIN_BOUCLE_TROUVE', REG_REMPLACEMENTS_GESHI_START . REG_FIN_BOUCLE . REG_REMPLACEMENTS_GESHI_END);
// 2) trouver le debut <BOUCLEx
@define('REG_DEBUT_BOUCLE', '&lt;BOUCLE' . REG_NOM_BOUCLE);
@define('REG_DEBUT_BOUCLE_TROUVE', REG_REMPLACEMENTS_GESHI_START . REG_DEBUT_BOUCLE . REG_REMPLACEMENTS_GESHI_END);
// 3) trouver la table (TABLE)
@define('REG_TABLE_BOUCLE_TROUVE', REG_REMPLACEMENTS_GESHI_START . REG_NOM_TABLE_BOUCLE . REG_REMPLACEMENTS_GESHI_END);
// 4) trouver les criteres


// Aide pour les reg de boucle
// ?R est applique sur l'expression complete.
// on indique donc la parenthese que l'on souhaite avec ?x
// sinon ca ne fonctionne pas (des qu'un critere a d'autres accolades internes.)
@define('REG_CRITERES_BOUCLE', '(?:(\s*' . REG_CRITERES . '\s*)*)'); // 1 parenthese capturante pour la recursion

// 1) <BOUCLEx(TABLE){criteres} /> ( la fin /> )
@define('REG_BOUCLE_FIN', '((?:' . REG_DEBUT_BOUCLE . ')(?:' . REG_NOM_TABLE_BOUCLE . ')'
	// criteres + fin de boucle
	. str_replace('?R', '?2', REG_CRITERES_BOUCLE) . ')(' . REG_FIN_BOUCLE . ')');

// 2) <BOUCLEx(TABLE){criteres} @@/>@@ ( le debut <BOUCLEx )
@define('REG_BOUCLE_DEBUT','(' . REG_DEBUT_BOUCLE . ')((?:' . REG_NOM_TABLE_BOUCLE . ')'
	// criteres + fin de boucle
	. str_replace('?R', '?3', REG_CRITERES_BOUCLE) . '(?:' . REG_FIN_BOUCLE_TROUVE . '))');

// 3) @@<BOUCLEx@@ (TABLE){criteres} @@/>@@ ( la table (TABLE) )
@define('REG_BOUCLE_TABLE','(' . REG_DEBUT_BOUCLE_TROUVE . ')(' . REG_NOM_TABLE_BOUCLE . ')' . '('
	// criteres + fin de boucle
	.  str_replace('?R', '?4', REG_CRITERES_BOUCLE) . '(?:' . REG_FIN_BOUCLE_TROUVE . '))');

// 4) @@<BOUCLEx@@ @@(TABLE)@@ {criteres} @@/>@@ ( des criteres {criteres} )
@define('REG_BOUCLE_CRITERES','((?:' . REG_DEBUT_BOUCLE_TROUVE . ')(?:' . REG_TABLE_BOUCLE_TROUVE . '))'
	// criteres + fin de boucle
	. '(' . str_replace('?R', '?3', REG_CRITERES_BOUCLE) . ')(' . REG_FIN_BOUCLE_TROUVE . ')');


// <INCLURE
@define('REG_INCLURE','(&lt;INCLU(D|R)E)(\([^)]*\))?(.*)?(&gt;)');

// Ce qui suit un filtre d'operation tel que |> |< |== |? ...
// doit toujours etre une { ou equivalent ( la capture d'un parametre (REGEXP40) )
// On le teste pour eviter de prendre | <style> par exemple
// Mais certaines versions de PCRE (7.6 ko, 8.12 ok) plantent sur cette recherche de suite,
// donc on ne le fait pas pour ces vieilles versions
if (version_compare(current(explode(' ', PCRE_VERSION, 2)), '7.7', '>=')) {
	@define('REG_SUITE_FILTRE', '(?={|<\|!REG3XP40!>)');
} else {
	@define('REG_SUITE_FILTRE', '');
}

// |filtre |class::methode
// et |>= |?
@define('REG_NOM_FILTRE', '((?:<PIPE>\s*[a-z_][a-z0-9_=]*(::[a-z0-9_]*)?)'
		// |< et consoeurs sont toujours suivis par une accolade ouvrante {
		. '|(?:<PIPE>\s*(?:&gt;=?|&lt;=?|&lt;&gt;|===?|!==?|\?)(?:\s*)'
		. REG_SUITE_FILTRE
		. '))');
// la meme chose, mais sans etre capturant.
@define('REG_NOM_FILTRE_TOUT', '(?:(?:<PIPE>\s*[a-z_][a-z0-9_=]*(?:::[a-z0-9_]*)?)'
		. '|(?:<PIPE>\s*(?:&gt;=?|&lt;=?|&lt;&gt;|===?|!==?|\?)(?:\s*)'
		. REG_SUITE_FILTRE
		. '))');

// #BALISE
@define('REG_BALISE','(\#)(' . REG_NOM_BOUCLE . ':)?([A-Z0-9_]+)([*]{0,2})');
// la meme chose, mais sans etre capturant.
@define('REG_BALISE_TOUT','(?:\#)(?:' . REG_NOM_BOUCLE . ':)?(?:[A-Z0-9_]+)(?:[*]{0,2})');



// Recherche des parties exterieures des balises.
// On ne peut pas avoir plus d'une seule recursivite
/* @define('REG_BALISE_EXTERIEUR', '(?:\s*(?:(?>[^\[\]]+)|(?R))*\s*)'); */ 
// du coup on ne peut attraper que les parties exterieures
// qui n'ont pas de crochets internes.
@define('REG_BALISE_EXTERIEUR', '(?:[^\[\]]*)');
@define('REG_BALISE_COMPLET_START', '(\[)(' . REG_BALISE_EXTERIEUR . ')(\()'); // [ ... (
@define('REG_BALISE_COMPLET_STOP',  '(\))(' . REG_BALISE_EXTERIEUR . ')(\])'); // ) ... ]
// Attention a ne pas avoir plus de 9 parentheses capturantes car on ne peut pas ecrire \\10
@define('REG_BALISE_COMPLET',
	  REG_BALISE_COMPLET_START . '(' // [ ... (
	. '(?:' . REG_BALISE_TOUT . ')' // #BALISE
	 // {arguments} |filtre{criteres}
	 // *+ pour diminuer le nombre de pcre.backtrace_limit
	. '(?:(\s*' . REG_NOM_FILTRE_TOUT . '?' . str_replace('?R', '?5', REG_CRITERES_BOUCLE) . '?)*+)'
	 // ) ... ]
	. ')' . REG_BALISE_COMPLET_STOP );




if (!function_exists('spip2_geshi_regexp_critere_callback')) {
/**
 * Cette fonction recupere la liste des criteres de boucle trouves
 * "  {critere > 0} {critere=#ENV{tom}}{critere} "
 * Elle echappe tous les { et } appartenants au criteres
 * en les remplacant par <CRITERE> et </CRITERE>. Ils seront
 * remis apres le calcul les arguments de balises / filtres.
 * 
 * Ceci est simplement la pour pouvoir distinguer filtre et critere
 * dans la coloration.
 *
 * @param array $matches
 * 		Le resultat du preg_match
 * @param Object $geshi
 * 		Instance de l'objet SPIP_GESHI (issu de GESHI)
 * @return string
 * 		La chaine attendu par Geshi
 * 		qui est quelque chose comme
 * 		"avant <|!REG3XP31!>contenu|> apres"
**/
function spip2_geshi_regexp_critere_callback($matches, $geshi) {
	$key = $geshi->_hmr_key;
	// 0 = tout
	// 1 = <BOUCLEx(TABLE)
	// 2 = {critere}{critere}
	// 3 = />
	// L'entree 2 contient les criteres a echapper, et il ne faut pas oublier aussi les espaces possibles.
	$criteres = $matches[2];
	if (preg_match_all('/(\s*)' . REG_CRITERES_ACCOLADES . '(\s*)/s', $criteres, $crits, PREG_SET_ORDER)) {
		$criteres = "";
		foreach ($crits as $c) {
			$criteres .=
				  $c[1] // espace
				. "<CRITERE>" // {
				. $c[3] // critere
				. "</CRITERE>" // }
				. $c[5]; // espace
		}
	}

	$retour = $matches[1] . '<|!REG3XP' . $key .'!>'
		. str_replace("\n", "|>\n<|!REG3XP" . $key . '!>', $criteres)
		. '|>' . $matches[4];
	return $retour;
}




/**
 * Cette fonction recupere la liste des balises qui
 * ont des parties optionnelles [ ...( et ) ... ]
 * 
 * Elle colorie les signes [()]
 * Elle les echappe egalement.
 * Ainsi, on peut relancer la regexp pour tenter de trouver des enchainements
 * de balise plus profonds.
 *
 * @param array $matches
 * 		Le resultat du preg_match
 * @param Object $geshi
 * 		Instance de l'objet SPIP_GESHI (issu de GESHI)
 * @return string
 * 		La chaine attendu par Geshi
 * 		qui est quelque chose comme
 * 		"avant <|!REG3XP31!>contenu|> apres"
**/
function spip2_geshi_regexp_balise_callback($matches, $geshi) {
	$key = $geshi->_hmr_key;
	// on l'appelle plusieurs fois mais on colorie toujours avec la meme cle.
	$key = 4; 
	// 0 = tout
	// 1 = [
	// 2 = avant
	// 3 = (
	// 4 = #BALISE|filtre{x}
	// 5 =
	// 6 =
	// 7 = )
	// 8 = apres
	// 9 = ]
	// -INERTE=x= sera remplace ensuite par le vrai caractere.
	$retour =
		  '<|!REG3XP' . $key .'!>-INERTE=' . ord('[') . '=|>' // [
		. $matches[2] // avant
		. '<|!REG3XP' . $key .'!>-INERTE=' . ord('(') . '=|>' // (
		. $matches[4] // balise
		. '<|!REG3XP' . $key .'!>-INERTE=' . ord(')') . '=|>' // )
		. $matches[8] // apres
		. '<|!REG3XP' . $key .'!>-INERTE=' . ord(']') . '=|>' // ]
		;

	return $retour;
}



/**
 * Echapper les echappements \[ \] ...
 * Attention a < et > qui arrivent avec &lt; et &gt;
 *
**/
function spip2_geshi_regexp_echappements_echapper_callback($matches, $geshi) {
	$squelette = $matches[0];

	// rendre inertes les echappements de #[](){}<>
	$inerte = '-INERTE';
	// mais eviter d'echapper \<PIPE>, <PIPE> etant deja un echappement de Geshi
	// normalement les < et > sont justement aussi echapes en &gt et &lt
	// donc on ne cherche pas la capture de \< ou \>
	$squelette = preg_replace_callback(',\\\\([#[()\]{}]|&gt;|&lt;),',
		create_function('$a', "return '$inerte-'.ord(html_entity_decode(\$a[1])).'-';"), $squelette, -1, $esc);

	return $squelette;
}

/**
 * Remettre les echappements \[ \] ...
 * 
 * Remettre les [ ( ) ] echappes des balises
 * Attention a < et > qui doivent repartir avec &lt; et &gt;
 *
**/
function spip2_geshi_regexp_echappements_remettre_callback($matches, $geshi) {
	$contenu = $matches[0];
	$inerte = '-INERTE';
	$key = $geshi->_hmr_key;
	// echappements avec \
	$contenu = preg_replace_callback(",$inerte-(\d+)-,",
		#create_function('$a', 'return "\\\\" . chr($a[1]);'), $contenu);
		create_function('$a', 'return "<|!REG3XP'.$key.'!>\\\\" . htmlspecialchars(chr($a[1])) . "|>";'), $contenu);

	// echappements de balise faits par une regexp de ce colorieur (regexp 4 à 7).
	$contenu = preg_replace_callback(",$inerte=(\d+)=,",
		#create_function('$a', 'return "\\\\" . chr($a[1]);'), $contenu);
		create_function('$a', 'return chr($a[1]);'), $contenu);

	return $contenu;
}




} // /if



$language_data = array (
	'LANG_NAME' => 'XML',
	'COMMENT_SINGLE' => array(),
	'COMMENT_MULTI' => array('<!--' => '-->'),
	'CASE_KEYWORDS' => GESHI_CAPS_NO_CHANGE,
	'QUOTEMARKS' => array(),
	'ESCAPE_CHAR' => '',
	'KEYWORDS' => array(),
	'SYMBOLS' => array(),
	'CASE_SENSITIVE' => array(
		GESHI_COMMENTS => false,
		),
	'PARSER_CONTROL' => array(
		'ENABLE_FLAGS' => array(
			'BRACKETS' => GESHI_NEVER, // le colorieur s'occupera des [](){} !
			'NUMBERS'  => GESHI_NEVER, // le colorieur s'occupera des 123 (ou pas !)
			#'KEYWORDS' => GESHI_NEVER, // il les faut pour les classes css :(
	)),
	'STYLES' => array(
		'KEYWORDS' => array(),
		'COMMENTS' => array(
			'MULTI' => 'color: #808080; font-style: italic;'
			),
		'ESCAPE_CHAR' => array(
			0 => 'color: #000099; font-weight: bold;'
			),
		'BRACKETS' => array(),
		'STRINGS' => array(),
		'NUMBERS' => array(),
		'METHODS' => array(),
		'SYMBOLS' => array(),
		'SCRIPT' => array(),
		'REGEXPS' => array(
			0 => 'color: #FF4E00;', // balise (#nom:TITRE**) ** (meme couleur que 50:filtre)
			1 => 'color: #D05000;', // balise (#nom:TITRE) #TITRE
			2 => 'color: #1DA3DD;', // balise (#nom:TITRE) nom:
			4 => 'color: #D05000;', // [ ( et ) ]

			10 => 'color: #1DA3DD;', // fin boucle
			11 => 'color: #1DA3DD;', // debut boucle
			12 => 'color: #527EE0;', // tables boucle
			13 => 'color: #984CFF;', // criteres boucles 
			15 => 'color: #1DA3DD;', // boucle simple <Bx> </Bx> ...

			20 => 'color: #527EE0;', // inclure entre parenthese
			21 => 'color: #222', // inclure debut
			22 => 'color: #745E4B;', // inclure criteres
			23 => 'color: #222;', // inclure fin
			
			30 => 'color: #C90', // idiome (chaine de langue)
			31 => 'color: #C90', // multi 
			
			40 => 'color: #74B900;', // critere de boucle ou de balise 
			50 => 'color: #FF851D;', // filtres de balise

			// echappements
			101 => '', // \[ \] \{ ...
			102 => 'color:#FF2100; font-weight:bold;', // les \ dans les echappements

			// autres (eviter des notices php)
			100  => '',
			41   => '',
			'4b' => '',
			'4c' => '',
			'4d' => '',
			'4e' => '',
			)
		),
	'URLS' => array(),
	'OOLANG' => false,
	'OBJECT_SPLITTERS' => array(),

	'REGEXPS' => array(
		// echapper les echappements
		100 => array(
			GESHI_SEARCH => '^.*$',
			SPIP_GESHI_REGEXP_FUNCTION => 'spip2_geshi_regexp_echappements_echapper_callback',
			GESHI_MODIFIERS => 's',
			// eviter des notices PHP
			GESHI_REPLACE => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),


		#######  BALISES  ########

		// Balise complexe avec [ ( et ) ] si on peut
		// Colorie les [()] et les echappe lorsqu'il en trouve.
		// on l'applique plusieurs fois pour tenter de capturer un plus grand nombre
		// lorsqu'on a des imbrications comme [ [(#TITRE)] (#ENV) apres ]
		// car on ne peut pas effectuer d'autre recursion
		// dans cette regexp qui en possede deja une pour les criteres
		4 => array(
			GESHI_SEARCH => REG_BALISE_COMPLET,
			SPIP_GESHI_REGEXP_FUNCTION => 'spip2_geshi_regexp_balise_callback',
			GESHI_MODIFIERS => '',
			// eviter des notices PHP
			GESHI_REPLACE => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		'4b' => array(
			GESHI_SEARCH => REG_BALISE_COMPLET,
			SPIP_GESHI_REGEXP_FUNCTION => 'spip2_geshi_regexp_balise_callback',
			GESHI_MODIFIERS => '',
			// eviter des notices PHP
			GESHI_REPLACE => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		'4c' => array(
			GESHI_SEARCH => REG_BALISE_COMPLET,
			SPIP_GESHI_REGEXP_FUNCTION => 'spip2_geshi_regexp_balise_callback',
			GESHI_MODIFIERS => '',
			// eviter des notices PHP
			GESHI_REPLACE => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		'4d' => array(
			GESHI_SEARCH => REG_BALISE_COMPLET,
			SPIP_GESHI_REGEXP_FUNCTION => 'spip2_geshi_regexp_balise_callback',
			GESHI_MODIFIERS => '',
			// eviter des notices PHP
			GESHI_REPLACE => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		'4e' => array(
			GESHI_SEARCH => REG_BALISE_COMPLET,
			SPIP_GESHI_REGEXP_FUNCTION => 'spip2_geshi_regexp_balise_callback',
			GESHI_MODIFIERS => '',
			// eviter des notices PHP
			GESHI_REPLACE => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),

		// Balise (#nom:TITRE**) (les etoiles)
		0 => array(
			GESHI_SEARCH => REG_BALISE,
			GESHI_REPLACE => '\\4',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1\\2\\3',
			GESHI_AFTER => ''
			),
		// Balise (#nom:TITRE) (l'ensemble hors etoiles)
		1 => array(
			GESHI_SEARCH => REG_BALISE,
			GESHI_REPLACE => '\\1\\2\\3',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		// Balise (nom:) (le connecteur nom:)
		2 => array(
			GESHI_SEARCH => REG_BALISE,
			GESHI_REPLACE => '\\2',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1',
			GESHI_AFTER => '\\3'
			),


		#######  BOUCLES  ########

		// Au fur et a mesure que GESHI trouve des regexp
		// il encadre ses trouvailles de <|!REG3XPn!>contenu|>
		// tel que <|!REG3XP10!>(ARTICLES)|>

		// 1) fin de boucle <BOUCLEx(TABLE).../> ( /> )
		10 => array(
			GESHI_SEARCH => REG_BOUCLE_FIN,
			GESHI_REPLACE => '\\3',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1',
			GESHI_AFTER => ''
			),

		// 2) debut de boucle <BOUCLEx(TABLE).../> ( <BOUCLEx )
		11 => array(
			GESHI_SEARCH => REG_BOUCLE_DEBUT,
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => '\\2'
			),

		// 3) table de la boucle <BOUCLEx(TABLE).../> ( (TABLE) )
		12 => array(
			GESHI_SEARCH => REG_BOUCLE_TABLE,
			GESHI_REPLACE => '\\2',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1',
			GESHI_AFTER => '\\3'
			),

		// criteres de boucle <BOUCLEx(TABLE).../> ( {criteres} )
		// on remplace { et } de chaque critere par <CRITERE> pour
		// que les filtres 40 ne matchent pas les criteres
		// 41 les remets.
		13 => array(
			GESHI_SEARCH => REG_BOUCLE_CRITERES,
			SPIP_GESHI_REGEXP_FUNCTION => 'spip2_geshi_regexp_critere_callback',
			GESHI_MODIFIERS => '',
			// eviter des notices PHP
			GESHI_REPLACE => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),



		// boucle simple <Bx>
		15 => array(
			GESHI_SEARCH => REG_BOUCLE_SIMPLE,
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),


		#######  INCLURE  ########

		// inclure (entre parenthese)
		20 => array(
			GESHI_SEARCH => REG_INCLURE,
			GESHI_REPLACE => '\\3',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1',
			GESHI_AFTER => '\\4\\5'
			),
		// inclure (debut)
		21 => array(
			GESHI_SEARCH => REG_INCLURE,
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => '\\3\\4\\5'
			),
		// inclure (criteres)
		22 => array(
			GESHI_SEARCH => REG_INCLURE,
			GESHI_REPLACE => '\\4',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1\\3',
			GESHI_AFTER => '\\5'
			),
		// inclure (fin)
		23 => array(
			GESHI_SEARCH => REG_INCLURE,
			GESHI_REPLACE => '\\5',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1\\3\\4',
			GESHI_AFTER => ''
			),



		#######  CHAINES DE LANGUE  ########

		// idiome
		30 => array(
			GESHI_SEARCH => '(&lt;:(.*):&gt;)',
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		// multi
		31 => array(
			GESHI_SEARCH => '(&lt;multi&gt;(.*)&lt;\\/multi&gt;)',
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => 'i',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),


		#######  PARAMETRES  ########

		// parametres de filtre, balise
		40 => array(
			GESHI_SEARCH => '(' . REG_CRITERES . ')',
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		// remise des accolades de criteres
		// echappees pour eviter que filtres 40 ne les matchent
		41 => array(
			GESHI_SEARCH => '<CRITERE>(.*)<\/CRITERE>',
			GESHI_REPLACE => '{\\1}',
			GESHI_MODIFIERS => 'Us',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),


		#######  FILTRES  ########

		// filtre
		50 => array(
			GESHI_SEARCH => REG_NOM_FILTRE,
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => 'i',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),


		#######  ECHAPPEMENTS  ########

		// remettre les echappements
		101 => array(
			GESHI_SEARCH => '^.*$',
			SPIP_GESHI_REGEXP_FUNCTION => 'spip2_geshi_regexp_echappements_remettre_callback',
			GESHI_MODIFIERS => 's',
			// eviter des notices PHP
			GESHI_REPLACE => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		// permettre de colorer le \ des echappements differement
		// attention a \&gt; et \&lt; a la place de \> et \< dans geshi
		102 => array(
			GESHI_SEARCH => '(<\|!REG3XP101!>)(.)((?:.|&lt;|&gt;)\|>)',
			GESHI_REPLACE => '\\2',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1',
			GESHI_AFTER => '\\3'
			),
		
		),

	'STRICT_MODE_APPLIES' => GESHI_NEVER,
	'SCRIPT_DELIMITERS' => array(),

	'HIGHLIGHT_STRICT_BLOCK' => array(
		0 => true,
		1 => true,
		2 => true,
		3 => true,
		4 => true,
		5 => true,
		)
);

?>
