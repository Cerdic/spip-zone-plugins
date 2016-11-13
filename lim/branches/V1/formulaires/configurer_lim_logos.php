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

function formulaires_configurer_lim_logos_charger_dist(){
	$valeurs_meta = lire_config('lim_logos');
	$valeurs['lim_logos']=explode(',',$valeurs_meta);
	return $valeurs;
}

function formulaires_configurer_lim_logos_traiter_dist(){
	if (!is_null($v=_request($m='lim_logos')))
		ecrire_meta($m, is_array($v)?implode(',',$v):'');
	
	$res['message_ok'] = _T('config_info_enregistree');
	return $res;
}
