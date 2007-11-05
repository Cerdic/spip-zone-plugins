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
$GLOBALS['spipbb_version'] = $infos['version'];
$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);

if (version_compare(substr($GLOBALS['spip_version_code'],0,5),'1.925','<')) {
	include_spip('inc/spipbb_192'); // SPIP 1.9.2
}
// else if (!function_exists('spip_insert_id')) include_spip('inc/vieilles_defs');

// [fr] Met a jour la version et initialise les metas
// [en] Upgrade release and init metas
function spipbb_upgrade_all()
{
	$version_code = $GLOBALS['spipbb_version'] ;
	if ( isset($GLOBALS['meta']['spipbb'] ) )
	{
		if ( isset($GLOBALS['spipbb']['version'] ) )
		{
			$installed_version = $GLOBALS['spipbb']['version'];
		}
		else {
			$installed_version = 0.10 ; // first release didn't store the release level
		}
	}
	else {
		$installed_version = 0.0 ; // aka not installed
	}
	if ( $installed_version == 0.0 ) {
		spipbb_init_metas();
	}

	if ( $installed_version < 0.14 ) // 0.14 or schema
	{
		include_spip('base/spipbb');
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		spip_log('spipbb : spipbb_upgrade_all OK');
	}

	if ( $installed_version < $version_code ) {
		spipbb_upgrade_metas();
	}

	spip_log('spipbb : spipbb_upgrade_all OK');
} /* spipbb_upgrade_all */

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
		//spip_fetch_array(spip_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent=0 ORDER by 0+titre,titre LIMIT 1")); // SELECT the first rubrique met
		$row = sql_fetsel('id_rubrique','spip_rubriques',array('id_parent'=>0),'',array('0+titre','titre'),'1');

		$spipbb_meta['spipbb_id_rubrique']=  $row['id_rubrique'];
	}
	else $spipbb_meta['spipbb_id_rubrique']= $id_rubrique;

	$spipbb_meta['spipbb_squelette_groupeforum']= "groupeforum";
	$spipbb_meta['spipbb_squelette_filforum']= "filforum";

	// les mots cles specifiques
	// spip_fetch_array(spip_query("SELECT id_groupe FROM spip_groupes_mots WHERE titre='spipbb' LIMIT 1"));
	$row = sql_fetsel('id_groupe','spip_groupes_mots',array('titre'=>'spipbb'),'','','1');

	$spipbb_meta['spipbb_id_groupe_mot']=intval($row['id_groupe']);

	if (empty($spipbb_meta['spipbb_id_groupe_mot']))
		$spipbb_meta = spipbb_creer_groupe_mot($spipbb_meta);
	else {
		// Verifier aussi la config de SPIP
		// Utiliser les mot cles oui
		// Autoriser l'ajout de mot cles aux forums oui
		// spip_fetch_array(spip_query("SELECT id_mot FROM spip_mots WHERE titre='ferme' AND id_groupe='".$spipbb_meta['spipbb_id_groupe_mot']."'"));
		$row = sql_fetsel('id_mot','spip_mots', array(
						'titre'=>'ferme',
						'id_groupe'=>$spipbb_meta['spipbb_id_groupe_mot'])
				);
		if ( is_array($row) AND !empty($row['id_mot']) )
			$spipbb_meta['spipbb_id_mot_ferme']=intval($row['id_mot']);
		else
			$spipbb_meta['spipbb_id_mot_ferme'] = spipbb_init_mot_cle("ferme",$spipbb_meta['spipbb_id_groupe_mot']);

		//$row = spip_fetch_array(spip_query("SELECT id_mot FROM spip_mots WHERE titre='annonce' AND id_groupe='".$spipbb_meta['spipbb_id_groupe_mot']."'"));
		$row = sql_fetsel('id_mot','spip_mots',array(
						'titre'=>'annonce',
						'id_groupe'=>$spipbb_meta['spipbb_id_groupe_mot'])
				);
		if (is_array($row) AND !empty($row['id_mot']) )
			$spipbb_meta['spipbb_id_mot_annonce']=intval($row['id_mot']);
		else
			$spipbb_meta['spipbb_id_mot_annonce'] = spipbb_init_mot_cle("annonce",$spipbb_meta['spipbb_id_groupe_mot']);

		//$row = spip_fetch_array(spip_query("SELECT id_mot FROM spip_mots WHERE titre='postit' AND id_groupe='".$spipbb_meta['spipbb_id_groupe_mot']."'"));
		$row = sql_fetsel('id_mot','spip_mots',array(
						'titre'=>'postit',
						'id_groupe'=>$spipbb_meta['spipbb_id_groupe_mot'])
				);
		if (is_array($row) AND !empty($row['id_mot']) )
			$spipbb_meta['spipbb_id_mot_postit']=intval($row['id_mot']);
		else
			$spipbb_meta['spipbb_id_mot_postit'] = spipbb_init_mot_cle("postit",$spipbb_meta['spipbb_id_groupe_mot']);

	} // if empty spipbb_meta

	// chemin icones et smileys ?
	// final - sauver

	include_spip('inc/meta');
	ecrire_meta('spipbb', serialize($spipbb_meta));
	ecrire_metas();
	$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
	spip_log('spipbb : init_metas OK');

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
// [fr] Met à jour les metas du plugin
// [en] Upgrade plugin metas
//----------------------------------------------------------------------------
function spipbb_upgrade_metas()
{
	spipbb_init_metas($GLOBALS['spipbb']['spipbb_id_rubrique']);
} // spipbb_delete_metas


//----------------------------------------------------------------------------
// [fr] Initialise le groupe de mot cles necessaire pour spipbb
//----------------------------------------------------------------------------
function spipbb_creer_groupe_mot($l_meta)
{
	$res = sql_insertq("spip_groupes_mots",array(
				'titre' => 'spipbb',
				'descriptif' => _T('spipbb:mot_groupe_moderation'),
				'articles' => 'oui',
				'rubriques' => 'oui',
				'minirezo' => 'oui',
				'comite' => 'oui',
				'forum' => 'oui' )
			);

	$l_meta['spipbb_id_groupe_mot']= $res;
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
	$groupe_mot = sql_fetsel ("titre","spip_groupes_mots",array("id_groupe"=>$groupe));
	$id_mot = sql_insertq("spip_mots",array(
				'titre'=>$mot,
				'id_groupe'=>$groupe,
				'descriptif'=> _T('spipbb:mot_'.$mot),
				'type' => $groupe_mot['titre'])
			);
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
	$modules['01_general']['01_index']= "spipbb_admin.php"; // config par defaut

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
