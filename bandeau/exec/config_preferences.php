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

function exec_config_preferences(){
	
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('bando:titre_configurer_preferences'));
	
	echo "<h1 class='bando2_config_preferences bando2'>"._T('info_informations_personnelles')."</h1>";
	echo barre_onglets('infos_perso', 'config_preferences');

	echo debut_gauche("configurer_preferences",true);

	echo debut_droite("configurer_preferences",true);

	echo recuperer_fond('prive/configurer/preferences',$_GET);
	echo fin_gauche(),fin_page();
}

?>