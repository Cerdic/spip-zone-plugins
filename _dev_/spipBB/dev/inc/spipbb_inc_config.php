<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : inc/spipbb_inc_config                         #
#  Authors : Scoty, Chryjs 2007 et als                     #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : scoty!@!koakidi!.!com                         #
# [fr] Fonctions de configuration                          #
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

if (!defined("_ECRIRE_INC_VERSION")) return;
if (!defined('_INC_SPIPBB_COMMON')) include_spip('inc/spipbb_common');
spipbb_log("included",2,__FILE__);

//----------------------------------------------------------------------------
// controle config de SPIP
//----------------------------------------------------------------------------
function spipbb_check_spip_config() {
	// Les forums de SPIP sont ils actives ????????
	// en fait ce n'est pas obligatoire puisqu'il ne doivent letre que pour les articles geres.
	// Donc uniquement generer un avertissement. Et pas de blocage.
	if ( $GLOBALS['meta']['forums_publics']!='non' ) {
		$resultat="<li>"._T('spipbb:admin_spip_forums_ok')."</li>";
	} else {
		$resultat="<li>".propre(_T('spipbb:admin_spip_forums_warn',
					array('config_contenu'=>generer_url_ecrire('config_contenu','#configurer-participants') ) ) )."</li>"; //id configurer-participants
	}

	// utiliser mot cles
	// de meme si les mots cles ne sont pas actives ce n est pas un probleme
	// (a part dans les squelettes public a verifier)
	// On ne pourra pas avoir de ferme/annonce/postits...

	// mots_cles_forums articles_mots + mots_cles_forums
	if ( $GLOBALS['meta']['articles_mots']=='oui' ) {
		$resultat .= "<li>" . _T('spipbb:admin_spip_mots_cles_ok')."</li>";
	}
	else {
		$resultat .= "<li>" . propre(_T('spipbb:admin_spip_mots_cles_warn',
					array('configuration'=>generer_url_ecrire('configuration','#configurer-mots') ) ) )."</li>"; //id='configurer-mots'
	}
	if ( $GLOBALS['meta']['mots_cles_forums']=='oui' ) {
		$resultat .= "<li>". _T('spipbb:admin_spip_mots_forums_ok')."</li>";
	}
	else {
		$resultat .= "<li>" .propre(_T('spipbb:admin_spip_mots_forums_warn',
					array('configuration'=>generer_url_ecrire('configuration','#access-o') ) ) )."</li>"; //id=access-o
	}

	$resultat = _T('spipbb:admin_spip_config_forums')."<ul>".$resultat."</ul>";

	return array( true, $resultat );
} // spipbb_check_spip_config


//----------------------------------------------------------------------------
// controle presence plugins necessaires
//----------------------------------------------------------------------------
function spipbb_check_plugins_config() {
	$resultat="";
	$ok=0;
	//$res=array();
	$tab_plugins_installes = unserialize($GLOBALS['meta']['plugin']);
	if(!is_array($tab_plugins_installes['CFG'])) {
		$resultat.= "<li>".propre(_T('spipbb:admin_plugin_requis_erreur_cfg'))."</li>";
		//$res['CFG']=false;
		$ok++;
	} else {
		$resultat.= "<li>".propre(_T('spipbb:admin_plugin_requis_ok_cfg'))."</li>";
		$res['CFG']=true;
	}

	// Le plugin balise_session n'est plus necessaire depuis SPIP 1.945
	if (version_compare($GLOBALS['spip_version_code'],_SPIPBB_REV_BALISE_SESSION,'<')) {
		if (!is_array($tab_plugins_installes['BALISESESSION'])) {
			$resultat.= "<li>".propre(_T('spipbb:admin_plugin_requis_erreur_balisesession'))."</li>";
			//$res['BALISESESSION']=false;
			$ok++;
		} else {
			$resultat.= "<li>".propre(_T('spipbb:admin_plugin_requis_ok_balisesession'))."</li>";
			//$res['BALISESESSION']=true;
		}
	}
	if (0==$ok) $resultat = _T('spipbb:admin_plugin_requis_ok')."<ul>".$resultat."</ul>";
	else $resultat = http_img_pack('warning.gif', _T('info_avertissement'),"style='width: 48px; height: 48px; float: right;margin: 10px;'") .
					( ($ok>=2) ? _T('spipbb:admin_plugin_requis_erreur_s') : _T('spipbb:admin_plugin_requis_erreur') ) ."<ul>".$resultat."</ul>";
	return array( ($ok==0), $resultat );
} // spipbb_check_plugins_config


//----------------------------------------------------------------------------
// [fr] Verifier les tables dans la base de d du plugin
// [en] Check plugin database tables
//----------------------------------------------------------------------------
function spipbb_check_tables()
{
// Nouvelle façon de gérer la base. On stocke ici les informations.
// cf inc/interfaces.php
$tables_spipbb = array( 'spip_visites_forums', 'spip_auteurs_spipbb', 'spip_spam_words', 'spip_spam_words_log', 'spip_ban_liste' );

include_spip('base/serial');
include_spip('base/auxiliaires');


	global $tables_principales;
	include_spip('base/spipbb');
	reset($tables_spipbb);
	//$res=array();
	$ok=true;
	$res="";
	$resok="";
	while ( list(,$une_table) = each($tables_spipbb) )
	{
		//$res[$une_table]=
		if ( spipbb_check_une_table($une_table,$tables_principales) )
		{
			$resok.= $une_table.", ";
		}
		else
		{
			$res.= $une_table.", ";
			$ok=false;
		}
	}
	if ($ok) $res = propre(_T('spipbb:admin_config_tables_ok',array('tables_ok'=>substr($resok,0,-2))));
	else $res = http_img_pack('warning.gif', _T('info_avertissement'),"style='width: 48px; height: 48px; float: right;margin: 10px;'") .
				propre(_T('spipbb:admin_config_tables_erreur',array('tables_ok'=>substr($resok,0,-2),'tables_erreur'=>substr($res,0,-2))));

	$res = _T('spipbb:admin_config_tables')."<ul><li>".$res."</li></ul>";
	spipbb_log('END',2,__FILE__.":spipbb_check_tables()");
	return array($ok, $res);
} // spipbb_check_tables


//----------------------------------------------------------------------------
// [fr] Verifier une table de la base de d du plugin
// [en] Check plugin database tables
//----------------------------------------------------------------------------
function spipbb_check_une_table($nom_table,$tables_principales)
{
	$res = sql_showtable($nom_table,true);
	// une petite manip pour s'affranchir des differents formats des bases variees
	// on ne s'interre pas aux index pour le moment
	while ( list($k,$v) = each($res['field']) )
	{
		$param=preg_split("/\s/",$v);
		$champ=strtolower($param[0]);
		$champ=preg_replace("/^char(.*)/","varchar\\1",$champ); // char(x)==varchar(x) ?
		$champ=preg_replace("/^timestamp.*/","timestamp",$champ); // timestamp(14)==timestamp ?
		$champ=preg_replace("/^bigint.*/","bigint",$champ); // bigint(21)==bigint ?
		$champ=preg_replace("/^int.*/","int",$champ); // int(10)==int ?
		$champ=preg_replace("/^varcharacter/","varchar",$champ); //varcharacter == varchar
		$champ=preg_replace("/^varchar.*/","varchar",$champ); // varchar(10)==varchar ?
		$champ=preg_replace("/^integer/","int",$champ); //integer == int
		$champ=preg_replace("/^tinytext/","text",$champ); //tinytext == text
		$champ=preg_replace("/^mediumtext/","text",$champ); //mediumtext == text

		$res['field'][$k]=$champ;
	}
	$table_origine=$tables_principales[$nom_table];
	while ( list($k,$v) = each($table_origine['field']) )
	{
		$param=preg_split("/\s/",$v);
		$champ=$param[0];
		$champ=preg_replace("/^bigint.*/","bigint",$champ); // bigint(21)==bigint ?
		$champ=preg_replace("/^int.*/","int",$champ); // int(10)==int ?
		$champ=preg_replace("/^varchar.*/","varchar",$champ); // varchar(10)==varchar ?
		$champ=preg_replace("/^tinytext/","text",$champ); //tinytext == text
		$champ=preg_replace("/^mediumtext/","text",$champ); //mediumtext == text
		$table_origine['field'][$k]=strtolower($champ);
	}
	if ($res['field'] != $table_origine['field'] ) {
		spipbb_log("diff(".$nom_table.") res:".join(",",$res['field']).":orig:".join(",",$table_origine['field']),2,__FILE__." spipbb_check_une_table");
		return false ;
	}
	else return true;
} // spipbb_check_une_table


//----------------------------------------------------------------------------
// [fr] Supprimer les tables dans la base de d du plugin (desinstallation)
// [en] Delete plugin database tables
//----------------------------------------------------------------------------
function spipbb_delete_tables()
{
	global $tables_spipbb;
	include_spip('base/spipbb');
	reset($tables_spipbb);
	$liste="";
	while ( list($key,$val) = each($tables_spipbb) )
	{
		$res=sql_drop_table($val,true); // true => if exists
		$liste.="$val ";
	}
	spipbb_log(__FILE__.' spipbb_delete_tables END liste:'.$liste);
} // spipbb_delete_tables


//----------------------------------------------------------------------------
# ecrire tables spipbb
//----------------------------------------------------------------------------
function spipbb_ecrire_tables() {
	include_spip('base/spipbb');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();
}

//----------------------------------------------------------------------------
# installe des tables
//----------------------------------------------------------------------------
function spipbb_upgrade_tables($version_installee) {
	if($version_installee=='') {
		spipbb_ecrire_tables();
	}
	else {
		spipbb_maj_tables($version_installee);
		# et on repasse si nouvelles tables
		spipbb_ecrire_tables();
	}
} // spipbb_upgrade_tables

//----------------------------------------------------------------------------
# Les MaJ a appliquer sur anciennes tables ()
//----------------------------------------------------------------------------
function spipbb_maj_tables($version_installee) {
	## en prevision de modif sur table
	/*
	if($version_installee < nnn) {
		ALTER TABLE ...
	}
	*/
}

?>
