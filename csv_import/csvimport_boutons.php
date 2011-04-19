<?php
/*
 * CSVimport
 * Plug-in d'import csv dans les tables spip et d'export CSV des tables
 *
 * Auteur :
 * Cedric MORIN
 * notre-ville.net
 * © 2005,2009 - Distribue sous licence GNU/GPL
 *
 */

/**
 * Insertion dans le pipeline d'ajout de boutons dans le bandeau de SPIP
 *  
 * @return Array $boutons_admin La liste des boutons après notre ajout 
 * @param Array $boutons_admin La liste de l'ensemble des boutons du bandeau
 */
function csvimport_ajouterBoutons($boutons_admin) {
	/**
	 * On ajoute le bouton uniquement pour les admins
	 */
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"] AND 
	(!isset($GLOBALS['meta']["activer_csvimport"]) OR $GLOBALS['meta']["activer_csvimport"]!="non") AND
	(!test_plugin_actif('bando'))) {

		// on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu["csvimport_tous"]= new Bouton(
			_DIR_PLUGIN_CSVIMPORT."img_pack/csvimport-24.gif",  // icone
			_T("csvimport:csvimport") //titre
		);
	}
	return $boutons_admin;
}

?>