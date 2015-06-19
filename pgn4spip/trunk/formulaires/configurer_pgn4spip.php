<?php
/**********************************************************************************
 * @Subject "OK" and "Reinit" buttons for the configurer_pgn4spip.html config form 
 * @package pgn4spip plugin to embed pgn4web Chessboard in a SPIP 3.x article
 * @version 2.61.0
 * @copyright copyright (c) 2012 Matt Chesstale 
 * @license GNU General Public License version 3
 * @language PHP for SPIP 3
 *
 * @history: Based on formulaires_configurer_mediabox_traiter_dist() in
 * 			 plugins-dist\mediabox\formulaires\configurer_mediabox.php
 * @see		 http://permalink.gmane.org/gmane.comp.web.spip.devel/62805
 **********************************************************************************/
if (!defined("_ECRIRE_INC_VERSION")) return; // No direct access allowed to this file
//define('PLUGIN_Name', "pgn4spip");
define('PLUGIN_Form', "configurer_" . PLUGIN_Name);
//define('PATH_Conf', find_in_path(pgn4spip_fonctions.php));
//if (!function_exists('InitOptionValueByDefault')) require _DIR_PLUGINS . PATH_Conf;

// Implement "Reinit" and "Save" buttons
function formulaires_configurer_pgn4spip_traiter()
{
	include_spip('inc/cvt_configurer'); include_spip('inc/meta');
	global $optValue; // Current values of options

	if (_request('reinit'))
	{	// The user clicks to the "reinit" button in the configuration form
		InitOptionValueByDefault($optValue); // Initial values of options
		effacer_meta(PLUGIN_Name); // Deleting the configuration restores default settings in the form
		return array('message_ok'=>_T('pgn4spip:config_reinit'), 'editable'=>true);
	}
	else // The user clicks to the "Save" button
	{	// Save current values of options from the GUI form to the configuration
		cvtconf_formulaires_configurer_enregistre(PLUGIN_Form, func_get_args());
		return array('message_ok'=>_T('config_info_enregistree'), 'editable'=>true);
	}
}