<?php


	/**
	 * SPIP-Formulaires
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
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