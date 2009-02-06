<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2009
 * $Id$
*/

	function abomailman_inputhidden ($texte) {
		$liste = explode ("@", $texte);
		$nom_liste_join = $liste[0] ."-join";
		$domaine = $liste[1];
		$abonnement = $nom_liste_join . "@" . $domaine;

		return $texte = "<input name=\"listes[]\" value=\"" . $abonnement . "\" type=\"hidden\" />";	
	}

?>