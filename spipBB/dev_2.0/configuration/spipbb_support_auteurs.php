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

function  configuration_bloc_support_auteurs()
{
	$GLOBALS['spipbb']=@unserialize($GLOBALS['meta']['spipbb']);

	$res = "<table width='100%' cellpadding='2' cellspacing='0' border='0' class='verdana2'>\n";

	# les champs, infos
	$res.= "<tr><td colspan='2'>"
		. _T('spipbb:config_champs_requis')
		. "</td></tr>\n"
		. "<tr><td colspan='2'>";
		foreach($GLOBALS['champs_sap_spipbb'] as $champ => $def) {
			$res.= "<b>".$champ."</b>, ".$def['info']."<br />";
		}
	$res.= "</td></tr>\n";

	# mode d exploitation
	$res.= "<tr><td colspan='2'>"
		. _T('spipbb:config_orig_extra')
		. "</td></tr>\n"
		. "<tr><td>". _T('spipbb:config_orig_extra_info')
		. "</td><td width='25%'>\n"
		
		. bouton_radio("support_auteurs", "extra", _T('spipbb:support_extra_normal'), $GLOBALS['spipbb']['support_auteurs'] == "extra", "changeVisible(this.checked, 'supp-table', 'none', 'block');")
		. bouton_radio("support_auteurs", "table", _T('spipbb:support_extra_table'), $GLOBALS['spipbb']['support_auteurs'] == "table", "changeVisible(this.checked, 'supp-table', 'block', 'none');")
	
		. "<div id='supp-table' style='display:"
		. ($GLOBALS['spipbb']['support_auteurs'] == "table" ? 'block':'none') 
		."'>"
		."[spip_]<input type='text' name='table_support' value='".$GLOBALS['spipbb']['table_support']."' size='8' />"
		. "</div>"
		. "</td></tr>\n"

		. "</table>\n";
	
	return $res;
}

function configuration_spipbb_support_auteurs_dist()
{
	$res = configuration_bloc_support_auteurs();

	$res = 	debut_cadre_relief("", true, "", _T('spipbb:config_champs_auteurs_plus'))
	. ajax_action_post('spipbb_configurer', 'spipbb_support_auteurs', 'configuration','',$res)
	. fin_cadre_relief(true);

	return ajax_action_greffe('spipbb_configurer-spipbb_support_auteurs','', $res);
}
?>