<?php
/*************************************************************************************
 * spip3.php
 * -------
 * Author: Nigel McNie (oracle.shinoda@gmail.com)
 * Copyright: (c) 2004 Nigel McNie (http://qbnz.com/highlighter/)
 * Release Version: 1.0.8.10
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


/**
 * Partager les styles avec public/format_html_geshi.php 
**/
if (!function_exists('geshi_language_data_spip3_styles')) {
function geshi_language_data_spip3_styles($export_for_keywords = false) {
	$kws = array(
		'KEYWORDS' => array(
			10 => 'color: #1D91DD;', // <boucle>
			11 => 'color: #527EE0;', // (TABLE)
			12 => 'color: #984CFF;', // {criteres}

			20 => 'color: #D05000;', // #BALISE
			21 => 'color: #1DA3DD;', // #x:BALISE (x:)
			22 => 'color: #FF4E00;', // #BALISE** (**)
			
			25 => 'color: #D05000;', // [ de [(#BALISE)]
			26 => 'color: #D05000;', // ( de [(#BALISE)]

			30 => 'color: #FF851D;', // |filtre
			31 => 'color: #74B900;', // {parametre}
			32 => 'color: #74B900;', // {parametre, x} (parametre et x)

			40 => 'color: #EB2D2B;', // <INCLURE />
			41 => 'color: #6B3635;', // <INCLURE(fichier) /> fichier

			50 => 'color: #9B7400;', // polyglotte <multi>
			51 => 'color: #9B7400;', // polyglotte [en]
			52 => 'color: #666;', // polyglotte contenu

			60 => 'color: #9B7400;', // <: idiome :> (<: :>)
			61 => 'color: #9B7400;', // <: module : idiome :> (module)
			62 => 'color: #C90;', // <: chaine :> (chaine)

			70 => 'color: #FF67BF; font-weight:bold;', // operateurs =

			80 => '', // nombre 12
		),
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
		'REGEXPS' => array()
	);

	if (!$export_for_keywords) {
		return $kws;
	}

	// un bazar pour les classes css
	// pour avoir un tableau avec toutes les cles de keywords
	// valides (meme si le mot est lui non trouve)
	// afin que geshi ajoute les classes CSS / styles dans la feuille de style
	// generee.
	$keywords = array();
	foreach ($kws['KEYWORDS'] as $k=>$null) {
		$keywords[$k] = array('@#@#@'); // un mot qu'on ne rencontrera pas.
	}
	return $keywords;
}
}



$language_data = array (
	'LANG_NAME' => 'SPIP',
	'COMMENT_SINGLE' => array(),
	'COMMENT_MULTI' => array(),
	'CASE_KEYWORDS' => GESHI_CAPS_NO_CHANGE,

	// fonction d'analyse du code a colorier
	'SPIP_GESHI_COLOR_FUNCTION' => 'geshi_spip3_colorier',
	'PARSER_CONTROL' => array(
		'ENABLE_FLAGS' => array(
			'BRACKETS' => GESHI_NEVER, // le colorieur s'occupera des [](){} !
			'NUMBERS'  => GESHI_NEVER, // le colorieur s'occupera des 123 (ou pas !)
			#'KEYWORDS' => GESHI_NEVER, // il les faut pour les classes css :(
	)),

	'QUOTEMARKS' => array(),
	'ESCAPE_CHAR' => '',
	// pour que les classes CSS s'ajoutent :(
	// lorsque qu'on les demande avec define('PLUGIN_COLORATION_CODE_STYLES_INLINE', false);
	'KEYWORDS' => geshi_language_data_spip3_styles(true),
	'SYMBOLS' => array(),
	'CASE_SENSITIVE' => array(
		GESHI_COMMENTS => false,
		),
	'STYLES' => geshi_language_data_spip3_styles(),
	'URLS' => array(),
	'OOLANG' => false,
	'OBJECT_SPLITTERS' => array(),

	'REGEXPS' => array(),

	'STRICT_MODE_APPLIES' => GESHI_NEVER,
	'SCRIPT_DELIMITERS' => array(),

	'HIGHLIGHT_STRICT_BLOCK' => array()
);


/**
 * Colorie un code au format SPIP (extension html)
 *
 * @param string $contenu
 * 		Le code a colorier
 * @return string
 * 		Le code modifie en ajoutant des <| class="br0">xxx|>
 * 		Indiquant les classes utilisees pour colorier les morceaux de code.
**/
if (!function_exists('geshi_spip3_colorier')) {
function geshi_spip3_colorier($contenu) {

	// phraser le squelette en question
	// on obtient alors un arbre de syntaxe abstraite
	$boucles = array();
	$descr = array(
		'nom' => 'geshi_spip3',
		'gram' => 'html',
		'sourcefile' => '',
		'squelette' => ''
	);

	$squelette = $contenu; // code a traiter

	// rendre inertes les echappements de #[](){}<>
	$i = 0;
	while(false !== strpos($squelette, $inerte = '-INERTE'.$i)) $i++;
	$squelette = preg_replace_callback(',\\\\([#[()\]{}<>]),',
		create_function('$a', "return '$inerte-'.ord(\$a[1]).'-';"), $squelette, -1, $esc);


	$phraser = charger_fonction('phraser_html', 'public');
	$boucles = $phraser($squelette, '', $boucles, $descr);

	// decompiler l'arbre pour obtenir une nouvelle syntaxe concrete
	// dans la grammaire choisie
	include_spip('public/decompiler');
	$contenu = public_decompiler($boucles, 'html_geshi');

	// restituer les echappements
	if ($esc) {
		$contenu = preg_replace_callback(",$inerte-(\d+)-,", create_function('$a', 'return "\\\\" . chr($a[1]);'), $contenu);
	}

	return $contenu;
}
}

?>
