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
define('_DIR_LIB_PGN4WEB', _DIR_RACINE . 'lib/pgn4web/');

define('find_in_path(pgn4spip/pgn4spip_conf.php)', 'pgn4spip_conf.php');
if (!function_exists('ReadCurrentConfiguration')) require 'pgn4spip_conf.php';

// Init $optValue with default values of options overriden with the current configuration
ReadCurrentConfiguration($optValue); // read from the config form