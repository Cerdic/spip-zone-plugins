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

include_spip("inc/spipbb");

// Module secifique de debogage
include_spip('inc/filtres.php');
$svn_revision = abs(version_svn_courante(_DIR_PLUGIN_SPIPBB));

if( !empty($setmodules) )
{
	if ( $svn_revision AND $svn_revision>0) {
		$file = basename(__FILE__);
		$modules['01_general']['ZZ_debug'] = $file;
	}
	return;
}

// ------------------------------------------------------------------------------
function exec_spipbb_admin_debug()
{
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:titre_spipbb'), "configuration", 'spipbb');

	echo gros_titre(_T('spipbb:titre_spipbb'),'',false) ;

	if (spipbb_is_configured()) {
		echo debut_grand_cadre(true);
		echo afficher_hierarchie($GLOBALS['meta']['spipbb']['spipbb_id_rubrique']);
		echo fin_grand_cadre(true);
	}

	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo  _T('spipbb:titre_spipbb');
	echo fin_boite_info(true);
	echo spipbb_admin_gauche('spipbb_admin_debug');
	echo creer_colonne_droite('', true);
	echo debut_droite('',true);

	echo spipbb_show_debug();

	echo fin_gauche(), fin_page();
} // exec_spipbb_admin_debug

// ------------------------------------------------------------------------------
// [fr] Affiche les infos de debogage
// ------------------------------------------------------------------------------
function spipbb_show_debug()
{
	$loc_meta = @unserialize($GLOBALS['meta']['spipbb']);
	$res = debut_cadre_trait_couleur('',true,'xxx','SpipBB DEBUG');
	$res.= "<fieldset><legend>SPIPBB META</legend>";
	$res.= print_r_html($loc_meta,true);
	$res.="</fieldset>";
	$res.= fin_cadre_trait_couleur(true);
	return $res;
} // spipbb_show_debug

// ------------------------------------------------------------------------------
// [fr] Formatte une sortie de print_r
// [en] Html-ize print_r output
// ------------------------------------------------------------------------------
function print_r_html($var,$return_data=false)
{
    $data = print_r($var,true);
    $data = str_replace( "  ","&nbsp;&nbsp;", $data);
    $data = str_replace( "\r\n","<br />\r\n", $data);
    $data = str_replace( "\r","<br />\r", $data);
    $data = str_replace( "\n","<br />\n", $data);

    if (!$return_data)
        echo $data;   
    else
        return $data;
}

?>
