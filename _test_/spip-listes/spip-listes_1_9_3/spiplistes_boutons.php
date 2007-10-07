<?php

/**
 * definition du plugin "spiplistes" version "classe statique"
 * utilisee comme espace de nommage
 */

	if (!defined('_DIR_PLUGIN_SPIPLISTES')){
		$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_SPIPLISTES',(_DIR_PLUGINS.end($p)).'/');
	} 

	function spiplistes_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['naviguer']->sousmenu["spiplistes"]= new Bouton(
			_DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_mail.gif"  // icone
			, _T('spiplistes:bouton_listes')	// titre
			, _SPIPLISTES_EXEC_COURRIERS_LISTE
			);
		}
		return $boutons_admin;
	}




?>