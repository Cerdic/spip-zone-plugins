<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_admin_debug - base admin menu          #
#  Authors : Chryjs, 2007                                       #
#  Contact : chryjs!@!free!.!fr                                 #
# [en] admin menus                                              #
# [fr] menus d'administration                                   #
#---------------------------------------------------------------#

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

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/plugin'); // pour version du plugin
if (defined("_GENERAL_DEBUG")) return; else define("_GENERAL_DEBUG", true);
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

// ------------------------------------------------------------------------------
// Affiche le debogage pour la version SVN
// ------------------------------------------------------------------------------
function exec_spipbb_admin_debug() {

	# initialiser spipbb
//	include_spip('inc/spipbb_init'); // sauf que si bug là -> erreur !!
	# lire version plugin.xml
	#
	if(version_compare($GLOBALS['spip_version_code'],'15375','>=')) {
		$get_infos = charger_fonction('get_infos','plugins');
		$infos = $get_infos(_DIR_PLUGIN_SPIPBB);
	}
	else {
		$infos = plugin_get_infos(_DIR_PLUGIN_SPIPBB);
	}
	$GLOBALS['spipbb_plug_version'] = $infos['version'];

	# recup des metas
	// c: 18/12/7 normalement ce n'est pas utile !! car deja initialise ailleurs !
	$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);

	include_spip('inc/spipbb_util');
	include_spip('inc/spipbb_presentation');
	include_spip('inc/spipbb_menus_gauche');
	# Def. repertoire icones back
	if (!defined("_DIR_IMG_SPIPBB")) {
		define('_DIR_IMG_SPIPBB', _DIR_PLUGIN_SPIPBB.'/img_pack/');
	}

	# requis de cet exec
	#

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')), "forum", "spipbb_admin", '');
	echo "<a name='haut_page'></a>";

	echo debut_gauche('',true);
		spipbb_menus_gauche(_request('exec'));

	echo creer_colonne_droite('', true);

	echo debut_droite('',true);

	echo spipbb_show_debug();
	echo spipbb_show_log("spipbb");
	echo spipbb_show_log("spip");
	echo spipbb_show_log("mysql");

	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();
} // exec_spipbb_admin_debug

// ------------------------------------------------------------------------------
// [fr] Affiche les infos de debogage : les metas
// ------------------------------------------------------------------------------
function spipbb_show_debug()
{
	#$loc_meta = @unserialize($GLOBALS['meta']['spipbb']);
	$res = debut_cadre_trait_couleur('',true,'xxx',_T('spipbb:admin_debug_metas'));
	#$res.= "<fieldset style='border:1px solid #000;'><legend>SPIPBB META</legend>";
	#$res.= print_r_html($GLOBALS['spipbb'],true);
	$res.= affiche_metas_spipbb($GLOBALS['spipbb']);
	#$res.="</fieldset>";
	$res.= fin_cadre_trait_couleur(true);
	return $res;
} // spipbb_show_debug

// ------------------------------------------------------------------------------
// [fr] Affiche les infos de debogage : la log specifique
// ------------------------------------------------------------------------------
function spipbb_show_log($log_name="spipbb")
{
	$res="";
	$content="";
	// on lit la log principale
	@lire_fichier(_DIR_TMP.$log_name.".log", $content);
	// on lit la log precedente
	$content_1="";
	if (lire_fichier(_DIR_TMP.$log_name.".log.1", $content_1)) {
		$content = $content_1.$content;
	}
	$content=trim($content);
	if ($content) {
		// nettoyage
		$content=str_replace("\r\n","",$content); // pas besoin des pids ici
		$content=preg_replace(";\(pid.*?\);","",$content); // pas besoin des pids ici
		//$content=preg_replace(";^.*?rotate.*?$;","",$content); // pas besoin des rotates ici
		$content=str_replace("[-- rotate --]\n","",$content);
		// on passe en ordre chronologique inverse
		$log=explode("\n",$content);
		// logline : 	$m = date("M d H:i:s").' '.$GLOBALS['ip'].' '.$pid.' '.preg_replace("/\n*$/", "\n", $message);
		$new_log=array();
		$i=0;
		while (list(,$v)=each($log)) {
			// on verifie que la ligne lue n'est pas sans prefixe de date/adresse
			if (!preg_match("/(\w+) (\w+) (\d+:\d+:\d+) (\d+\.\d+.\d+.\d+) (.*)/i",$v,$matches) and $i>0) {
				/*
				// sinon on lui colle le prefixe de la ligne precedente (ligne rompue);
				$prec=$log[$k-1];
				@preg_match("/(\w+) (\w+) (\d+:\d+:\d+) (\d+\.\d+.\d+.\d+) (.*)/i",$prec,$matches);
				$v = $matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4]." ".$v;
				*/
				// Mieux
				// sinon on la recolle a la precedente ! et on n'incremente pas
				$new_log[$i-1]=$new_log[$i-1]." ".entites_html($v);
			} else {
				$new_log[$i]=entites_html($v);
				$i++;
			}
		}
		$log=$new_log;
		$log=array_reverse($log);
		$content=join("<br />\n",$log);
		$res .= debut_cadre_trait_couleur('',true,'xxx',_T('spipbb:admin_debug_log',array('log_name'=>$log_name)));
		$res .= "<div style='overflow:auto; width:100%; height: 50em; font-size:80%;border: 1px dashed #ada095;padding:2px;margin:2px;'>";
		$res .= $content;
		$res .= "</div>";
		$res .= fin_cadre_trait_couleur(true);
	}
	return $res;
} // spipbb_show_log

?>
