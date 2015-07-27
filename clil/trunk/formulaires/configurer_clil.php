<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');



function formulaires_configurer_clil_traiter_dist(){
	$taggues = _request('themes');
	if (isset($taggues)) {
		sql_updateq('spip_clil_themes', array('tag' => 'non'));
		foreach ($taggues as $key => $value) {
			sql_updateq('spip_clil_themes', array('tag' => 'oui'), "id_clil_theme=$key");
		}
	}
	else
		sql_updateq('spip_clil_themes', array('tag' => 'non'));
	
	$res['message_ok'] = _T('config_info_enregistree');
	return $res;
}
