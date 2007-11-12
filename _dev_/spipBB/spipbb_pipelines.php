<?php
#----------------------------------------------------------#
#  Plugin  : spipBB - Licence : GPL                        #
#  File    : spipbb_pipelines - pipelines                  #
#  Authors : Chryjs, 2007 et als                           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#----------------------------------------------------------#

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


function spipbb_ajouter_boutons($boutons_admin) {
	// si on est admin ou admin restreint
	if ($GLOBALS['connect_statut'] == "0minirezo") {
		// on voit le bouton dans la barre "statistiques"
		$boutons_admin['forum']->sousmenu["spipbb_admin"]= new Bouton(
		"../"._DIR_PLUGIN_SPIPBB."/img_pack/spipbb-24.png",  // icone
		_T('spipbb:admin_titre')	// titre
		);
		$boutons_admin['configuration']->sousmenu["spipbb_admin_configuration"]= new Bouton(
		"../"._DIR_PLUGIN_SPIPBB."/img_pack/spipbb-24.png",  // icone
		_T('spipbb:admin_forums_configuration')	// titre
		);
	}
	return $boutons_admin;
}

function spipbb_affiche_droite($flux)
{
	// [fr] Peut etre ajouter un controle d acces
	// [en] Todo : maybe add access control
	include_spip('inc/spipbb'); // Compatibilite 192
	if ( !isset($GLOBALS['meta']['spipbb']) or !is_array($GLOBALS['meta']['spipbb']) ) {
		spipbb_upgrade_all();
	}

	$spipbb_meta = @unserialize($GLOBALS['meta']['spipbb']); // facilite la lecture

	if ( ($flux['args']['exec']=='naviguer') AND (!empty($spipbb_meta)) AND
	    (!empty($flux['args']['id_rubrique'])) )
	{
		$r = sql_fetsel("id_secteur", "spip_rubriques", "id_rubrique=".$flux['args']['id_rubrique']);
		if (empty($spipbb_meta['spipbb_id_rubrique']) ) {
		// [fr] configuration pas terminee -> lien vers la config
			$url_lien = generer_url_ecrire('spipbb_admin_configuration', "") ;
			$flux['data'] .= debut_cadre_relief('',true);
			$flux['data'] .= "<div style='font-size: x-small' class='verdana1'><b>" ;
			$flux['data'] .= _T('spipbb:admin_titre') . " :</b>\n";
			$flux['data'] .= "<table class='cellule-h-table' cellpadding='0' style='vertical-align: middle'>\n" ;
			$flux['data'] .= "<tr><td><a href='$url_lien' class='cellule-h'><span class='cell-i'>" ;
			$flux['data'] .= "<img src='"._DIR_PLUGIN_SPIPBB ."img_pack/spipbb-24.png' width='24' alt='";
			$flux['data'] .= _T('spipbb:admin_titre') . "' /></span></a></td>\n" ;
			$flux['data'] .= "<td class='cellule-h-lien'><a href='$url_lien' class='cellule-h'>" ;
			$flux['data'] .= _T('spipbb:config_spipbb') . "</a></td></tr></table>\n</div>\n" ;
			$flux['data'] .= fin_cadre_relief(true);
		} else if (is_array($r) AND $r['id_secteur']==$spipbb_meta['spipbb_id_rubrique']) {
		// [fr] configuration Ok et on est dans la rubrique forum
			$url_lien = generer_url_ecrire('spipbb_admin', "id_rubrique=".$flux['args']['id_rubrique']) ;
			$flux['data'] .= debut_cadre_relief('',true);
			$flux['data'] .= "<div style='font-size: x-small' class='verdana1'><b>" . _T('spipbb:admin_titre') . " :</b>\n";
			$flux['data'] .= "<table class='cellule-h-table' cellpadding='0' style='vertical-align: middle'>\n" ;
			$flux['data'] .= "<tr><td><a href='$url_lien' class='cellule-h'><span class='cell-i'>" ;
			$flux['data'] .= "<img src='"._DIR_PLUGIN_SPIPBB ."img_pack/spipbb-24.png' width='24' alt='";
			$flux['data'] .= _T('spipbb:admin_surtitre') . "' /></span></a></td>\n" ;
			$flux['data'] .= "<td class='cellule-h-lien'><a href='$url_lien' class='cellule-h'>" ;
			$flux['data'] .= _T('spipbb:admin_sous_titre') . "</a></td></tr></table>\n</div>\n" ;
			$flux['data'] .= fin_cadre_relief(true);
		}
	}
	return $flux;
}

?>