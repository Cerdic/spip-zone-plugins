<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_forums_participants_charger_dist(){

	return array(
		'forums_publics' => $GLOBALS['meta']["forums_publics"],
	);
	
}

function formulaires_configurer_forums_participants_traiter_dist(){
	include_spip('inc/config');
	appliquer_modifs_config();
		
	return array('message_ok'=>_T('config_info_enregistree'));
}

?>