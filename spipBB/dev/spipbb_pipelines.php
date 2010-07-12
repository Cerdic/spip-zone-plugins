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

#
# bouton interface spip col. droite sur exec/naviguer (rubrique)
#
function spipbb_affiche_droite($flux)
{
	// [fr] Peut etre ajouter un controle d acces
	// [en] Todo : maybe add access control
	if ( ($flux['args']['exec']=='naviguer') AND (intval($flux['args']['id_rubrique']) > 0)  )
	{
		include_spip('inc/spipbb_util'); // pour spipbb_is_configured
		$r = sql_fetsel("id_secteur", "spip_rubriques", "id_rubrique=".$flux['args']['id_rubrique']);
		$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
		if ((lire_config('spipbb/activer_spipbb', '') != 'on')
			OR (intval(lire_config('spipbb/secteur_spipbb', '')) < 0)) {
		// [fr] configuration pas terminee -> lien vers la config
			$url_lien = generer_url_ecrire('spipbb_configuration',"") ;
			$flux['data'] .= debut_cadre_relief('',true);
			$flux['data'] .= "<div style='font-size: x-small'><h3>" . _T('spipbb:admin_titre') . " :</h3>\n";
			$flux['data'] .= "<img src='".chemin('img_pack/spipbb-24.png')."' width='24' alt='"._T('spipbb:admin_surtitre')."' />";
			$flux['data'] .= "<a href='$url_lien' style='font-size: 1.2em;'>"._T('spipbb:config_spipbb')."</a>";
			$flux['data'] .= "</div>";
			$flux['data'] .= fin_cadre_relief(true);
		} elseif (is_array($r) AND ($r['id_secteur']!=lire_config('spipbb/secteur_spipbb'))) {
		// [fr] configuration Ok et on est dans la rubrique forum
			$url_lien = generer_url_ecrire('spipbb_admin',"") ;
			$flux['data'] .= debut_cadre_relief('',true);
			$flux['data'] .= "<div style='font-size: x-small'><h3>" . _T('spipbb:admin_titre') . " :</h3>\n";
			$flux['data'] .= "<img src='".chemin('img_pack/spipbb-24.png')."' width='24' alt='"._T('spipbb:admin_surtitre')."' />";
			$flux['data'] .= "<a href='$url_lien' style='font-size: 1.2em;'>"._T('spipbb:admin_sous_titre')."</a>";
			$flux['data'] .= "</div>";
			$flux['data'] .= fin_cadre_relief(true);
		}
	}
	return $flux;
}

#
# affiche formulaire sur page exec_auteur_infos
function spipbb_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	if ($exec=='auteur_infos'){
		$id_auteur = $flux['args']['id_auteur'];
		// c 7/12/8 plus d'extras donc... pour modifier il faut le plugin extras !!
		if(lire_config("spipbb/support_auteurs")=="table") 
		{
			include_spip('inc/spipbb_auteur_infos');
			$flux['data'].= spipbb_auteur_infos($id_auteur);
		}
	}
	return $flux;
}

#
# ch. traiter visite-forum en cron
#
function spipbb_taches_generales_cron($taches_generales){
	if (!defined('_INC_SPIPBB_COMMON')) include_spip('inc/spipbb_common');
	$taches_generales['statvisites'] = _SPIPBB_DELAIS_CRON ;
	return $taches_generales;
} // spipbb_taches_generales_cron

?>