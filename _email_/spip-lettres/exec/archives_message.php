<?php


	/**
	 * SPIP-Lettres : plugin de gestion de lettres d'information
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');
	include_spip('inc/date');


	/**
	 * exec_archives_message
	 *
	 * @author Pierre Basson
	 **/
	function exec_archives_message() {
		global $dir_lang, $spip_lang_right;

		lettres_verifier_droits();

		$id_archive	= $_GET['id_archive'];
		$requete_archive = 'SELECT message_html, message_texte FROM spip_archives WHERE id_archive="'.$id_archive.'" LIMIT 1';
		$resultat_archive = spip_query($requete_archive);
		list($message_html, $message_texte) = @spip_fetch_array($resultat_archive);

		if ($_GET['format'] == 'html') {
			echo $message_html;
		}
		if ($_GET['format'] == 'texte') {
			header("content-type: text/plain");
			echo $message_texte;
		}
	
	}