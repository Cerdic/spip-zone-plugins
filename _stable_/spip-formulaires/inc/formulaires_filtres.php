<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


	include_spip('formulaires_fonctions');


	/**
	 * formulaires_afficher_erreur
	 *
	 * @param boolean resultat
	 * @param string controle
	 * @return string texte erreur
	 * @author Pierre Basson
	 **/
	function formulaires_afficher_erreur($resultat, $controle) {
		if ($resultat) {
			return _T('formulairespublic:controle_'.$controle);
		} else {
			return '';
		}
	}


?>