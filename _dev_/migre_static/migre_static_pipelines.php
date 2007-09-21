<?php
#-----------------------------------------------------#
#  Plugin  : migre_static - Licence : GPL             #
#  File    : migre_static_pipelines - pipelines       #
#  Authors : Chryjs, 2007 - Beurt, 2006               #
#  Contact : chryjs¡@!free¡.!fr                       #
#-----------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_DIR_PLUGIN_ACCESRESTREINT')){ // definie automatiquement en 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_ACCESRESTREINT',(_DIR_PLUGINS.end($p)));
}

function migrestatic_ajouter_boutons() {
// [fr] Ne semble pas fonctionner donc inutilise (a supprimer)
// [en] Doesn t seems to work... Left unused (To be removed)
}

function migrestatic_affiche_droite($flux) {
// [fr] Peut etre ajouter un controle d acces
// [fr] On accede a la migration de pages vers de nouveaux articles uniquement au sein d une rubrique
// [en] Todo : maybe add access control
// [en] Access is only allowed within a rubrique
	if (($flux['args']['exec']=='naviguer') AND
	    ($flux['args']['id_rubrique']>0) ) {
		$url_lien = generer_url_ecrire('migre_formulaire', "id_rubrique=".$flux['args']['id_rubrique']) ;
		$flux['data'] .= debut_cadre_relief('',true);
		$flux['data'] .= "<div style='font-size: x-small' class='verdana1'><b>" . _T('migre:titre_migre_formulaire') . " :</b>\n";
		$flux['data'] .= "<table class='cellule-h-table' cellpadding='0' style='vertical-align: middle'>\n" ;
		$flux['data'] .= "<tr><td><a href='$url_lien' class='cellule-h'><span class='cell-i'>" ;
		$flux['data'] .= "<img src='"._DIR_IMG_PACK ."article-24.gif' width='24' alt='";
		$flux['data'] .= _T('migrestatic:sur_titre_migre_formulaire') . "' /></span></a></td>\n" ;
		$flux['data'] .= "<td class='cellule-h-lien'><a href='$url_lien' class='cellule-h'>" ;
		$flux['data'] .= _T('migrestatic:sur_titre_migre_formulaire') . "</a></td></tr></table>\n</div>\n" ;
		$flux['data'] .= fin_cadre_relief(true);
	}
	return $flux;
}

?>
