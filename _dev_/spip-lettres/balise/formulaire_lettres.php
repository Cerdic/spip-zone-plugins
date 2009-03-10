<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('lettres_fonctions');


	/**
	 * balise_FORMULAIRE_LETTRES
	 *
	 * @param p est un objet SPIP
	 * @return string url de validation de l'inscription
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_LETTRES ($p) {
		return calculer_balise_dynamique($p,'FORMULAIRE_LETTRES', array());
	}


	/**
	 * balise_FORMULAIRE_LETTRES_dyn
	 *
	 * Calcule la balise #FORMULAIRE_LETTRES
	 *
	 * @return array
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_LETTRES_dyn() {
		global $lang;
		global $message, $themes;
		global $email, $erreur;

		if (isset($message)) {
			return	inclure_balise_dynamique(
						array(
							'formulaires/formulaire_lettres_messages',
							0,
							array(
								'message'	=> $message,
								'themes'	=> $themes,
								'lang'		=> $lang
							)
						),
						false
					);
		} else {
			return	inclure_balise_dynamique(
						array(
							'formulaires/formulaire_lettres',
							0,
							array(
								'erreur'	=> $erreur ? ' ' : '',
								'email'		=> $email,
								'lang'		=> $lang
							)
						),
						false
					);
		}
	}



?>