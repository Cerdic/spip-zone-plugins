<?php
#--------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                      #
#  File    : inc/spipbb - donnees et fonctions du plugin #
#  Authors : Chryjs, 2007 et als                         #
#  Contact : chryjs¡@!free¡.!fr                          #
#--------------------------------------------------------#

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
if (defined("_INC_SPIPBB")) return; else define("_INC_SPIPBB", true);

if (!function_exists('plugin_get_infos')) include_spip('inc/plugin');

$infos=plugin_get_infos(_DIR_PLUGIN_SPIPBB);
$GLOBALS['spipbb_version'] = $infos['version']; // was 0.12
$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);

if (!function_exists('spip_insert_id')) include_spip('inc/vieilles_defs');

//----------------------------------------------------------------------------
// [fr] Initialisation des valeurs de meta du plugin aux defauts
// [en] Init plugin meta to default values
//----------------------------------------------------------------------------
function spipbb_init_metas($id_rubrique=0)
{
	spipbb_delete_metas(); // [fr] Nettoyage des traces [en] remove old metas
	unset($spipbb_meta);
	$spipbb_meta=array();
	$spipbb_meta['version']= $GLOBALS['spipbb_version'];
	$id_rubrique=intval($id_rubrique);
	if (empty($id_rubrique)) {
		$row = spip_fetch_array(spip_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent=0 ORDER by 0+titre,titre LIMIT 1")); // SELECT the first rubrique met
		$spipbb_meta['spipbb_id_rubrique']=  $row['id_rubrique'];
	}
	else $spipbb_meta['spipbb_id_rubrique']= $id_rubrique;

	$spipbb_meta['spipbb_squelette_groupeforum']= "groupeforum";
	$spipbb_meta['spipbb_squelette_filforum']= "filforum";

	// les mots cles specifiques
	$row = spip_fetch_array(spip_query("SELECT id_groupe FROM spip_groupes_mots WHERE titre='spipbb' LIMIT 1"));
	$spipbb_meta['spipbb_id_groupe_mot']=intval($row['id_groupe']);

	if (empty($spipbb_meta['spipbb_id_groupe_mot']))
		$spipbb_meta = spipbb_creer_groupe_mot($spipbb_meta);
	else {

		$row = spip_fetch_array(spip_query("SELECT id_mot FROM spip_mots WHERE titre='ferme' AND id_groupe='".$spipbb_meta['spipbb_id_groupe_mot']."'"));
		if ( is_array($row) AND !empty($row['id_mot']) )
			$spipbb_meta['spipbb_id_mot_ferme']=intval($row['id_mot']);
		else
			$spipbb_meta['spipbb_id_mot_ferme'] = spipbb_init_mot_cle("ferme",$spipbb_meta['spipbb_id_groupe_mot']);

		$row = spip_fetch_array(spip_query("SELECT id_mot FROM spip_mots WHERE titre='annonce' AND id_groupe='".$spipbb_meta['spipbb_id_groupe_mot']."'"));
		if (is_array($row) AND !empty($row['id_mot']) )
			$spipbb_meta['spipbb_id_mot_annonce']=intval($row['id_mot']);
		else
			$spipbb_meta['spipbb_id_mot_annonce'] = spipbb_init_mot_cle("annonce",$spipbb_meta['spipbb_id_groupe_mot']);

		$row = spip_fetch_array(spip_query("SELECT id_mot FROM spip_mots WHERE titre='postit' AND id_groupe='".$spipbb_meta['spipbb_id_groupe_mot']."'"));
		if (is_array($row) AND !empty($row['id_mot']) )
			$spipbb_meta['spipbb_id_mot_postit']=intval($row['id_mot']);
		else
			$spipbb_meta['spipbb_id_mot_postit'] = spipbb_init_mot_cle("postit",$spipbb_meta['spipbb_id_groupe_mot']);

	} // if empty spipbb_meta

	// chemin icones et smileys ?
	// final - sauver

	if ($spipbb_meta!= $GLOBALS['meta']['spipbb'])
	{
		include_spip('inc/meta');
		ecrire_meta('spipbb', serialize($spipbb_meta));
		ecrire_metas();
		$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
		spip_log('spipbb : init_metas OK');
	}

} // spipbb_init_metas

//----------------------------------------------------------------------------
// [fr] Supprimer les metas du plugin (desinstallation)
// [en] Delete plugin metas
//----------------------------------------------------------------------------
function spipbb_delete_metas()
{
	if (isset($GLOBALS['meta']['spipbb']))
	{
		include_spip('inc/meta');
		effacer_meta('spipbb');
		ecrire_metas();
		unset($GLOBALS['meta']['spipbb']);
		spip_log('spipbb : delete_metas OK');
	}
} // spipbb_delete_metas

//----------------------------------------------------------------------------
// [fr] Initialise le groupe de mot cles necessaire pour spipbb
//----------------------------------------------------------------------------
function spipbb_creer_groupe_mot($l_meta)
{
	$res = spip_query("INSERT INTO spip_groupes_mots SET titre='spipbb'");
	$l_meta['spipbb_id_groupe_mot']= spip_insert_id();
	$res = spip_query("INSERT INTO spip_mots SET titre='ferme', id_groupe='".$l_meta['spipbb_id_groupe_mot']."'");
	$l_meta['spipbb_id_mot_ferme'] = spipbb_init_mot_cle("ferme",$l_meta['spipbb_id_groupe_mot']);
	$l_meta['spipbb_id_mot_annonce'] = spipbb_init_mot_cle("annonce",$l_meta['spipbb_id_groupe_mot']);
	$l_meta['spipbb_id_mot_postit'] = spipbb_init_mot_cle("postit",$l_meta['spipbb_id_groupe_mot']);
	return $l_meta;
} // spipbb_creer_groupe_mot

//----------------------------------------------------------------------------
// [fr] Cree le mot cle donne dans le groupe donne et retourne son id_mot
//----------------------------------------------------------------------------
function spipbb_init_mot_cle($mot,$groupe)
{
	if (empty($mot) OR empty($groupe)) return 0;
	$res = spip_query("INSERT INTO spip_mots SET titre='$mot', id_groupe='$groupe'");
	$id_mot = spip_insert_id();
	return $id_mot;
} // spipbb_init_mot_cle

// ------------------------------------------------------------------------------
// [fr] Construit la liste des choix de l'admin spipbb en fonction de ce qui est
// disponible
// Pour etre affiché dans la liste, le fichier doit etre dans exec/
// s'appeler spipbb_admin_XXXX
// et contenir une fonction / un objet ? associé ... element de tableau ?
// ------------------------------------------------------------------------------
function spipbb_admin_gauche($id_rubrique=0,$adm="")
{
	$modules = array();
	$modules['general']['index']= "spipbb_admin.php"; // config par defaut

	$dir = @opendir(_DIR_PLUGIN_SPIPBB."exec/");

	$setmodules = 1; // permet d'activer le lien lors de l'include
	while( $file = @readdir($dir) )
	{
		if( preg_match("/^spipbb_admin_.*?\.php$/", $file) )
		{
			// chaque fichier inclu doit contenir ceci (par exemple) en entete :
			//if( !empty($setmodules) )
			//{
			//	$file = basename(__FILE__);
			//	$modules['General']['Configuration'] = $file;
			//	return;
			//}
			@include( _DIR_PLUGIN_SPIPBB . "exec/" . $file);
		}
	}
	@closedir($dir);
	unset($setmodules);

	ksort($modules);
	$res = "<ul>";
	while( list($cat, $action_array) = each($modules) )
	{
		$cat = _T('spipbb:admin_cat_'.$cat); // on traduit le nom de chaque categorie
		$res .= "\n<li>".$cat."\n<ul>";
		ksort($action_array);
		while( list($action, $file) = each($action_array) )
		{
			$file = substr($file,0,-4); // supprimer l'extension
			$action = _T('spipbb:admin_action_'.$action) ; // on traduit le nom de chaque action(exec)
			if ( $adm <> $file ) {
				$lien = generer_url_ecrire($file, "id_rubrique=".$id_rubrique) ;
				$res .= "<li><a href='".$lien."' class='verdana2'>".$action."</a></li>" ;
			}
			else {
				$res .= "<li>".$action."</li>" ;
			}
		}
		$res .= "\n</ul></li>";
	}
	$res .= "</ul>";

//	$res = debut_cadre_relief("", true, '',_T('spipbb:admin_moderation')) . $res .
//		fin_cadre_relief(true);
	$res = debut_boite_info(true) . $res . fin_boite_info(true);

	// -- liste des fonctions

/*
Index de l'Administration
Aperçu du Forum
Configuration
Gestion
Permissions
Délester
Administration Générale
E-mail de Masse
Restaurer la base de données
Smilies
Censure
Administration des Groupes
Gestion
Permissions
Spam Words
Configuration
Flagged Posts
Log
Manage Words
Administration des Thèmes
Menu
Administration des Utilisateurs
Contrôle du bannissement
Interdire un nom d'utilisateur
Flags
Link Checker
Gestion
Mass Delete Users
Permissions
Rangs
Supprimer les membres inactifs
Utilisateurs inactifs
*/
	return $res;
}


?>
