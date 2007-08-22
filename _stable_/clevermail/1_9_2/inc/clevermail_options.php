<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
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

// Ajoute le bouton du plugin dans l'interface du back-office
function clevermail_ajouter_boutons($boutons_admin) {
	if ($GLOBALS['connect_statut'] == "0minirezo") {
		$boutons_admin['configuration']->sousmenu['clevermail_index'] = new Bouton(_DIR_PLUGIN_CLEVERMAIL.'/img_pack/enveloppe.png', 'CleverMail');
	}
	return $boutons_admin;
}

function clevermail_taches_generales_cron($taches_generales){
	$taches_generales['clevermail_cron'] = 10 ;
	return $taches_generales;
}
?>