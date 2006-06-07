<?php
/*
 * csvimport
 * plug-in d'import csv dans les tables spip
 *
 * Auteur :
 * Cedric MORIN
 * notre-ville.net
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_CSVIMPORT',(_DIR_PLUGINS.end($p)));

	function csvimport_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" AND 
		isset($GLOBALS['meta']["activer_csvimport"]) AND $GLOBALS['meta']["activer_csvimport"]!="non") {

		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu["csvimport_tous"]= new Bouton(
			"../"._DIR_PLUGIN_CSVIMPORT."/img_pack/csvimport-24.png",  // icone
			_L("Import CSV") //titre
			);
		}
		return $boutons_admin;
	}


?>