<?php
/**
 * @name 		ADX MENU | SPIP 2.0 plugin
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 * @license		(c) 2009 GNU GPL v3 {@link http://opensource.org/licenses/gpl-license.php GNU Public License}
 * @version 	0.2 (06/2009)
 *
 * BASED ON :
 * - ADXmenu.js V4.21
 *   By Aleksandar Vacic (aplus.co.yu)
 *   Under CC BY 3.0 Attribution license.
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
// ini_set('display_errors','1'); error_reporting(E_ALL);
	
/**
 * ###########################################
 * Reglages par defaut 
 * (si vous ne disposez pas du plugin CFG, vous pouvez edtier ici ces valeurs)
 */

// Sens d'ouverture du menu :
//		- htb : Horizontal Top to Bottom (horizontal de haut en bas)
//		- hbt : Horizontal Bottom to Top (horizontal de bas en haut)
//		- vlr : Vertical Left to Right (vertical de gauche a droite)
//		- vrl : Vertical Right to Left (vertical de droite a gauche)
define('ADXMENU_OUVERTURE_DEFAUT', 'htb');

// Liste des secteurs affiches par defaut
//		- 'secteurs' : tous les secteurs en tete de menu
//		- 'tout' : toutes les rubriques en tete de menu
// 		- liste d'ID de rubriques separes par deux-points (ex. '1:2')
define('ADXMENU_RUB_DEFAUT', 'secteurs');

/**
 * Fin des reglages par defaut 
 * ###########################################
 */

/**
 * Nom de la page de documentation interne pour generation des liens
 */
define('ADXMENU_DOC', 'adxmenu_documentation');
define('ADXMENU_DOC_SPIP2', 'adxmenu_documentation_spip2'); // compat

/**
 * URL de la page de documentation sur spip-contrib (documentation officielle)
 */
define('ADXMENU_DOC_CONTRIB', 'http://www.spip-contrib.net/?article3566');

?>