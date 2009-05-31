<?php
/*
 * Glossaire
 * Gestion des listes de definitions techniques
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function glossaire_ajouter_boutons($boutons_admin) {
	// si on est admin
	if (autoriser('adminsitrer','glossaire')) {
		$boutons_admin['naviguer']->sousmenu["glossaire"]= new Bouton(
		_DIR_PLUGIN_GLOSSAIRE."img_pack/glossaire-24.png",  // icone
		_T("glossaire:type_des_tables") //titre
		);
	}
	return $boutons_admin;
}


?>
