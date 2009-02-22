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

include_spip('inc/presentation');

function exec_config_forum_dist()
{
	if (!autoriser('configurer', 'forum')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

	$config = charger_fonction('config', 'inc');
	$config();

	pipeline('exec_init',array('args'=>array('exec'=>'config_forum'),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_page_config_contenu'), "configuration", "configuration");

	echo gros_titre(_T('titre_page_config_contenu'),'', false);
	echo barre_onglets("configuration", "config_forum");

	echo debut_gauche('', true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'config_forum'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'config_forum'),'data'=>''));
	echo debut_droite('', true);

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'config_forum'),'data'=>recuperer_fond("prive/configurer/forum",array())));

	echo fin_gauche(), fin_page();
	}
}

?>
