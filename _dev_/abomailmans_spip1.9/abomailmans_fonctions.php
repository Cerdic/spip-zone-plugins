<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
 * $Id$
*/

	function abomailman_checkbox ($texte, $total_boucle) {
		$liste = explode ("@", $texte);
		$nom_liste_join = $liste[0] ."-join";
		$domaine = $liste[1];
		$abonnement = $nom_liste_join . "@" . $domaine;

        $texte = $abonnement;
	    if (1 == $total_boucle) {
            $texte .= "\" checked=\"checked";
        }
        return $texte;
	}

	function abomailman_inputhidden ($texte) {
		$liste = explode ("@", $texte);
		$nom_liste_join = $liste[0] ."-join";
		$domaine = $liste[1];
		$abonnement = $nom_liste_join . "@" . $domaine;

		return $texte = "<input name=\"listes[]\" value=\"" . $abonnement . "\" type=\"hidden\" />";	
	}

    function abosympa_checkbox ($texte, $total_boucle) {
        $abonnement = $texte;

        $texte = $abonnement;
        if (1 == $total_boucle) {
            $texte .= "\" checked=\"checked";
        }        
        return $texte; 
    }
?>