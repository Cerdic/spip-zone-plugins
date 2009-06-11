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
	if (!$skin = _request('skin'))
		die('indiquez une &skin=xxx');

	foreach(array(24,16) as $size){
		$masque = "";
		$postfix = "-$size.png";
		if (file_exists(_DIR_PLUGIN_BANDO.($f="skins/$skin/images/masque-$size.png"))){
			$masque = $f;
			$postfix = "-base".$postfix;
		}
		// les icones 24px
		$icones = preg_files(_DIR_PLUGIN_BANDO."skins/$skin/images/","$postfix$");
		foreach($icones as $icone){
			$objet = basename($icone,$postfix);
			if (!preg_match(',(edit|new|add|del)$,',$objet)){
				$base = $icone;
				if ($masque)
					$base = image_masque($base,$masque);
				$base_edit = image_masque($base,"skins/$skin/images/edit-$size.png","mode=normal");
				$base_new = image_masque($base,"skins/$skin/images/new-$size.png","mode=normal");
				$base_add = image_masque($base,"skins/$skin/images/add-$size.png","mode=normal");
				$base_del = image_masque($base,"skins/$skin/images/del-$size.png","mode=normal");
				echo $base
				. $base_edit
				. $base_new
				. $base_add
				. $base_del
				. "<br />";
				$icone = basename($icone,$postfix);
				if ($masque)
					copy(extraire_attribut($base,'src'),_DIR_PLUGIN_BANDO."skins/$skin/images/$objet-$size.png");
				if (in_array($objet,array('article','auteur','annonce','breve','calendrier','cookie','document','forum','groupe-mot','image','message','mot','petition','rubrique','site','traduction'))){
					copy(extraire_attribut($base_edit,'src'),_DIR_PLUGIN_BANDO."skins/$skin/images/$objet-edit-$size.png");
					copy(extraire_attribut($base_new,'src'),_DIR_PLUGIN_BANDO."skins/$skin/images/$objet-new-$size.png");
					copy(extraire_attribut($base_add,'src'),_DIR_PLUGIN_BANDO."skins/$skin/images/$objet-add-$size.png");
					copy(extraire_attribut($base_del,'src'),_DIR_PLUGIN_BANDO."skins/$skin/images/$objet-del-$size.png");
				}
			}
		}
	}
	
	echo fin_gauche(), fin_page();
}

?>