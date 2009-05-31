<?php
/*
 * charts
 *
 * Auteur :
 * Cedric MORIN
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_CHARTS',(_DIR_PLUGINS.end($p)));

function charts_ajouter_boutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees" 
	AND (!isset($GLOBALS['meta']['activer_charts']) OR $GLOBALS['meta']['activer_charts']!="non") ) {

	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu["charts_tous"]= new Bouton(
		"../"._DIR_PLUGIN_CHARTS."/img_pack/chart-24.gif",  // icone
		_T("charts:graphiques") //titre
		);
	}
	return $boutons_admin;
}

?>