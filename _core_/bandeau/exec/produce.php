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

/**
 * Afficher en pleine page les sous items de navigation d'une entree principale du menu
 *
 */
function exec_produce_dist()
{

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page("icones", "", "");

	echo debut_gauche('', true);

	echo creer_colonne_droite('', true);
			
	echo debut_droite('', true);
	echo gros_titre("icones",'',false);

	include_spip('filtres/images_transforme');

	foreach(array(24,16) as $size){
		// les icones 24px
		$icones = preg_files(_DIR_PLUGIN_BANDO."images/base/","-$size.png$");
		foreach($icones as $icone){
			$base = image_masque($icone,"images/v1/masque-$size.png");
			$base_new = image_masque($base,"images/v1/new-$size.png","mode=normal");
			$base_add = image_masque($base,"images/v1/add-$size.png","mode=normal");
			$base_del = image_masque($base,"images/v1/del-$size.png","mode=normal");
			echo $base
			. $base_new
			. $base_add
			. $base_del
			. "<br />";
			$icone = basename($icone,"-$size.png");
			copy(extraire_attribut($base,'src'),_DIR_PLUGIN_BANDO."images/v1/$icone-$size.png");
			copy(extraire_attribut($base_new,'src'),_DIR_PLUGIN_BANDO."images/v1/$icone-new-$size.png");
			copy(extraire_attribut($base_add,'src'),_DIR_PLUGIN_BANDO."images/v1/$icone-add-$size.png");
			copy(extraire_attribut($base_del,'src'),_DIR_PLUGIN_BANDO."images/v1/$icone-del-$size.png");
		}
	}
	
	echo fin_gauche(), fin_page();
}

?>