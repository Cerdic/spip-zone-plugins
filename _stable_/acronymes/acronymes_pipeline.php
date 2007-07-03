<?php
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function acronymes_ajouter_boutons($boutons_admin) {
	if (autoriser('administrer','form')) {
	  // on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu["sigles_tous"]= new Bouton(
		_DIR_PLUGIN_ACRONYMES."img_pack/acronym-24.png",  // icone
		_L("Sigles &lt;acronym&gt; &lt;abbr&gt;") //titre
		);
	}
	return $boutons_admin;
}


?>
