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
	$res=array();
	// Les forums de SPIP sont ils actives ????????
	if ( $GLOBALS['meta']['forums_publics']!='non' ) {
		$res['forums_publics']= array( 'etat'=>true, 'message'=>_T('spipbb:admin_spip_forums_ok'));
		$resultat=_T('spipbb:admin_spip_forums_ok');
	} else {
		$res['forums_publics']= array( 'etat'=>false, 
										'message'=>_T('spipbb:admin_spip_forums_erreur')
										//'message' => spipbb_
										);
		$resultat=_T('spipbb:admin_spip_forums_erreur');
	}

	// utiliser mot cles 
	// mots_cles_forums articles_mots + mots_cles_forums
	if ( $GLOBALS['meta']['articles_mots']=='oui' ) {
		$res['articles_mots']= array( 'etat'=>true, 
										'message'=>_T('spipbb:admin_spip_mots_cles_ok')
									
										);
		$resultat=_T('spipbb:admin_spip_mots_cles_ok');
	}
	else {
		//c: 11/1/8 Pret pour la configuration :
		//$spipbb_mots = charger_fonction('spipbb_mots', 'configuration');
		//$config_mots = $spipbb_mots();

		$res['articles_mots']= array( 'etat'=>false, 
										'message'=>_T('spipbb:admin_spip_mots_cles_erreur')
										//'message'=> $config_mots,
										);
		$resultat=_T('spipbb:admin_spip_mots_cles_erreur');
	}
	$resultat.="<br />";
	if ( $GLOBALS['meta']['mots_cles_forums']=='oui' ) {
		$resultat.=_T('spipbb:admin_spip_mots_forums_ok');
		$res['mots_cles_forums']= array( 'etat'=>true, 'message'=>_T('spipbb:admin_spip_mots_forums_ok'));
	}
	else {
		$res['mots_cles_forums']= array( 'etat'=>false, 'message'=>_T('spipbb:admin_spip_mots_forums_erreur'));
		$resultat.=_T('spipbb:admin_spip_mots_forums_erreur');
	}
	return $res;
	//return $resultat;
} // spipbb_check_spip_config


//----------------------------------------------------------------------------
// controle presence plugins necessaires
//----------------------------------------------------------------------------
function spipbb_check_plugins_config() {
	$resultat="";
	$res=array();
	$tab_plugins_installes = unserialize($GLOBALS['meta']['plugin']);
	if(!is_array($tab_plugins_installes['CFG'])) {
		$resultat.= "<li>"._T('spipbb:admin_plugin_requis_erreur')." CFG</li>";
		$res['CFG']=false;
	} else {
		$resultat.= "<li>"._T('spipbb:admin_plugin_requis_ok')." CFG</li>";
		$res['CFG']=true;
	}

	// Le plugin balise_session n'est plus necessaire depuis SPIP 1.945
	if (version_compare(substr($GLOBALS['spip_version_code'],0,6),_SPIPBB_REV_BALISE_SESSION,'<')) {
		if (!is_array($tab_plugins_installes['BALISESESSION'])) {
			$resultat.= "<li>"._T('spipbb:admin_plugin_requis_erreur')." BALISESESSION</li>";
			$res['BALISESESSION']=false;
		} else {
			$resultat.= "<li>"._T('spipbb:admin_plugin_requis_ok')." BALISESESSION</li>";
			$res['BALISESESSION']=true;
		}
	}
	if ($resultat) $resultat="<ul>".$resultat."</ul>";
	return $res;
	//return $resultat;
} // spipbb_check_plugins_config


//----------------------------------------------------------------------------
// [fr] Verifier les tables dans la base de d du plugin
// [en] Check plugin database tables
//----------------------------------------------------------------------------
function spipbb_check_tables()
{
	global $tables_spipbb,$tables_principales;
	include_spip('base/spipbb');
	reset($tables_spipbb);
	$res=array();
	while ( list(,$une_table) = each($tables_spipbb) )
	{
		$res[$une_table]=spipbb_check_une_table($une_table,$tables_principales);
	}
	spipbb_log('END',2,__FILE__.":spipbb_check_tables()");
	return $res;
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
		$res['field'][$k]=$champ;
	}
	$table_origine=$tables_principales[$nom_table];
	while ( list($k,$v) = each($table_origine['field']) )
	{
		$param=preg_split("/\s/",$v);
		$table_origine['field'][$k]=strtolower($param[0]);
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
		$res=sql_query("DROP TABLE IF EXISTS $val ");
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
