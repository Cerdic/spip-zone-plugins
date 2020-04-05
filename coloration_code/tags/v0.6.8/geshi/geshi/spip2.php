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

@define('REG_NOM_BOUCLE', '[a-zA-Z0-9_]*');
@define('REG_BOUCLE','(&lt;\/?\/?B(OUCLE)?' . REG_NOM_BOUCLE . ')(\([^)]*\))?\s*({.*})?\s*(&gt;)');
@define('REG_INCLURE','(&lt;INCLU(D|R)E)(\([^)]*\))?(.*)?(&gt;)');
@define('REG_BALISE','(\#)(' . REG_NOM_BOUCLE . ':)?([A-Z0-9_]+)([*]{0,2})');
@define('REG_NOM_FILTRE', '(<PIPE>[a-z_=!<>?][a-z0-9_=]*(::[a-z0-9_]*)?)');


$language_data = array (
	'LANG_NAME' => 'XML',
	'COMMENT_SINGLE' => array(),
	'COMMENT_MULTI' => array('<!--' => '-->'),
	'CASE_KEYWORDS' => GESHI_CAPS_NO_CHANGE,
	'QUOTEMARKS' => array(),
	'ESCAPE_CHAR' => '',
	'KEYWORDS' => array(
		),
	'SYMBOLS' => array(
		),
	'CASE_SENSITIVE' => array(
		GESHI_COMMENTS => false,
		),
	'STYLES' => array(
		'KEYWORDS' => array(
			),
		'COMMENTS' => array(
			'MULTI' => 'color: #808080; font-style: italic;'
			),
		'ESCAPE_CHAR' => array(
			0 => 'color: #000099; font-weight: bold;'
			),
		'BRACKETS' => array(
			),
		'STRINGS' => array(
			),
		'NUMBERS' => array(
			),
		'METHODS' => array(
			),
		'SYMBOLS' => array(
			),
        'SCRIPT' => array(

            ),
		'REGEXPS' => array(
			0 => 'color: #E1861A;', // balise (#nom:TITRE**) ** (meme couleur que 50:filtre)
			1 => 'color: #CA5200;', // balise (#nom:TITRE) #TITRE
			2 => 'color: #e72;', // balise (#nom:TITRE) nom:

			10 => 'color: #527EE0;', // tables boucle
            11 => 'color: #222;', // debut boucle
            12 => 'color: #745E4B;', // criteres boucles 
            13 => 'color: #222;', // fin boucle
						
            
			
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
	'URLS' => array(
		),
	'OOLANG' => false,
	'OBJECT_SPLITTERS' => array(
		),

	'REGEXPS' => array(
		// Balise (#nom:TITRE**)
		0 => array(
			GESHI_SEARCH => REG_BALISE,
			GESHI_REPLACE => '\\4',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1\\2\\3',
			GESHI_AFTER => ''
			),
		// Balise (#nom:TITRE)
		1 => array(
			GESHI_SEARCH => REG_BALISE,
			GESHI_REPLACE => '\\1\\2\\3',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		// Balise (nom:)
		2 => array(
			GESHI_SEARCH => REG_BALISE,
			GESHI_REPLACE => '\\2',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1',
			GESHI_AFTER => '\\3'
			),

		// table de la boucle
		10 => array(
			GESHI_SEARCH => REG_BOUCLE,
			GESHI_REPLACE => '\\3',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1',
			GESHI_AFTER => '\\4\\5'
			),		
		// debut de boucle
		11 => array(
			GESHI_SEARCH => REG_BOUCLE,
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '',
			GESHI_AFTER => '\\3\\4\\5'
			),	
		// criteres de boucle
		12 => array(
			GESHI_SEARCH => REG_BOUCLE,
			GESHI_REPLACE => '\\4',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1\\3',
			GESHI_AFTER => '\\5'
			),
		// fin de boucle
		13 => array(
			GESHI_SEARCH => REG_BOUCLE,
			GESHI_REPLACE => '\\5',
			GESHI_MODIFIERS => '',
			GESHI_BEFORE => '\\1\\3\\4',
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

		// critere de boucle ou de filtre
		40 => array(
			GESHI_SEARCH => '(\{(\s*(?:([^{}]+)|(?R))*)\s*\}?)',
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
