<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information base sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

// New line string, which should be:
//		\n		on unices
//		\r		on Mac OS
//		\r\n	on Windows
define('CM_NEWLINE', "\n");

function clevermail_taches_generales_cron($taches_generales){
	$taches_generales['clevermail_cron'] = 10 ;
	return $taches_generales;
}

function clevermail_header_prive($flux) {
	// On ajoute un CSS et un JS pour le back-office
	$flux .= "<script src=\""._DIR_PLUGIN_CLEVERMAIL."/js/functions.js\" type=\"text/javascript\"></script>";
	$flux .= "<link rel=\"stylesheet\" type=\"text/css\" href=\""._DIR_PLUGIN_CLEVERMAIL."/css/styles.css\" />";
	return $flux;
}
?>