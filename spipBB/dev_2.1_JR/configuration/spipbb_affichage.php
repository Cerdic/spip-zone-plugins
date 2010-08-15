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

function  configuration_bloc_affichage()
{
	$GLOBALS['spipbb']=@unserialize($GLOBALS['meta']['spipbb']);
	$fixlimit = $GLOBALS['spipbb']['fixlimit'];
	$lockmaint = $GLOBALS['spipbb']['lockmaint'];
	$affiche_bouton_abus = $GLOBALS['spipbb']['affiche_bouton_abus'];
	$affiche_bouton_rss = $GLOBALS['spipbb']['affiche_bouton_rss'];	
	$affiche_avatar = $GLOBALS['spipbb']['affiche_avatar'];	
	$taille_avatar_suj = $GLOBALS['spipbb']['taille_avatar_suj'];	
	$taille_avatar_cont = $GLOBALS['spipbb']['taille_avatar_cont'];	
	$taille_avatar_prof = $GLOBALS['spipbb']['taille_avatar_prof'];	
	$affiche_membre_defaut = $GLOBALS['spipbb']['affiche_membre_defaut'];
	$log_level = $GLOBALS['spipbb']['log_level'];
	
	// CONFIG ISSUE GAFOSPIP
	$res = debut_cadre_relief("", true, "", "<label for='fixlimit'>"._T('spipbb:admin_nombre_lignes_messages')."</label>")
	. "<input type='text' name='fixlimit' id='fixlimit' value=\"$fixlimit\" size='40' class='formo' />"
	. fin_cadre_relief(true)

	. debut_cadre_relief("", true, "", "<label for='lockmaint'>"._T('spipbb:admin_temps_deplacement')."</label>")
	. "<input type='text' name='lockmaint' id='lockmaint' value=\"$lockmaint\" size='40' class='formo' />"
	. fin_cadre_relief(true);
	
	// boutons speciaux RSS/ Abus
	$res .= debut_cadre_relief("", true, "", _T('spipbb:bouton_speciaux_sur_skels'))
		. "<table border='0' cellspacing='1' cellpadding='3' width=\"100%\">\n"

		. "<tr><td align='$spip_lang_left' class='verdana2'>\n"
		. _T('spipbb:admin_afficher_bouton_alerte_abus')
		. "</td><td align='$spip_lang_left' class='verdana2'>\n"
		. afficher_choix('affiche_bouton_abus',
			($affiche_bouton_abus != 'non') ? 'oui' : 'non',
			array(
				'oui' => _T('item_oui'),
				'non' => _T('item_non')
			), " &nbsp; " )
		. "</td></tr>\n"

		. "<tr><td align='$spip_lang_left' class='verdana2'>\n"
		. _T('spipbb:admin_affichier_bouton_rss')
		. "</td><td align='$spip_lang_left' class='verdana2'>\n"
		. afficher_choix('affiche_bouton_rss',
			$affiche_bouton_rss,
			array(
				'non' => _T('item_non'),
				'un' => _T('spipbb:un'),
				'tous' => _T('spipbb:tous')
			), " &nbsp; " )
		. "</td></tr>\n"

		. "</table>\n"
		. fin_cadre_relief(true);

	
	//  OPTIONS

	$res .= debut_cadre_relief("", true, "", _T('spipbb:admin_forums_configuration_options'))
		. "<table border='0' cellspacing='1' cellpadding='3' width=\"100%\">\n"

		. "<tr><td align='$spip_lang_left' class='verdana2'>\n"
		. _T('spipbb:admin_forums_affiche_membre_defaut')
		. "</td><td align='$spip_lang_left' class='verdana2'>\n"
		. afficher_choix('affiche_membre_defaut',
			($affiche_membre_defaut != 'non') ? 'oui' : 'non',
			array(
				'oui' => _T('item_oui'),
				'non' => _T('item_non')
			), " &nbsp; " )
		. "</td></tr>\n"
		
		. "<tr><td align='$spip_lang_left' class='verdana2'>\n"
		. _T('spipbb:admin_forums_log_level')
		. "</td><td align='$spip_lang_left' class='verdana2'>\n"
		. "\n<div style='text-align: center'><select name='log_level' id='log_level' class='fondo' size='1'>\n"
		. "<option".mySel("0",$log_level).">[ 0 ] - "._T('spipbb:admin_forums_log_level_0')."</option>\n"
		. "<option".mySel("1",$log_level).">[ 1 ] - "._T('spipbb:admin_forums_log_level_1')."</option>\n"
		. "<option".mySel("2",$log_level).">[ 2 ] - "._T('spipbb:admin_forums_log_level_2')."</option>\n"
		. "<option".mySel("3",$log_level).">[ 3 ] - "._T('spipbb:admin_forums_log_level_3')."</option>\n"
		. "\n</select>\n"
		. "</div>"
		. "</td></tr>\n"

		. "</table>\n"
		. fin_cadre_relief(true);

	return $res;
}

function configuration_spipbb_affichage_dist()
{
	$res = configuration_bloc_affichage();

	$res = 	debut_cadre_relief("", true, "", _T('spipbb:admin_interface'))
	. ajax_action_post('spipbb_configurer', 'spipbb_affichage', 'configuration','',$res)
	. fin_cadre_relief(true);

	return ajax_action_greffe('spipbb_configurer-spipbb_affichage','', $res);
}
?>