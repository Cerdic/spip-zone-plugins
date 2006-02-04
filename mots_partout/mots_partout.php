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

class MotsPartout {
	/* static public */

	/* public static */
	function ajouterBoutons($boutons_admin) {


	  // on voit les bouton dans la barre "accueil"
	  $boutons_admin['naviguer']->sousmenu["mots_partout"]= new Bouton(
																	   "../"._DIR_PLUGIN_MOTS_PARTOUT."/tag.png",  // icone
																	   'NOTS PARTOUT' //titre
		);
	return $boutons_admin;
}

}

?>
