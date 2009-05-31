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

function  configuration_bloc_spipbb_rubriques() {
	global $spip_lang_left;
	$GLOBALS['spipbb']=@unserialize($GLOBALS['meta']['spipbb']);
	$spipbb_id_secteur = $GLOBALS['spipbb']['id_secteur'];
	$rub_secteur=sql_allfetsel("id_rubrique,titre","spip_rubriques","id_parent=0","","id_rubrique");
	$rub_existe=false;
	foreach ($rub_secteur as $k => $v) {
		if ($v['id_rubrique']==$spipbb_id_secteur) {
			$rub_existe=true;
			break;
		}
	}
	
	$res = "<table border='0' cellspacing='1' cellpadding='3' width=\"100%\">"
		. "<tr>" ;
	if ( count($rub_secteur)==0 OR empty($spipbb_id_secteur) ) // quasi similaire a $rub_existe mais sans la recherche dans le tableau
	{
		$res .= "<td align='$spip_lang_left' class='verdana2'>"
		. _T('spipbb:choix_rubrique_creation')
		. "</td>"
		. "<td align='$spip_lang_left' class='verdana2'>"
		. "<input type='submit' name='spipbbrub_now' id='spipbbrub_now' value='"
		. _T('spipbb:choix_rubrique_creation_submit')
		. "' class='fondl' onclick='AjaxNamedSubmit(this)' />"
		. "</td></tr>\n";
	}
	
	$res .= "<td align='$spip_lang_left' class='verdana2'>"
	. _T('spipbb:choix_rubrique_selection')
	. "</td>"
	. "<td align='$spip_lang_left' class='verdana2'>";
	$res .= "\n<div style='text-align: center'><select name='id_secteur' id='id_secteur' class='fondo' size='1'>\n";

	if (!$rub_existe)
	{
		$res .= "<option".mySel('0','0').">"."&nbsp;"."</option>\n";
	}
	foreach ($rub_secteur as $k => $v) {
		$res .= "<option".mySel($v['id_rubrique'],$spipbb_id_secteur).">".propre($v['titre'])."</option>\n";
	}
	$res .= "</select></div>\n";
	$res .= "</td></tr>\n"
		.   "</table>";
	
	return $res;
}

function configuration_spipbb_rubriques_dist()
{
	$res = configuration_bloc_spipbb_rubriques();

	$res = 	debut_cadre_relief("", true, "", _T('spipbb:config_choix_rubrique'))
	. ajax_action_post('spipbb_configurer', 'spipbb_rubriques', 'configuration','',$res)
	. fin_cadre_relief(true);

	$res = ajax_action_greffe('spipbb_configurer-spipbb_rubriques', '', $res);

	return $res;
}
?>