<?php

/*
 * SpipMailer
 * Envoyer des mails par SMTP sur SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1@arscenic.info)
 *
 * © 2008 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */
 
function phpmailer_install($action){
	switch ($action){
		case 'test':
			return;
			break;
		case 'install':
			return;
			break;
		case 'uninstall':
			return phpmailer_uninstall();
			break;
	}
}

function phpmailer_uninstall(){
	effacer_meta('phpmailer');
	ecrire_metas();
}
?>