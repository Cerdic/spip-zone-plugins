<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/config');

function  configuration_bloc_squelettes()
{
	$GLOBALS['spipbb']=@unserialize($GLOBALS['meta']['spipbb']);
	$spipbb_squelette_groupeforum = entites_html($GLOBALS['spipbb']['squelette_groupeforum']);
	$spipbb_squelette_filforum = entites_html($GLOBALS['spipbb']['squelette_filforum']);
	$spipbb_config_squelette = $GLOBALS['spipbb']['config_squelette'];
	
	
	$res = debut_cadre_relief("", true, "", "<label for='squelette_groupeforum'>"._T('spipbb:squelette_groupeforum')."</label>")
	. "<input type='text' name='squelette_groupeforum' id='squelette_groupeforum' value=\"$spipbb_squelette_groupeforum\" size='40' class='formo' />"
	. fin_cadre_relief(true)

	. debut_cadre_relief("", true, "", "<label for='squelette_filforum'>"._T('spipbb:squelette_filforum')."</label>")
	. "<input type='text' name='squelette_filforum' id='squelette_filforum' value=\"$spipbb_squelette_filforum\" size='40' class='formo' />"
	. fin_cadre_relief(true);
	
	return $res;
}

function configuration_spipbb_squelettes_dist()
{
	$res = configuration_bloc_squelettes();

	$res = 	debut_cadre_relief("", true, "", _T('spipbb:config_choix_squelettes'))
	. ajax_action_post('spipbb_configurer', 'spipbb_squelettes', 'configuration','',$res)
	. fin_cadre_relief(true);

	return ajax_action_greffe('spipbb_configurer-spipbb_squelettes','', $res);
}
?>