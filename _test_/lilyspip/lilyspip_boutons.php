<?php

/**
 * definition du plugin "Lilyspip" version "classe statique"
 * création du bouton
 */

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_LILYSPIP',(_DIR_PLUGINS.end($p)));



function lilyspip_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

	  // Pour voir le bouton dans la barre "naviguer"

	  $boutons_admin['configuration']->sousmenu['lilyspip']= new Bouton(
		"../"._DIR_PLUGIN_LILYSPIP."/images/icon22.png",  // icone
		'Lilyspip'	// titre
		);
	}

	return $boutons_admin;
}


	function lilyspip_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}


?>