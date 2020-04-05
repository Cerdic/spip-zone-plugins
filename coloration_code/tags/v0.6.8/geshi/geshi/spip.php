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
			0 => 'color: #ff0000;',
			1 => 'font-weight: bold; color: black;',
			2 => 'font-weight: bold; color: black;',
			3 => 'color: #66cc66; font-weight: bold;',
			)
		),
	'URLS' => array(
		),
	'OOLANG' => false,
	'OBJECT_SPLITTERS' => array(
		),
	'REGEXPS' => array(
		0 => array(
			GESHI_SEARCH => '(\#[a-z0-9_]*)',
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => 'i',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		1 => array(
			GESHI_SEARCH => '(&lt;\/?\/?B(OUCLE)?[a-z0-9_]*)',
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => 'i',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		2 => array(
			GESHI_SEARCH => '(\([^\)]*\))',
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => 'i',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		3 => array(
			GESHI_SEARCH => '(\{[^\}]*\})',
			GESHI_REPLACE => '\\1',
			GESHI_MODIFIERS => 'i',
			GESHI_BEFORE => '',
			GESHI_AFTER => ''
			),
		),
	'STRICT_MODE_APPLIES' => GESHI_NEVER,
	'SCRIPT_DELIMITERS' => array(	),
	'HIGHLIGHT_STRICT_BLOCK' => array(
		0 => true,
		1 => true,
		2 => true,
		3 => true
		)
);

?>