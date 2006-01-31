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

class GestionDocuments {
	/* static public */

	/* public static */
	function ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" ) {

		  // on voit les bouton dans la barre "accueil"
			$boutons_admin['naviguer']->sousmenu["documents_tous"]= new Bouton(
			"../"._DIR_PLUGIN_GESTION_DOCUMENTS."/stock_broken_image.png",  // icone
			_L("Tous vos documents") //titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function ajouterOnglets($onglets, $rubrique) {
		return $onglets;
	}
}

?>