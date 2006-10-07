<?php

/*
 * gestion_documents
 *
 * interface de gestion des documents
 *
 * Auteur : cedric.morin@yterium.com
 * © 2006 - Distribue sous licence GPL
 *
 */

if (!defined('_DIR_PLUGIN_GESTIONDOCUMENTS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_GESTIONDOCUMENTS',(_DIR_PLUGINS.end($p)));
}

	function GestionDocuments_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" ) {

		  // on voit les bouton dans la barre "accueil"
			$boutons_admin['naviguer']->sousmenu["portfolio"]= new Bouton(
			"../"._DIR_PLUGIN_GESTIONDOCUMENTS."/img_pack/stock_broken_image.png",  // icone
			_L("Tous vos documents") //titre
			);
		}
		return $boutons_admin;
	}


	function GestionDocuments_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}


?>