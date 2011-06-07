<?php

/*
 * Didaspip
 *
 * interface de gestion des projets disapages
 *
 * Auteur : moise.maindron@ac-nantes.fr
 * © 2008 - Distribue sous licence GPL
 *
 */

if (!defined('_DIR_PLUGIN_DIDASPIP')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_DIDASPIP',(_DIR_PLUGINS.end($p)));
}

	function dida_ajouterBoutons($boutons_admin) {
		// si on est admin
		//if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		//AND $GLOBALS["options"]=="avancees" ) {

		  // on voit les bouton dans la barre "accueil"
			$boutons_admin['naviguer']->sousmenu["gestdidaspip"]= new Bouton(
			"../"._DIR_PLUGIN_DIDASPIP."/img_pack/dida_ico.png",  // icone
			_T("gestion:projets didapages") //titre
			);
		//}
		return $boutons_admin;
	}


?>
