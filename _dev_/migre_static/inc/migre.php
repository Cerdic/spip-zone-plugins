<?php
#-----------------------------------------------------#
#  Plugin  : migre_static - Licence : GPL             #
#  File    : migre - include                          #
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

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['migrestatic_version'] = 0.83;
$GLOBALS['migrestatic'] = @unserialize($GLOBALS['meta']['migrestatic']);

// ------------------------------------------------------------------------------
// [fr] Initialisation des valeurs de meta du plugin aux defauts
// [en] Init plugin meta to default values
// ------------------------------------------------------------------------------
function migre_static_init_metas()
{
	global $migre_meta;
	migre_static_delete_metas(); // [fr] Nettoyage des traces [en] remove old metas
	unset($migre_meta);
	$migre_meta=array();
	$migre_meta['version']= $GLOBALS['migrestatic_version'];
	$migre_meta['migre_id_rubrique']= 0;
	$migre_meta['migreidmot']= "";
	$migre_meta['migre_liste_pages']= _T('migrestatic:liste_des_pages'); // from lang file
	$migre_meta['migre_test']= "checked";
	$migre_meta['migre_bcentredebut'] = ""; // was example : &lt;.{3,5}NAME.*index.{3,5}&gt
	$migre_meta['migre_bcentrefin'] = ""; // was example : &lt;.{3,5}END.*index.*&gt
	$migre_meta['migre_htos'] = get_list_htos();
	$migre_meta['migre_cs_decoupe']= "checked";
	if ($migre_meta!= $GLOBALS['meta']['migrestatic']) {
		include_spip('inc/meta');
		ecrire_meta('migre_static', serialize($migre_meta));
		ecrire_metas();
		$GLOBALS['migrestatic'] = @unserialize($GLOBALS['meta']['migrestatic']);
		spip_log('migrestatic : migre_static_init_metas OK');
	}
} // migre_static_init_metas

// ------------------------------------------------------------------------------
// [fr] Supprimer les metas du plugin (desinstallation)
// [en] Delete plugin metas
// ------------------------------------------------------------------------------
function migre_static_delete_metas()
{
	if (isset($GLOBALS['meta']['migrestatic'])) {
		include_spip('inc/meta');
		effacer_meta('migrestatic');
		ecrire_metas();
		unset($GLOBALS['migrestatic']);
		unset($GLOBALS['meta']['migrestatic']);
		spip_log('migrestatic : migre_static_delete_meta OK');
	}
} // migre_static_delete_metas

// ------------------------------------------------------------------------------
// [fr] Tableau de correspondance par defaut des differentes balises html
// [fr] On ne peut pas faire en une seule passe car certaines balises spip ont une syntaxe proche de celles du html...
// [fr] D autre part il se peut qu on veuille adapter la conversion a ses propres choix...
// [fr] Comme on peut les modifier dans des champs (pas des textarea) les \n et \r sont transformes en @n et @r
// ------------------------------------------------------------------------------
function get_list_htos()
{
	$htos=array();
	
	$htos['prem']['filtre']		="";				$htos['prem']['spip']=""; // un premier filtre pour l utilisateur
	
	$htos['comment']['filtre']	="/<!--.*-->/iUms";		$htos['comment']['spip']="";
	$htos['script']['filtre']	="/<(script|style)\b.+?<\/\\1>/i";	$htos['script']['spip']="";
	$htos['italique']['filtre']	=",<(i|em)( [^>@r]*)?".">(.+)</\\1>,Uims";	$htos['italique']['spip']="{\\3}";
	$htos['bold']['filtre']		=",<(b|h[4-6])( [^>]*)?".">(.+)</\\1>,Uims";	$htos['bold']['spip']="@@b@@\\3@@/b@@"; 
	$htos['h']['filtre']		=",<(h[1-3])( [^>]*)?".">(.+)</\\1>,Uims";	$htos['h']['spip']="@r{{{ \\3 }}}@r";
	$htos['tr']['filtre']		=",<tr( [^>]*)?".">,Uims";	$htos['tr']['spip']="<br>@r";
	$htos['thtd']['filtre']		=",<t[hd]( [^>]*)?".">,Uims";	$htos['thtd']['spip']=" | ";
	//$htos['thtd']['filtre']	=",<t[hd]( [^>]*)?".">,Uims";	$htos['thtd']['spip']="";
	$htos['br']['filtre']		="/<br.*>/iUs";			$htos['br']['spip']="";
	$htos['tbody']['filtre']	="/<\/*tbody.*>/iUs";		$htos['tbody']['spip']="";
	$htos['table']['filtre']	="/<\/*table.*>/iUs";		$htos['table']['spip']="";
	$htos['font']['filtre']		="/<\/*font.*>/iUs";		$htos['font']['spip']="";
	$htos['span']['filtre']		="/<\/*span.*>/iUs";		$htos['span']['spip']="";
	$htos['ulol']['filtre']		="/<\/*[uo]l.*>/iUs";		$htos['ulol']['spip']="";
	$htos['blockquote']['filtre']	="/<\/*blockquote.*>/iUs";	$htos['blockquote']['spip']="";
	$htos['div']['filtre']		="/<\/*div.*>/iUs";		$htos['div']['spip']="";
	$htos['hr']['filtre']		="/<hr.*>/iUs";			$htos['hr']['spip']="";
	$htos['bull']['filtre']		="/&bull;/";			$htos['bull']['spip']="@r@r-*";
	$htos['li']['filtre']		="/<li.*>/iUs";			$htos['li']['spip']="@r@r-*";
	$htos['slashli']['filtre']	="/<\/li>/iUs";			$htos['slashli']['spip']="";
	$htos['nbsp']['filtre']		="/&nbsp;/iUs";			$htos['nbsp']['spip']=" ";
	$htos['slashtrtd']['filtre']	=",</t[rhd]>,Uims";		$htos['slashtrtd']['spip']="@r";
	$htos['p']['filtre']		=",</*p.*>,Uims";		$htos['p']['spip']="";
	
	$htos['dern']['filtre']		="";				$htos['dern']['spip']=""; // un dernier filtre pour l utilisateur
	
	return $htos;
} // get_list_htos

// ------------------------------------------------------------------------------
// [fr] Cette fonction verirife que :
// [fr] - la liste est bien sur le meme site (petit controle de copyright)
// [fr] retourne un tableau avec la liste des URI des pages a telecharger. 
// [fr] Le document attendu doit respecter la syntaxe definie !!!
// ------------------------------------------------------------------------------
function get_list_of_pages($uri_list="")
{
	include_spip("inc/distant");
	$uri_pages=array();
	if (!empty($uri_list)) {
		$site=parse_url($uri_list); // urlencode ?
		$site_uri= $site[host];
		$dochtml = recuperer_page($uri_list,true);
		$prelist = preg_split("/\r\n|\n\r|\n|\r|\s| |\t/",$dochtml);
		reset($prelist);
		while (list($key,$val)=each($prelist)) {
			$val=preg_replace("/[\t| |\s]#.*/","",$val); // remove comments
			$val=preg_replace("/^#.*$/","",$val); // remove comments line
			if (!empty($val)) {
				$site=parse_url($val);
				if ($site[host] == $site_uri) {
					$uri_pages[]=$val;
				}
			}
		} // while
	}
	return $uri_pages;
} // get_list_of_pages

?>
