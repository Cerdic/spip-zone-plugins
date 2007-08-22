<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

function exec_clevermail_queue_process() {
	include_spip('inc/clevermail_cron');
	// On force l'envoi en affichant une trace
	cron_clevermail_cron('yes');
}
?>