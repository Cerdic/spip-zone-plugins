<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

function clevermail_header_prive($flux) {
	// On vérifie la présence des tables, le cas échéant on les crée
	// (on doit pouvoir faire ca plus proprement mais j'ai pas trouvé ;)
	clevermail_creer_table();
	// On ajoute un CSS et un JS pour le back-office
	$flux .= "<script src=\""._DIR_PLUGIN_CLEVERMAIL."/js/functions.js\" type=\"text/javascript\"></script>";
	$flux .= "<link rel=\"stylesheet\" type=\"text/css\" href=\""._DIR_PLUGIN_CLEVERMAIL."/css/styles.css\" />";
	return $flux;
}
?>
