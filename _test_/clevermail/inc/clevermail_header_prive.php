<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information bas� sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

function clevermail_header_prive($flux) {
	// On v�rifie la pr�sence des tables, le cas �ch�ant on les cr�e
	// (on doit pouvoir faire ca plus proprement mais j'ai pas trouv� ;)
	clevermail_creer_table();
	// On ajoute un CSS et un JS pour le back-office
	$flux .= "<script src=\""._DIR_PLUGIN_CLEVERMAIL."/js/functions.js\" type=\"text/javascript\"></script>";
	$flux .= "<link rel=\"stylesheet\" type=\"text/css\" href=\""._DIR_PLUGIN_CLEVERMAIL."/css/styles.css\" />";
	return $flux;
}
?>
