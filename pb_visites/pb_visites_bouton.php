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

function pb_visites_ajouter_boutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees" ) {

	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['statistiques_visites']->sousmenu["pb_statistiques"]= new Bouton(
		"../"._DIR_PLUGIN_CHARTS."/imgs/stats-avancees.gif",  // icone
		_L("Statistiques avancées") //titre
		);
	}
	return $boutons_admin;
}

?>