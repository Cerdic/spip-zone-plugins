<?php
#-----------------------------------------------------#
#  Plugin  : spipBB - Licence : GPL                   #
#  File    : spipbb_pipelines - pipelines             #
#  Authors : Chryjs, 2007                             #
#  Contact : chryjs�@!free�.!fr                       #
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

function spipbb_affiche_droite($flux) {
// [fr] Peut etre ajouter un controle d acces
// [en] Todo : maybe add access control
$spipbb_meta = @unserialize($GLOBALS['meta']['spipbb']); // facilite la lecture
	if ( ($flux['args']['exec']=='naviguer') AND (!empty($spipbb_meta)) AND
	    (!empty($flux['args']['id_rubrique'])) ) //$spipbb_meta['spipbb_id_rubrique'])
	{
		$r = sql_fetsel("id_secteur", "spip_rubriques", "id_rubrique=".$flux['args']['id_rubrique']);
		if (is_array($r) AND $r['id_secteur']==$spipbb_meta['spipbb_id_rubrique'] )
		{
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