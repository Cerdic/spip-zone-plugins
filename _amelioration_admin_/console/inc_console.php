<?php

/**
 * definition du plugin "console" version "classe statique"
 * utilisee comme espace de nommage
 */
define('_DIR_PLUGIN_CONSOLE',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));
 

	/* static public */

	/* public static */
	function Console_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['configuration']->sousmenu['console']= new Bouton(
			"../"._DIR_PLUGIN_CONSOLE."/console.png",  // icone
			_L('Console')	// titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function Console_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}


?>