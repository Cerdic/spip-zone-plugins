<?php
/*************************************************************************************
 * xml.php
 * -------
 * Author: Nigel McNie (oracle.shinoda@gmail.com)
 * Copyright: (c) 2004 Nigel McNie (http://qbnz.com/highlighter/)
 * Release Version: 1.0.7.6
 * CVS Revision Version: $Revision: 1.10 $
 * Date Started: 2004/09/01
 * Last Modified: $Date: 2005/12/30 04:52:10 $
 *
 * XML language file for GeSHi. Based on the idea/file by Christian Weiske
 *
 * CHANGES
 * -------
 * 2005/12/28 (1.0.2)
 *   -  Removed escape character for strings
 * 2004/11/27 (1.0.1)
 *   -  Added support for multiple object splitters
 * 2004/10/27 (1.0.0)
 *   -  First Release
 *
 * TODO (updated 2004/11/27)
 * -------------------------
 * * Check regexps work and correctly highlight XML stuff and nothing else
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
@define('REG_NOM_TABLE_BOUCLE', '\([^)]*\)');

// criteres | arguments : {critere > 0} {critere = #ENV{truc}}
// reste un bug sur : {#ENV{truc}, #TITO}
@define('REG_CRITERES', '\{(\s*(?:([^\{\}]+)|(?R))*\s*)\}');

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
@define('REG_FIN_BOUCLE', '\/?&gt;');
@define('REG_FIN_BOUCLE_TROUVE', REG_REMPLACEMENTS_GESHI_START . REG_FIN_BOUCLE . REG_REMPLACEMENTS_GESHI_END);
// 2) trouver le debut <BOUCLEx
@define('REG_DEBUT_BOUCLE', '&lt;BOUCLE' . REG_NOM_BOUCLE);
@define('REG_DEBUT_BOUCLE_TROUVE', REG_REMPLACEMENTS_GESHI_START . REG_DEBUT_BOUCLE . REG_REMPLACEMENTS_GESHI_END);
// 3) trouver la table (TABLE)
@define('REG_TABLE_BOUCLE_TROUVE', REG_REMPLACEMENTS_GESHI_START . REG_NOM_TABLE_BOUCLE . REG_REMPLACEMENTS_GESHI_END);
// 4) trouver les criteres

// 1) <BOUCLEx(TABLE){criteres} /> ( la fin /> )
@define('REG_BOUCLE_FIN', '((?:' . REG_DEBUT_BOUCLE . ')(?:' . REG_NOM_TABLE_BOUCLE . ')'
	// criteres + fin de boucle
	. '(?:(?:\s*' . REG_CRITERES . '\s*)*))(' . REG_FIN_BOUCLE . ')');

// 2) <BOUCLEx(TABLE){criteres} @@/>@@ ( le debut <BOUCLEx )
@define('REG_BOUCLE_DEBUT','(' . REG_DEBUT_BOUCLE . ')((?:' . REG_NOM_TABLE_BOUCLE . ')'
	// criteres + fin de boucle
	. '(?:(?:\s*' . REG_CRITERES . '\s*)*)(?:' . REG_FIN_BOUCLE_TROUVE . '))');

// 3) @@<BOUCLEx@@ (TABLE){criteres} @@/>@@ ( la table (TABLE) )
@define('REG_BOUCLE_TABLE','(' . REG_DEBUT_BOUCLE_TROUVE . ')(' . REG_NOM_TABLE_BOUCLE . ')'
	// criteres + fin de boucle
	. '((?:(?:\s*' . REG_CRITERES . '\s*)*)(?:' . REG_FIN_BOUCLE_TROUVE . '))');

// 4) @@<BOUCLEx@@ @@(TABLE)@@ {criteres} @@/>@@ ( des criteres {criteres} )
@define('REG_BOUCLE_CRITERES','((?:' . REG_DEBUT_BOUCLE_TROUVE . ')(?:' . REG_TABLE_BOUCLE_TROUVE . '))'
	// criteres + fin de boucle
	. '((?:\s*' . REG_CRITERES . '\s*)*)(' . REG_FIN_BOUCLE_TROUVE . ')');


// <INCLURE
@define('REG_INCLURE','(&lt;INCLU(D|R)E)(\([^)]*\))?(.*)?(&gt;)');

// |filtre |class::methode
@define('REG_NOM_FILTRE', '(<PIPE>[a-z_=!<>?][a-z0-9_=]*(::[a-z0-9_]*)?)');

// #BALISE
@define('REG_BALISE','(\#)(' . REG_NOM_BOUCLE . ':)?([A-Z0-9_]+)([*]{0,2})');

// /!\ pas encore au point
@define('REG_BALISE_COMPLET_START', '((\[)[^\[]*(\())'); // [ ... (
@define('REG_BALISE_COMPLET_STOP',  '((\))[^\]]*(\]))'); // ) ... ]
@define('REG_BALISE_COMPLET',
	  REG_BALISE_COMPLET_START // [ ... (
	. '(' . REG_BALISE . ')' // #BALISE 
	. '(?:' . REG_NOM_FILTRE . '?' . REG_CRITERES . '?)*' // {arguments} |filtre{criteres}
	. REG_BALISE_COMPLET_STOP); // ) ... ]


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
			0 => 'color: #E1861A;', // balise (#nom:TITRE**) ** (meme couleur que 50:filtre)
			1 => 'color: #CA5200;', // balise (#nom:TITRE) #TITRE
			2 => 'color: #e72;', // balise (#nom:TITRE) nom:

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
			50 => 'color: #E1861A;', // filtres de balise
			)
		),
	'URLS' => array(),
	'OOLANG' => false,
	'OBJECT_SPLITTERS' => array(),

	'REGEXPS' => array(
/*
		// Balise complexe avec [ ( et ) ] si on peut
		99 => array(
			GESHI_SEARCH => REG_BALISE_COMPLET,
			GESHI_REPLACE => '',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
*/
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


		// Au fur et a mesure que GESHI trouve des regexp
		// il encadre ses trouvailles de <|!REG3XPn!>contenu|>
		// tel que <|!REG3XP10!>(ARTICLES)|>
		
		// 1) fin de boucle <BOUCLEx(TABLE).../> ( /> )
		10 => array(
			GESHI_SEARCH => REG_BOUCLE_FIN,
			GESHI_REPLACE => '\\4',
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
		13 => array(
			GESHI_SEARCH => REG_BOUCLE_CRITERES,
			GESHI_REPLACE => '\\2',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1',
			GESHI_AFTER => '\\5'
			),



		// boucle simple <Bx>
		15 => array(
			GESHI_SEARCH => REG_BOUCLE_SIMPLE,
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),



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

		// parametres de filtre, balise
		40 => array(
			GESHI_SEARCH => '(' . REG_CRITERES . '?)',
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),


		// filtre
		50 => array(
			GESHI_SEARCH => REG_NOM_FILTRE,
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => 'i',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
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
