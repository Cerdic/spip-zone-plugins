<?php
if (!defined('_DIR_PLUGIN_MASSIMPORT')){ // definie automatiquement en 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_MASSIMPORT',(_DIR_PLUGINS.end($p)));
}

/* public static */
function MassImport_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu['mass_import']= new Bouton(
			"../"._DIR_PLUGIN_MASSIMPORT."/img/massimport-24.png",  // icone
			_T('massimport:icone')	// titre
			);
		}
		return $boutons_admin;
}
?>
