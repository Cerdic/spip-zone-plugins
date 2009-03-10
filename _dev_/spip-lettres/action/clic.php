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
	 * action_clic
	 *
	 * @author Pierre BASSON
	 **/
	function action_clic() {
		
		$id_clic	= $_GET['id_clic'];
		$email		= $_GET['email'];
		$code		= $_GET['code'];

		$redirection = redirection_clic($id_clic);
		
		if (lettres_verifier_validite_email($email)) {
			$abonne = new abonne(0, $email);
			if ($abonne->existe and $abonne->verifier_code($code) and !empty($id_clic)) {
				$redirection = $abonne->enregistrer_clic($id_clic);
			}
		}
		
		header('Location: '.$redirection);
		exit();

	}
	

?>