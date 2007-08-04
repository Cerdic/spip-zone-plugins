<?php


function jeux_ajouterBoutons($boutons_admin) {
		
		
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['naviguer']->sousmenu["jeux_tous"]= new Bouton(
			find_in_path("img/jeu-24.png"),  // icone
			_T('jeux:jeux')	// titre
			);
		
		return $boutons_admin;
}


?>