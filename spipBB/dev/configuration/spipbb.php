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

function  configuration_bloc_spipbb() {
	global $spip_lang_left;
	$GLOBALS['spipbb']=@unserialize($GLOBALS['meta']['spipbb']);
	$etat_spipbb = $GLOBALS['spipbb']['configure'];
	
	$res = "<table border='0' cellspacing='1' cellpadding='3' width=\"100%\">"
	. "<tr>"
	. "<td align='$spip_lang_left' class='verdana2'>"
	. _T('spipbb:admin_config_spipbb_info')
	. "</td>"
	. "<td align='$spip_lang_left' class='verdana2'>"
	//	. afficher_choix('configure', $etat_spipbb, // attention 'configure' correspond au champ dans les metas
	//		array('oui' => _T('item_oui'), 'non' => _T('item_non')), " &nbsp; ")

	. bouton_radio("configure", "oui", _T('item_oui'), $etat_spipbb == "oui", "changeVisible(this.checked, 'etat-spipbb', 'block', 'none');")
	. bouton_radio("configure", "non", _T('item_non'), $etat_spipbb == "non", "changeVisible(this.checked, 'etat-spipbb', 'none', 'block');")

	. "</td></tr>\n"
	. "</table>";
	
	return $res;
}

function configuration_spipbb_dist()
{
	$res = configuration_bloc_spipbb();

	$res = 	debut_cadre_relief("", true, "", _T('spipbb:admin_config_spipbb'))
	. ajax_action_post('spipbb_configurer', 'spipbb', 'configuration','',$res)
	. fin_cadre_relief(true);

	$res = ajax_action_greffe('spipbb_configurer-spipbb','', $res);

	return $res;
}
?>