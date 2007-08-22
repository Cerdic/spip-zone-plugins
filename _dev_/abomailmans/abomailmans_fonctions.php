<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
*/

	function abomailman_checkbox ($texte) {		
		$liste = explode ("@", $texte);
		$nom_liste_join = $liste[0] ."-join";
		$domaine = $liste[1];
		$abonnement = $nom_liste_join . "@" . $domaine;

		return $texte = "<input name=\"listes[]\" type=\"checkbox\" value=\"" . $abonnement . "\" />";	
	}

	function abomailman_inputhidden ($texte) {		
		$liste = explode ("@", $texte);
		$nom_liste_join = $liste[0] ."-join";
		$domaine = $liste[1];
		$abonnement = $nom_liste_join . "@" . $domaine;

		return $texte = "<input name=\"listes[]\" value=\"" . $abonnement . "\" type=\"hidden\">";	
	}

?>