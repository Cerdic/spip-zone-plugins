<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_STATS_PUB',(_DIR_PLUGINS.end($p)));

function stats_pub_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees") {
	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['statistiques_visites']->sousmenu['stats_pub']= new Bouton(
		"../"._DIR_PLUGIN_STATS_PUB."/statistiques-24.gif",  // icone
		_T('statspub:icone_menu_config')	// titre
		);
	}
	return $boutons_admin;
}


?>
