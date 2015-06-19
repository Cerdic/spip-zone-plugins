<?php
/**********************************************************************************
 * @Subject Parse options and generate HTML iframe to run board.html
 * @package pgn4spip plugin to embed pgn4web Chessboard in a SPIP 2.x or 3.x article
 * @version 2.61.0
 * @copyright copyright (c) 2012 Matt Chesstale 
 * @license GNU General Public License version 3
 * @compatible SPIP 2.x and CFG 1.15 or SPIP 3.x
 * Penknife or Swiss Army Knife 1.8 if pgn4spip is enabled after Swiss Knife
 * If you see .pgn header with French double quotes, disable and reenable pgn4spip.
 * @language PHP for SPIP 2 or 3
 *
 * @history:
 * 2.61.0: Initial version for SPIP 2.1
 * @credits: Paolo Casaschi
 * @reference: http://pgn4web.casaschi.net
 * @tutorial: http://code.google.com/p/pgn4web/wiki/User_Notes_joomla
 * @Usage
	In a SPIP article, enter the following tag:
	[pgn parameter1=value1 prm2=value2 ...] chess games notation in PGN format [/pgn]
	
	Tag parameters: see the configuration of the module in CFG for the help
	layout=horizontal|vertical
	height=auto|'number'
	movesDisplay=figurine|text|puzzle|hidden
	initialGame=first|last|random|'number'
	initialVariation='number'
	initialHalfmove=start|end|random|comment|'number'
	autoplayMode=game|loop|none
 
	Example in a SPIP article:
	Before
	[pgn autoplayMode=loop initialHalfmove=end] 1. e4 Nf6 2. e5 Nd5 [/pgn]
	After
 **********************************************************************************/
if (!defined("_ECRIRE_INC_VERSION")) return; // No direct access allowed to this file

//define('PLUGIN_Name', "pgn4spip");
//define('PATH_Conf', 'find_in_path(pgn4spip_conf.php)');
//if (!function_exists('ReadCurrentConfiguration')) require _DIR_PLUGIN_PGN4SPIP . PATH_Conf;

define('TAG_pgn', "[pP][gG][nN]"); // pgn or PGN
define('PATH_board',_DIR_RACINE . 'lib/pgn4web/board.html');
define('PATH_live', _DIR_PLUGIN_PGN4SPIP . 'boardLive.html');
define('indFullPattern', 0);
define('indPgnOption', 1);
define('indPgnGame', 2);
define('NO_iFrame', "your web browser or your host do not support iframes as required to display the chessboard");
define('DEFAULT_HEIGHT', 60); // 3*2 border + 13*2 padding + 28 buttons without 26*8 squares

define('CLS_CHESSS', "class='chessboard-wrapper'"); // chessboard-wrapper class for the div

// Parse options, prepare HTML content for output, generate the HTML iframe
// pipeline "pre_propre" is called for the description of the article then its body before "post_propre"
// @in:		$flux the full article including the [pgn] ... [/pgn] tags
// @return	$flux with the pgn tags replaced with the HTML iframe to run board.html
function pgn4spip_prepropre($flux)	
{
	global $optValue; // Values of options

	if ($optValue == NULL) // True in SPIP 3 since pgn4spip_options.php is not called
	{	// Init $optValue with default values of options overriden with the current configuration
		ReadCurrentConfiguration($optValue);
	}
	$regex = "@\[" . TAG_pgn . "(.*?)\](.*?)\[/" . TAG_pgn . "\]@s"; // Expression to search for
	preg_match_all($regex, $flux, $matches); // find all instances of plugin and put in $matches
	$countPlugins = count($matches[2] ); // Number of plugins
	if ($countPlugins > 0) // PGN game(s) found?
	{
		NameOptionPrm(/* out */ $optName, $skipPrms, $pgnPrms);
		for ( $indPlugin=0; $indPlugin < $countPlugins; $indPlugin++ ) // For each game
		{
			$pgnOptionsInput = $matches[indPgnOption][$indPlugin];
			$pgnOptions = OptionParser($pgnOptionsInput, $optName);
			$pgnSource = ExtendedOptionParser($pgnOptions, $optName, $skipPrms, $pgnPrms, 
								    /* out */ $isNewPgnSource, $pgnSource); 
			$pgnText = SpipSpecific($matches[indPgnGame][$indPlugin], /* out */ $idTxtArea);
			$height = $optValue['fh']; // Either "" for automatic evaluation of the height or fh=nbr
			$isLive = IsMatchFirst($optValue['md'], 'live');
			$optValue['fh'] = FrameHeightEval($pgnText, $isLive, $optValue['hl'], $isNewPgnSource, $optValue['md'],
									/* i/o */ $height); // The return value could be not numeric like "b"
			$strHtml = GenHtml($pgnText, $idTxtArea, $isLive, $isNewPgnSource, $pgnSource, $height);
			$flux = str_replace($matches[indFullPattern][$indPlugin], $strHtml, $flux);
			//InitOptionValueByDefault($optValue); // Restore values by default defined in PATH_check module
		}		
	}
	return $flux;
}

// Generate the HTML code of the div including the iframe calling board.html with several options
// @in:		$pgnText the game in PGN format
//			$idTxtArea id of the pgnText
//			$isLive true if live broadcast otherwise false
//			$isNewPgnSource true if extendedoptions
//			$pgnSource special option(s) to indicate the (remote) source of PGN if extendedoptions
//			$height the height of the iframe module
// @return	the HTML iframe to run board.html
function GenHtml($pgnText, $idTxtArea, $isLive, $isNewPgnSource, $pgnSource, $height)
{
	global $cs, $optValue;
	
	if ($isLive)
	{
		$pathBoard = PATH_live; // Relative path of the HTML script boardLive.html
		$optValue['md'] = "f"; // (f)igurine in live mode
	}
	else
		$pathBoard = PATH_board; // Relative path of the HTML script board.html
	if (strlen($cs) == 0) 
		$csDef = " ";
	else 
		$csDef = " style='" . $cs . "' ";	// containerStyle
	
	$strHtml  = "<div" . $csDef . CLS_CHESSS . ">";
    if (!$isNewPgnSource)
	{
		$pgnId = "pgn4web_" . $idTxtArea;
		$strHtml .= "<textarea id='" . $pgnId . "' style='display:none;' rows='40' cols='8'>" . $pgnText . "</textarea>\n";
	}
	$strHtml .= "<iframe src='"  . $pathBoard . "?";
	$isFirstOption = true;
	foreach ($optValue as $optShort => $value)
	{
		if (($optShort != "l") && // (l)ayout already encoded by (h)orizontal(L)ayout
			(($optShort[0] != "r") || $isLive))
		{
			if (!$isFirstOption) $strHtml .= "&amp;"; // Separator between two parameters
			$strHtml .= $optShort . "=" . rawurlencode($value);
			$isFirstOption = false;
		}
	}
    if (!$isNewPgnSource) 
		$strHtml .= "&amp;pi=" . rawurlencode($pgnId);
		
    $strHtml .= $pgnSource . "'\n";
	$strHtml .= "frameborder='0' width='100%' height='" . $height . "' ";
	$strHtml .= "scrolling='no' marginheight='0' marginwidth='0'>" . NO_iFrame . "</iframe>";
	$strHtml .= "</div>";

	return $strHtml;
}

// @return	true if the option matches with the value or the first character of the value
// @in:		@option the value of the option
//			@value	the value for comparison
function IsMatchFirst($option, $value)
{
	if ($option == $value)
		return true;
	if ($option == $value[0])
		return true;
	return false;
}

// Parse the options inside the [pgn prm1=value1 prm2=value2 ...] tag
// Update the values in $optValue, the array of options
// @in:		$pgnOptionsInput the specified options in the pgn tag
//			$optName array of long names of options, an alternative to the short names
// @return	the pgn options without HTML tags
function OptionParser($pgnOptionsInput, $optName)
{
	global $optValue;
	
	$pgnOptions = preg_replace('@(pgnData=|pd=)<a href="([^"]+)".*</a>@i', "$1$2", $pgnOptionsInput); // Fix Swiss Knife MailCrypt
	$pgnOptions = preg_replace("@<.*?>@", " ", $pgnOptions); // Remove HTML tags
	foreach ($optValue as $optShort => $value)
	{
		if (preg_match("#(^|\s)(" . $optName[$optShort] . "|" . $optShort . ")=(.*?)(\s|$)#si", $pgnOptions, $thisOption))
		{
			$valueNew = $thisOption[3];
			if (IsOptionValueOk($optShort, $valueNew, $optValue[$optShort]))
				$optValue[$optShort] = $valueNew;
		}
	}	
	// Rules between options
	if (IsMatchFirst($optValue['l'], "vertical")) // vertical (l)ayout?
		$optValue['hl'] = "f"; // (f)alse: vertical
	elseif (IsMatchFirst($optValue['l'], "horizontal")) // horizontal (l)ayout?
		$optValue['hl'] = "t"; // (t)rue:  horizontal 
	if (preg_match("#(^|\s)(height|h)=(.*?)(\s|$)#si", $pgnOptions, $thisOption))
		$height = $thisOption[3];		
	if (IsMatchFirst($optValue['md'], "puzzle"))
		$optValue['hd'] = "v"; // headerDisplay <- (v)ariations
	return $pgnOptions;
}

// Compatibility with the SPIP plugin Swiss Army Knife a.k.a PenKnife
// @in:		$pgnText eventually with left curly inverted commas ou French double quotes << guillemets >>
// @return	$pgnText with only '"' instead of &laquo; &raquo; &ldquo; &rdquo; generated by Swiss Knife
// 			Tool "Curly inverted commas"
// @see		http://www.degraeve.com/reference/specialcharacters.php
function CompatibleSwissKnife($pgnText)
{
	$pgnText = str_replace('&laquo;&nbsp;', '"', $pgnText);	// Left angle quote + " " to double quote
	$pgnText = str_replace('&ldquo;', '"', $pgnText);		// Left double quote
	$pgnText = str_replace('&nbsp;&raquo;', '"', $pgnText);	// " " + Right angle quote
	$pgnText = str_replace('&rdquo;', '"', $pgnText);		// Right double quote
	
	$pgnText = str_replace('&#8217;', "'", $pgnText);		// Left single quote to single quote
	$pgnText = str_replace('&lsquo;', "'", $pgnText);		// Left single quote
	$pgnText = str_replace('&rsquo;', "'", $pgnText);		// Right single quote
	return $pgnText;
}

// PGN comments are not processed as italic by SPIP. No empty line and HTML tags
// @in:		$pgnText eventually with HTML tags, emtpy lines, PGN comments between {...}, quotes by Swiss Knife
// @out:	$idTxtArea hexa identifier based on crc of the pgn text before replacing {...}
// @return	$pgnText without HTML tags, empty lines, quotes by Swiss Knife but protected PGN comments
function SpipSpecific($pgnText, &$idTxtArea)
{
	$pgnText = trim(preg_replace("@<.*?>@", " ", $pgnText)); // Remove HTML tags in the PGN game
	// No empty line processed as <p>...</p> by other SPIP plugins
	$pgnText = preg_replace("@(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n\']+@", "\r\n", $pgnText);
	$pgnText = CompatibleSwissKnife($pgnText);
	$idTxtArea = dechex(crc32($pgnText));
	$pgnText = str_replace("{", "&#123;", $pgnText); // Replace "{PGN comment}" with 
	$pgnText = str_replace("}", "&#125;", $pgnText); // "&#123;PGN comment&#125;" to avoid SPIP italic
	return $pgnText;
}

// Is there a new source for the PGN? 
// @in:		the pgn options without HTML tags
//			$optName array of long names of options, an alternative to the short names
//			$skipPrms other options (extendedoptions, height) to be skiped
//			$pgnPrms pgn options to indicate alternative (remote) source
// @out:	$isNewPgnSource true if extendedoptions
// @return	$pgnSource special option(s) to indicate the (remote) (URL) source of PGN if extendedoptions
function ExtendedOptionParser($pgnOptions, $optName, $skipPrms, $pgnPrms, &$isNewPgnSource, &$pgnSource)
{
	global $extOpt;

    $isNewPgnSource = false;
    $pgnSource = trim($extOpt); 
	if ($pgnSource != '')
	{
		$pgnSource = preg_replace('/^\s+/', '', $pgnSource);
		$pgnSource = preg_replace('/\s+$/', '', $pgnSource);
		$pgnSource = preg_replace('/&/', ' ', $pgnSource);
	}
	foreach ($skipPrms as $optShort => $optLong)
	{
		if (preg_match("#(^|\s)(" . $optLong . "|" . $optShort . ")=(.*?)(\s|$)#si", $pgnOptions, $thisOption))
		{
			$valueNew = $thisOption[3];
			switch ($optShort)
			{						
			case "eo":	if (IsMatchFirst($valueNew, 'true')) // Extended options?
							$pgnSource .= ' ' . $pgnOptions;		
						break;						
			}
		}
	}	
	if (strlen($pgnSource) > 0) 
	{
        foreach ($optName as $optShort => $optLong)
        	$pgnSource = preg_replace('#(^|\s+)' . $optShort . '|' . $optLong . '=\S*#si', '', $pgnSource);

        foreach ($skipPrms as $optShort => $optLong)
        	$pgnSource = preg_replace('#(^|\s+)' . $optShort . '|' . $optLong . '=\S*#si', '', $pgnSource);

        foreach ($pgnPrms as $optShort => $optLong)
		{
            if (preg_match('#(^|\s+)' . $optShort . '|' . $optLong . '=\S*#si', $pgnSource))
			{
                $isNewPgnSource = true;
                break;
            }
        }
        if (strlen($pgnSource) > 0)
        	$pgnSource = ' ' . $pgnSource;
			
		$pgnSource = rtrim($pgnSource); // No need to add "&amp;" at the right of the last parameter
        $pgnSource = preg_replace('#\s+#si', '&amp;', $pgnSource);
	}	
	return $pgnSource;
}

// Evaluate the frame height of the iframe module
// @in:		$pgnText PGN game
//			$isLive true if $md="live"
//			$hl horizontalLayout "t" or "f"
//			$isNewPgnSource true if extendedOptions then +34 for the height
//			$md movesDisplay not hidden -> +300 for the height
// @return	$height = fh, the (f)rame(H)eight of the module
//			"b" for (b)oard for vertical scrolling of the game if horizontalLayout
function FrameHeightEval($pgnText, $isLive, $hl, $isNewPgnSource, $md, /* i/o */ &$height)
{
	global $optValue;

    if (!is_numeric($height))
	{
		$ss = $optValue['ss'];
		if (!is_numeric($ss))
			$ss = DEFAULT_ss; // default size for squareSize
		$ss = intval($ss);
        $height = DEFAULT_HEIGHT + $ss * 8;
		if ($isLive)
		{ // GameSelector + eventDetails + playersDetails + statusDetails + padding
			$height += 23 + 18 + 17 + 17 + 10;
			if ($hl == "f") // Vertical layout?
				$height += 150; // Room for the PGN moves under statusDetails
		}
		else
		{
			// guessing if one game or multiple games are supplied
			$multiGamesRegexp = '/\s*\[\s*\w+\s*"[^"]*"\s*\]\s*[^\s\[\]]+[\s\S]*\[\s*\w+\s*"[^"]*"\s*\]\s*/';
			if ($isNewPgnSource || (preg_match($multiGamesRegexp, $pgnText) > 0)) { $height += 34; }
			if ($hl == "t") // Horizontal layout?
				return "b"; // (b)oard required for vertical scrolling bar
			
			$height += 75; // header
			if (!IsMatchFirst($md, 'hidden')) { $height += 300; } // moves
		}
    }
	return $height;
}

// Associate to each short name of options a long name that can be used as paramater of the [pgn prm=value] tag
// @out:	$optName array of long names of options in alphabetic order, an alternative to the short names
//			$skipPrms other options (extendedoptions, height) to be skiped
//			$pgnPrms pgn options to indicate alternative (remote) source
// @return	nothing
function NameOptionPrm(&$optName, &$skipPrms, &$pgnPrms)
{
    // the [value by default] is in square brackets in first position in the list after //
    $optName = array( // of long names of options, an alternative to the short names
    'am'    => 'autoplayMode',           // [n]one |(g)ame | (l)oop
    'bbch'  => 'boardBorderColor',       // [E0E0E0] was 000000
    'bd'    => 'buttonsDisplay',         // [c]ustom, (h)idden, (s)tandard
    'bch'   => 'backgroundColorHex',     // [F6F6F6] was FFFFFF, (t)ransparent to use the parent's background color
    'bsch'  => 'boardShadowColor',       // [t]ransparent no shadow | (b)order | nbr
    'cbch'  => 'controlBackgroundColor', // [F0F0F0] was standard buttons
    'cd'    => 'commentsDisplay',        // [n]ewline, (i)nline,(h)idden
    'ctch'  => 'controlTextColor',       // [696969] was 000000
    'd'     => 'delay',                  // [3000] was 1000 in ms
    'dch'   => 'darkColor',              // [E0E0E0] was C6CEC3
    'fcch'  => 'fontCommentsColor',      // [000080]
    'fcs'   => 'fontCommentsSize',       // [m]oves <- fontMovesSize
    'fh'    => 'frameHeight',            // [""], (p)age, (b)oard, nbr, overriding textHeight. See FrameHeightEval()
    'fhch'  => 'fontHeaderColor',        // [000000]
    'fhs'   => 'fontHeaderSize',         // [14] was 16
    'fmch'  => 'fontMovesColor',         // [000000]
    'fms'   => 'fontMovesSize',          // [14] was 16
    'fp'    => 'framePadding',           // [13] was 0
    'fw'    => 'frameWidth',             // [p]age, (b)oard, nbr, overriding textHeight
    'hch'   => 'highlightColor',         // [ABABAB] was DAF4D7
    'hd'    => 'headerDisplay',          // [j]ustified | (h)idden | (c)entered | (l)ive | (v)ariations
    'hl'    => 'horizontalLayout',       // [t]rue layout=[h]orizontal, (f)alse layout=(v)ertical
    'hm'    => 'highlightMode',          // [b]order, (s)quare, (n)one
    'hmch'  => 'highlightMovesColor',    // [E0E0E0] was DAF4D7 | (b)ackground for no highligh
    'ig'    => 'initialGame',            // [f]irst | (l)ast | (r)andom
    'ih'    => 'initialHalfmove',        // [s]tart | (e)nd | (r)andom | (c)omment | (v)ariation | nbr
    'iv'    => 'initialVariation',       // [0]
    'l'     => 'layout',                 // [h]orizontal, (v)ertical. See OptionParser()
    'lch'   => 'lightColor',             // [F6F6F6] was EFF4EC. suffix 'h' stands for hex
    'md'    => 'movesDisplay',           // [f]igurine | (t)ext | (p)uzzle | (h)idden | (l)ive
    'pf'    => 'pieceFont',              // [d]efault based on pieceSize | (a)lpha | (m)erida | (u)scf | (s)vgchess
    'ps'    => 'pieceSize',              // [d]efault <- squareSize
    'rd'    => 'refreshDemo',            // [f]alse | (t)rue if md="t"
    'rm'    => 'refreshMinutes',         // [1] minute if md="t"
    'ss'    => 'squareSize',             // [26] was 28
                                         // textHeight: nbr, optional if frameHeight
    'tm'    => 'textMargin'              // [13] was 0. Set left/right margin width of the  textual section, header and/or moves text
                                         // textWidth: nbr, optional if frameWidth
                    );
    $skipPrms = array( // of other options (extendedOptions, height) to be skiped
    'eo'    => 'extendedOptions',        // [f]alse, (t)rue
    'h'     => 'height'                  // [auto] = DEFAULT_HEIGHT. Height of the module
                    );
    $pgnPrms = array( // of pgn options to indicate alternative (remote) source
    'fs'    => 'fenString',              // Initial position in format FEN 
    'pd'    => 'pgnData',                // URL of the PGN file
    'pe'    => 'pgnEncoded',             // PGN game encoded
    'pi'    => 'pgnId',                  // id of the pgnText textarea 
    'pt'    => 'pgnText'                 // PGN game
                    );
}