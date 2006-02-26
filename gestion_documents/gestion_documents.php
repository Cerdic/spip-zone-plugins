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

function pluginpath($filepath){
   $realpath = str_replace("\\","/",realpath($filepath));
   $plugpath = str_replace("\\","/",realpath(_DIR_PLUGINS));
   $htmlpath = str_replace($plugpath,_DIR_PLUGINS,$realpath);
   $htmlpath = str_replace("//","/",$htmlpath);
   return $htmlpath;
}

define('_DIR_PLUGIN_GESTION_DOCUMENTS',pluginpath(dirname(__FILE__)));


	function GestionDocuments_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" ) {

		  // on voit les bouton dans la barre "accueil"
			$boutons_admin['naviguer']->sousmenu["portfolio"]= new Bouton(
			"../"._DIR_PLUGIN_GESTION_DOCUMENTS."/stock_broken_image.png",  // icone
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