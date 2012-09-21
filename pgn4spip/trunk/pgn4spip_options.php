<?php
/**********************************************************************************
 * @Subject Define the array optValue of default values of options read in CFG conf
 * @package pgn4spip plugin to embed pgn4web chessboard in a SPIP 2.x article
 * @version 2.61.0
 * @copyright copyright (c) 2012 Matt Chesstale 
 * @license GNU General Public License version 3
 * @language PHP for SPIP
 *
 * @history:
 * 2.61.0: Initial version for SPIP 2.1
 **********************************************************************************/
define('PLUGIN_Name', "pgn4spip");
define('PATH_Conf', PLUGIN_Name . '/' . PLUGIN_Name . '_conf.php');
if (!function_exists('ReadCurrentConfiguration')) require _DIR_PLUGINS . PATH_Conf;

// Init $optValue with default values of options overriden with the current configuration
ReadCurrentConfiguration($optValue); // read from the config form
?>