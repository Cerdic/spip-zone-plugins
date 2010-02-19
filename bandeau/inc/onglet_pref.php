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

function barre_onglets_infos_perso(){
	$onglets=array();
	$avatar=find_in_theme('images/auteur-24.png');
	$onglets['infos_perso']=
		  new Bouton($avatar, 'bando:icone_mes_infos',
			generer_url_ecrire("infos_perso",'infos_perso'));
	$lang=find_in_theme('images/traduction-24.png');
	$onglets['config_langage']=
		  new Bouton($lang, 'bando:icone_langage',
			generer_url_ecrire("config_langage"));
	$pref=find_in_theme('images/maconfig-24.png');
	$onglets['config_preferences']=
		  new Bouton($pref, 'bando:icone_preferences',
			generer_url_ecrire("config_preferences"));
	return $onglets;
}
?>