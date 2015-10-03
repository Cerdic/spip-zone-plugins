<?php
/**********************************************************************************
 * @Subject Read conf. Init default values. Check new value of an option.
 * @package pgn4spip plugin to embed pgn4web Chessboard in a SPIP 2.x or 3.x article
 * @version 2.61.0
 * @copyright copyright (c) 2012 Matt Chesstale 
 * @license GNU General Public License version 3
 * @language PHP
 *
 * @history: Shared module
 * SPIP 3: pgn4spip_fonctions.php, plugins\pgn4spip\formulaires\configurer_pgn4spip.php
 * SPIP 2: pgn4spip_options.php
 *
 * @Design
 * SPIP 3:
 * plugins\pgn4spip\formulaires\configurer_pgn4spip.html Design of the form in SPIP and HTML
 * plugins\pgn4spip\formulaires\configurer_pgn4spip.php Implement "Reinit" and "Save" buttons
 * plugins\pgn4spip\prive\squelettes\contenu\configurer_pgn4spip.html cfg-16.png icon in "Manage plugins"
 * http://www.spip.net/fr_article5414.html
 * http://contrib.spip.net/CFG-comment-s-en-passer
 * Does not need the CFG 3 plugin for SPIP 3. Does not use pgn4spip_options.php
 *
 * SPIP 2:
 * plugins\pgn4spip\fonds\cfg_pgn4spip.html Design of the form in SPIP and HTML for CFG plugin
 * pgn4spip_options.php calls ReadCurrentConfiguration()
 * Need CFG plugin 1.15 or more for SPIP 2
 **********************************************************************************/
if (!defined("_ECRIRE_INC_VERSION")) return; // No direct access allowed to this file

define('DEFAULT_Val', "d"); // (d)efault value
define('TRANSPARENT', 't'); // (t)ransparent color
define('DEFAULT_ss', '26'); // Square Size by default: [26] was 28

// Read the current configuration of the parameters of the plugin
// @out: 	$optValue: default values of options overriden with the current configuration
function ReadCurrentConfiguration(&$optValue)
{
	InitOptionValueByDefault($optValue); // Defined in PATH_check

	$cs = "";				// containerStyle
	$extOpt = "";			// extendedOptions

	if (function_exists('lire_config')) // Read values of plugin parameters in the CFG configuration
	{
		foreach ($optValue as $optShort => $valueByDefault)
		{
			$valueFromConf = lire_config(PLUGIN_Name . '/cfg_' . $optShort, $valueByDefault);
			if (IsOptionValueOk($optShort, $valueFromConf, $valueByDefault))
			{
				$optValue[$optShort] = $valueFromConf;
			}
		}			
		$isManual = lire_config(PLUGIN_Name . '/cfg_isManual');
		if ($isManual != '')
		{
			$optValue['fh'] = lire_config(PLUGIN_Name . '/cfg_height_site', ""); // height of the module
		}
	}
}

// @out: 	$optValue: default values of options
// @return	nothing
function InitOptionValueByDefault(&$optValue)
{
$optValue = array(		// Initial value of each option
'am'	=> 'n',			// 'autoplayMode', 		// [n]one |(g)ame | (l)oop
'bbch'	=> 'E0E0E0',	// 'boardBorderColor',	// [E0E0E0] was 000000
'bch'	=> 'F6F6F6',	// 'backgroundColorHex',// [F6F6F6] was FFFFFF, (t)ransparent to use the parent's background color
'bd'	=> 'c',			// 'buttonsDisplay',	// [c]ustom, (h)idden, (s)tandard
'bsch'	=> TRANSPARENT,	// 'boardShadowColor',	// [t]ransparent no shadow | (b)order | nbr
'cbch'	=> 'F0F0F0',	// 'controlBackgroundColor', // [F0F0F0] was standard buttons
'cd'	=> 'n',			// 'commentsDisplay',	// [n]ewline, (i)nline,(h)idden
'ctch'	=> '696969',	// 'controlTextColor',	// [696969] was 000000
'd'		=> '3000',		// 'delay',				// [3000] was 1000 in ms
'dch'	=> 'E0E0E0',	// 'darkColor',			// [E0E0E0] was C6CEC3
'fcch'	=> '000080',	// 'fontCommentsColor',	// [000080]
'fcs'	=> 'm',			// 'fontCommentsSize',	// [m]oves <- fontMovesSize
'fh'	=> '',			// 'frameHeight',		// [""], (p)age, (b)oard, nbr, overriding textHeight. See FrameHeightEval()
'fhch'	=> '000000',	// 'fontHeaderColor',	// [000000]
'fhs'	=> '14',		// 'fontHeaderSize',	// [14] was 16
'fmch'	=> '000000',	// 'fontMovesColor',	// [000000]
'fms'	=> '14',		// 'fontMovesSize',  	// [14] was 16
'fp'	=> '13',		// 'framePadding',  	// [13] was 0
'fw'	=> 'p',			// 'frameWidth', 		// [p]age, (b)oard, nbr, overriding textHeight
'hch'	=> 'ABABAB',	// 'highlightColor',	// [ABABAB] was DAF4D7
'hd'	=> 'j',			// 'headerDisplay',		// [j]ustified | (h)idden | (c)entered | (l)ive | (v)ariations
'hl'	=> 't',			// 'horizontalLayout',	// [t]rue layout=[h]orizontal, (f)alse layout=(v)ertical
'hm'	=> 'b',			// 'highlightMode',		// [b]order, (s)quare, (n)one
'hmch'	=> 'E0E0E0',	// 'highlightMovesColor', // [E0E0E0] was DAF4D7 | (b)ackground for no highligh
'ig'	=> 'f',			// 'initialGame',		// [f]irst | (l)ast | (r)andom
'ih'	=> 's',			// 'initialHalfmove', 	// [s]tart | (e)nd | (r)andom | (c)omment | (v)ariation | nbr
'iv'	=> '0',			// 'initialVariation',	// [0]
'l'		=> 'h',			// 'layout',			// [h]orizontal, (v)ertical. See OptionParser()
'lch'	=> 'F6F6F6',	// 'lightColor',		// [F6F6F6] was EFF4EC. suffix 'h' stands for hex
'md'	=> 'f',			// 'movesDisplay',		// [f]igurine | (t)ext | (p)uzzle | (h)idden
'pf'	=> DEFAULT_Val,	// 'pieceFont',			// [d]efault <- pieceSize | (a)lpha | (m)erida | (u)scf | (s)vgchess
'ps'	=> DEFAULT_Val,	// 'pieceSize',			// [d]efault <- squareSize
'rd'	=> 'f',			// 'refreshDemo',		// [f]alse | (t)rue
'rm'	=> '1',			// 'refreshMinutes',	// [1] minute
'ss'	=> DEFAULT_ss,	// 'squareSize',		// [26] was 28
						// textHeight: nbr, optional if frameHeight
'tm'	=> '13',		// 'textMargin'			// [13] was 0. Set left/right margin width of the  textual section, header and/or moves text
						// textWidth: nbr, optional if frameWidth
				 );
}

// @return	True if the new value of the option is correct and different of its previous value
// @in: 	$optShort: short name of the option (suffix "ch" for color hex)
//			$valueNew: new value to be checked
//			$valuePrevious: according to the type of the previous value
function IsOptionValueOk($optShort, $valueNew, $valuePrevious)
{
	$isNumeric = is_numeric($valuePrevious) || ($valuePrevious == DEFAULT_Val);
	$lenOpt = strlen($optShort);
	if ($lenOpt > 2)
		$isHexColor = (substr($optShort, $lenOpt - 2) == 'ch'); // (c)olor (h)ex
	else
		$isHexColor = false;
	
	if ($valueNew != $valuePrevious)
	{
		if ($isHexColor)
		{
			if (preg_match('/^[a-f0-9]{6}$/i', $valueNew) || ($valueNew == TRANSPARENT))
				return true;
		}
		elseif ($isNumeric)
		{
			if (is_numeric($valueNew) || ($valueNew == DEFAULT_Val))
				return true;
		}
		else
			return true;
	}
	return false;
}
?>
