<?php

/*
 * mots partout
 *
 * interface de gestion des mots clefs
 *
 * Auteur : Pierre Andrews (Mortimer)
 * © 2006 - Distribue sous licence GPL
 *
 */

define_once('_DIR_PLUGIN_MOTS_PARTOUT',(_DIR_PLUGINS . basename(dirname(__FILE__))));

class MotsPartout {
	/* static public */

	/* public static */
	function ajouterBoutons($boutons_admin) {


	  // on voit les bouton dans la barre "accueil"
	  $boutons_admin['naviguer']->sousmenu["mots_partout"]= new Bouton(
																	   "../"._DIR_PLUGIN_MOTS_PARTOUT."/tag.png",  // icone
																	   _L('mots_partout') //titre
		);
	return $boutons_admin;
}

}

?>
