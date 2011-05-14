<?php
/**
 * Ce plugin permet de g&#233;rer le bandeau de sommet des pages: hauteur et images.
 *
 * @author     cy_altern
 * @license    GNU/GPL
 * @package    plugins
 * @subpackage top_bando
 * @category   Interface Privee
 * @version    $Id$
 */

/**
 * fonction top_bando_jqueryui_forcer() 
 * utiliser l'API du plugin jqueryUI pour charger js et css des UI
 * inseres dans le head des pages privees et publiques par le pipeline jqueryui_forcer
 *
 * @param string $scripts la liste des UI a charger
 * @return $scripts
 */
function top_bando_jqueryui_forcer($scripts) {
	$version = substr($GLOBALS['spip_version_affichee'], 0, 3);
	if ( $version == '2.1') {
		$scripts[] = "jquery.ui.resizable";
		$scripts[] = "jquery.ui.draggable";
	}
	elseif ($version == '2.0') {
		$scripts[] = "ui.resizable";
		$scripts[] = "ui.draggable";
	}
		
	return $scripts;
}
	

?>