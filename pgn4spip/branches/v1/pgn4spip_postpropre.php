<?php
/**********************************************************************************
 * @Subject Fix _AUTOBR when the plugin is embedded in a table (see Test\SPIP3\Test08)
 * @package pgn4spip plugin to embed pgn4web Chessboard in a SPIP 3.x article
 * @version 2.61.0
 * @copyright copyright (c) 2012 Matt Chesstale 
 * @license GNU General Public License version 3
 * @compatible SPIP 3.x and SPIP 2.x
 * @language PHP for SPIP 3 and SPIP 2
 *
 * @history:
 * 2.61.0: Initial version for SPIP 3
 * @reference: _AUTOBR in http://www.spip.net/en_article5533.html
 * pipeline "post_propre" happens after the HTML iframe generation by pgn4spip_fonctions.php
 * and after spip3\plugins-dist\textwheel\inc\texte.php replaces "\n" with "<p><br class='autobr' /></p>"
 * The user does NOT need to define the constant _AUTOBR as '' in spip3\ecrire\mes_options.php
 * @example
	<?php
	// Folder: ecrire\
	define('_AUTOBR', ''); // Do not translate "\n" as "<p><br class='autobr' /></p>"
	?> 
 **********************************************************************************/
if (!defined("_ECRIRE_INC_VERSION")) return; // No direct access allowed to this file

define('AUTOBR_BEFORE', "</textarea>\n");
define('CLS_AUTOBR', "<br class='autobr' />");
define('AUTOBR_LINE', "<p>" . CLS_AUTOBR . "</p>\n");
define('AUTOBR_AFTER', "<iframe src=");
define('indFullPattern', 0);
define('CLS_CHESSS', "class='chessboard-wrapper'"); // chessboard-wrapper class for the div

// Fix AUTOBR in the generated HTML iframe and restore "{" "}" for a PGN comment
// @in:		$flux the full article including the generated HTML iframe to run board.html
// @return	$flux without AUTOBR between </textarea> and <iframe> inside a HTML table
function pgn4spip_postpropre($flux)	
{
	if (strpos($flux, CLS_CHESSS))
	{
		$regex = "@<div.*" . CLS_CHESSS . ".*</div>@msU";
		// http://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
		// m:PCRE_MULTILINE contains "\n"; s:PCRE_DOTALL including "\n"; + U:PCRE_UNGREEDY stop to first "</div>"
		preg_match_all($regex, $flux, $matches, PREG_PATTERN_ORDER); // find all instances of generated plugin
		$countPlugins = count($matches[0]); // Number of plugins
		if ($countPlugins > 0) // iframe(s) found
		{
			for ( $indPlugin=0; $indPlugin < $countPlugins; $indPlugin++ ) // For each iframe
			{	
				$strHtml = $matches[indFullPattern][$indPlugin];
				$strHtml = str_replace( AUTOBR_BEFORE . AUTOBR_LINE . AUTOBR_AFTER, 
										AUTOBR_BEFORE . AUTOBR_AFTER, $strHtml); // Fix AUTOBR
				$strHtml = str_replace( CLS_AUTOBR, "", $strHtml); // Second pass without <p>...</p>
				$strHtml = str_replace("&#123;", "{", $strHtml); // Replace "&#123;PGN comment&#125;" with 
				$strHtml = str_replace("&#125;", "}", $strHtml); // "{PGN comment}"
				$flux = str_replace($matches[indFullPattern][$indPlugin], $strHtml, $flux);
			}	
		}		
	}
	return $flux;
}
?>
