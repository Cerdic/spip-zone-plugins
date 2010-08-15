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
# bouton interface spip
#
function spipbb_ajouter_boutons($boutons_admin) {
	// si on est admin ou admin restreint
	if ($GLOBALS['connect_statut'] == "0minirezo") {
		// on voit le bouton dans la barre "statistiques"
		$boutons_admin['forum']->sousmenu["spipbb_admin"]= new Bouton(
		"../"._DIR_PLUGIN_SPIPBB."img_pack/spipbb-24.png",  // icone
		_T('spipbb:titre_spipbb')	// titre
		);
## h. un seul bouton suffit !!
		/*
		$boutons_admin['configuration']->sousmenu["spipbb_admin_configuration"]= new Bouton(
		"../"._DIR_PLUGIN_SPIPBB."/img_pack/spipbb-24.png",  // icone
		_T('spipbb:admin_forums_configuration')	// titre
		);
		*/
	}
	return $boutons_admin;
}

#
# js + css prive
#
function spipbb_header_prive($flux) {
	$exec = _request('exec');
	if(strpos($exec, '^(spipbb_).*')!==false) { 
	$flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_SPIPBB.'img_pack/spipbb_styles.css" />'."\n";
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_SPIPBB.'javascript/spipbb_vueavatar.js"></script>'."\n";
	}
	if($exec=="spipbb_formpost") {
	$flux.='<script type="text/javascript" src="'._DIR_PLUGIN_SPIPBB.'javascript/spipbb_js_formpost.js"></script>'."\n";
	}
	return $flux;
}

#
# bouton interface spip col. droite sur exec/naviguer (rubrique)
#
function spipbb_affiche_droite($flux)
{
	// [fr] Peut etre ajouter un controle d acces
	// [en] Todo : maybe add access control

	if ( ($flux['args']['exec']=='naviguer') AND (!empty($flux['args']['id_rubrique'])) )
	{ // AND (!empty($GLOBALS['meta']['spipbb']))
		include_spip('inc/spipbb_util'); // pour spipbb_is_configured
		$r = sql_fetsel("id_secteur", "spip_rubriques", "id_rubrique=".$flux['args']['id_rubrique']);
		$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
		if ( !spipbb_is_configured()
			OR ($GLOBALS['spipbb']['configure']!='oui')
			OR (empty($GLOBALS['spipbb']['id_secteur'])) ) {
		// [fr] configuration pas terminee -> lien vers la config
			$url_lien = generer_url_ecrire('spipbb_configuration',"") ;
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
		} elseif (is_array($r) AND ($r['id_secteur']!=$GLOBALS['meta']['spipbb']['id_secteur'])) {
		// [fr] configuration Ok et on est dans la rubrique forum
			$url_lien = generer_url_ecrire('spipbb_admin',"") ;
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

#
# Onglet dans la page de configuration
#
function spipbb_ajouter_onglets($flux){
	// si on est admin...
	if ($flux['args']=='configuration' && spipbb_autoriser())
		$flux['data']['spipbb']= new Bouton(find_in_path('img_pack/spipbb-24.png'), _T('spipbb:titre_spipbb'), generer_url_ecrire('spipbb_configuration'));
	return $flux;
}

?>