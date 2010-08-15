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

function  configuration_bloc_spipbb_mots_cles() {
	global $spip_lang_left;
	$GLOBALS['spipbb']=@unserialize($GLOBALS['meta']['spipbb']);
	$spipbb_id_groupe_mots = $GLOBALS['spipbb']['id_groupe_mot'];
	$spipbb_id_mot_ferme = $GLOBALS['spipbb']['id_mot_ferme'];
	$spipbb_id_mot_annonce = $GLOBALS['spipbb']['id_mot_annonce'];
	$spipbb_id_mot_postit = $GLOBALS['spipbb']['id_mot_postit'];
	$groupes_mots = sql_allfetsel("id_groupe,titre","spip_groupes_mots","","","id_groupe");
	if ($spipbb_id_groupe_mots) $liste_mots = sql_allfetsel("id_mot,titre,descriptif","spip_mots","id_groupe=$spipbb_id_groupe_mots","","id_mot"); 
	else $liste_mots=array();
	
	$groupe_existe=false;
	foreach ($groupes_mots as $k => $v) {
		if (intval($v['id_groupe'])==intval($spipbb_id_groupe_mots)) {
			$groupe_existe=true;
			break;
		}
	}
	$mot_ferme_existe=$mot_annonce_existe=$mot_postit_existe=false;
	foreach ($liste_mots as $k => $v) {
		if ($v['id_mot']==$spipbb_id_mot_ferme) {
			$mot_ferme_existe=true;
		}
		elseif ($v['id_mot']==$spipbb_id_mot_annonce) {
			$mot_annonce_existe=true;
		}
		elseif ($v['id_mot']==$spipbb_id_mot_postit) {
			$mot_postit_existe=true;
		}
	}
	
	$res = "<table border='0' cellspacing='1' cellpadding='3' width=\"100%\">";

	// generation d'un bouton de config auto
	if ( count($groupes_mots)==0
		OR count($liste_mots)==0 
		OR !$spipbb_id_groupe_mots
		OR !$spipbb_id_mot_ferme 
		OR !$spipbb_id_mot_annonce 
		OR !$spipbb_id_mot_postit )
	{
		$res .= "<tr><td align='$spip_lang_left' class='verdana2'>"
		. _T('spipbb:choix_mots_creation')
		. "</td>"
		. "<td align='$spip_lang_left' class='verdana2'>"
		. "<input type='submit' name='spipbbmots_now' id='spipbbmots_now' value='"
		. _T('spipbb:choix_mots_creation_submit')
		. "' class='fondl' onclick='AjaxNamedSubmit(this)' />"
		. "</td></tr>\n";
	}

	$res .= "\n<tr><td align='$spip_lang_left' class='verdana2'>"
	. _T('spipbb:choix_mots_selection')
	. "</td>"
	. "<td align='$spip_lang_left' class='verdana2'>";
	$res .= "\n<div style='text-align: center'><select name='id_groupe_mot' id='id_groupe_mot' class='fondo' size='1'>\n";

	if (!$groupe_existe)
	{
		$res .= "<option".mySel('0','0').">"."&nbsp;"."</option>\n";
	}
	foreach ($groupes_mots as $k => $v) {
		$res .= "<option".mySel($v['id_groupe'],$spipbb_id_groupe_mots).">".propre($v['titre'])."</option>\n";
	}
	$res .= "</select></div>\n";
	$res .= "</td></tr>\n"
		.   "</table>";

	// --- mots
	if ($spipbb_id_groupe_mots) 
	{
		$res .= "<table cellpadding='3' cellspacing='1' border='1' width='100%'>";
		$res .= config_spipbb_un_mot('id_mot_ferme',_T('spipbb:choix_mots_ferme'),$spipbb_id_mot_ferme,$liste_mots,$mot_ferme_existe);
		$res .= config_spipbb_un_mot('id_mot_annonce',_T('spipbb:choix_mots_annonce'),$spipbb_id_mot_annonce,$liste_mots,$mot_annonce_existe);
		$res .= config_spipbb_un_mot('id_mot_postit',_T('spipbb:choix_mots_postit'),$spipbb_id_mot_postit,$liste_mots,$mot_postit_existe);
		$res .= "</table>";
	}
	
	return $res;
}

function config_spipbb_un_mot($meta_mot,$trad_mot,$id_mot,&$liste_mots,$mot_existe=false) {
	global $spip_lang_left;
	
	$res = "<tr><td align='$spip_lang_left' class='verdana2'>"
		. $trad_mot //
		. "</td><td align='$spip_lang_left'>"
		. "\n<select name='$meta_mot' id='$meta_mot' class='fondo' size='1'>\n";
	if (!$mot_existe)
	{
		$res .= "<option".mySel('0','0').">"."&nbsp;"."</option>\n";
	}
	foreach ($liste_mots as $k => $v) {
		$res .= "<option".mySel($v['id_mot'],$id_mot).">".propre($v['titre'])
			//." - "
			//.propre(substr($v['descriptif'],0,40))
			."</option>\n";
	}
	$res .= "</td></tr>\n";
	return $res;
}

function configuration_spipbb_mots_cles_dist()
{
	$res = configuration_bloc_spipbb_mots_cles();

	$res = 	debut_cadre_relief("", true, "", _T('spipbb:config_choix_mots'))
	. ajax_action_post('spipbb_configurer', 'spipbb_mots_cles', 'configuration','',$res)
	. fin_cadre_relief(true);

	$res = ajax_action_greffe('spipbb_configurer-spipbb_mots_cles', '', $res);

	return $res;
}
?>