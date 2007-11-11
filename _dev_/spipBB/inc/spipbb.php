<?php
#--------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                      #
#  File    : inc/spipbb - donnees et fonctions du plugin #
#  Authors : Chryjs, 2007 et als                         #
#  Contact : chryjs!@!free!.!fr                          #
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

if (version_compare(substr($GLOBALS['spip_version_code'],0,5),'1.927','<')) {
	include_spip('inc/spipbb_192'); // SPIP 1.9.2
}
// else if (!function_exists('spip_insert_id')) include_spip('inc/vieilles_defs');

// [fr] Met a jour la version et initialise les metas
// [en] Upgrade release and init metas
function spipbb_upgrade_all()
{
	$version_code = $GLOBALS['spipbb_version'] ;
	if ( isset($GLOBALS['meta']['spipbb'] ) ) {
		if ( isset($GLOBALS['spipbb']['version'] ) ) {
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

	if ( version_compare(substr($installed_version,0,5),'0.2.2','<' ) ) { // 0.2.1 or schema
		include_spip('base/spipbb'); // inclure nouveau schema
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		spip_log('spipbb : spipbb_upgrade_all OK');
	}

	if ( version_compare(substr($installed_version,0,5),$version_code,'<' ) ) {
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
		$row = sql_fetsel('id_rubrique','spip_rubriques',
				array( "UPPER(titre) LIKE '%FORUM%'","id_parent=0") ,'','','1');
		if (!is_array($row)) // [fr] Selection de la premiere rubrique
			$row = sql_fetsel('id_rubrique','spip_rubriques',
				array("id_parent=0") ,'',array('0+titre','titre'),'1');

		$spipbb_meta['spipbb_id_rubrique']=  $row['id_rubrique'];
	}
	else $spipbb_meta['spipbb_id_rubrique']= $id_rubrique;

	$spipbb_meta['spipbb_squelette_groupeforum']= "groupeforum";
	$spipbb_meta['spipbb_squelette_filforum']= "filforum";

	// les mots cles specifiques
	$row = sql_fetsel('id_groupe','spip_groupes_mots', "titre = 'spipbb'" ,'','','1');
	$spipbb_meta['spipbb_id_groupe_mot']=intval($row['id_groupe']);

	if (empty($spipbb_meta['spipbb_id_groupe_mot']))
		$spipbb_meta = spipbb_creer_groupe_mot($spipbb_meta);
	else {
		// Verifier aussi la config de SPIP
		// Utiliser les mot cles oui
		// Autoriser l'ajout de mot cles aux forums oui

		$row = sql_fetsel('id_mot','spip_mots', array(
						"titre='ferme'",
						"id_groupe=".$spipbb_meta['spipbb_id_groupe_mot'])
				);
		if ( is_array($row) AND !empty($row['id_mot']) )
			$spipbb_meta['spipbb_id_mot_ferme']=intval($row['id_mot']);
		else
			$spipbb_meta['spipbb_id_mot_ferme'] = spipbb_init_mot_cle("ferme",$spipbb_meta['spipbb_id_groupe_mot']);

		$row = sql_fetsel('id_mot','spip_mots',array(
						"titre='annonce'",
						"id_groupe=".$spipbb_meta['spipbb_id_groupe_mot'])
				);
		if (is_array($row) AND !empty($row['id_mot']) )
			$spipbb_meta['spipbb_id_mot_annonce']=intval($row['id_mot']);
		else
			$spipbb_meta['spipbb_id_mot_annonce'] = spipbb_init_mot_cle("annonce",$spipbb_meta['spipbb_id_groupe_mot']);

		$row = sql_fetsel('id_mot','spip_mots',array(
						"titre='postit'",
						"id_groupe=".$spipbb_meta['spipbb_id_groupe_mot'])
				);
		if (is_array($row) AND !empty($row['id_mot']) )
			$spipbb_meta['spipbb_id_mot_postit']=intval($row['id_mot']);
		else
			$spipbb_meta['spipbb_id_mot_postit'] = spipbb_init_mot_cle("postit",$spipbb_meta['spipbb_id_groupe_mot']);

	} // if empty spipbb_meta

	// chemin icones et smileys ?

	// spam words
	$spipbb_meta['disable_sw'] = "non";
	$spipbb_meta['sw_nb_spam_ban'] = 3;
	$spipbb_meta['sw_ban_ip'] = "non";
	$spipbb_meta['sw_admin_can_spam'] = "oui";
	$spipbb_meta['sw_modo_can_spam'] = "oui";
	$spipbb_meta['sw_send_pm_warning'] = "non";
	$spipbb_meta['sw_warning_from_admin'] = 1; // id_auteur
	$spipbb_meta['sw_warning_pm_titre'] = _T('spipbb:sw_pm_spam_warning_titre');
	$spipbb_meta['sw_warning_pm_message'] = _T('spipbb:sw_pm_spam_warning_message');

	// final - sauver

	include_spip('inc/meta');
	ecrire_meta('spipbb', serialize($spipbb_meta));
	if (defined('_INC_SPIPBB_192')) ecrire_metas(); // Code 192
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
		effacer_meta('spipbb_fromphpbb'); // requis si la migration n est pas finie
		if (defined('_INC_SPIPBB_192')) ecrire_metas(); // Code 192
		unset($GLOBALS['meta']['spipbb']);
		spip_log('spipbb : delete_metas OK');
	}
} // spipbb_delete_metas

//----------------------------------------------------------------------------
// [fr] Supprimer les tables dans la base de d du plugin (desinstallation)
// [en] Delete plugin database tables
//----------------------------------------------------------------------------
function spipbb_delete_tables()
{
	global $tables_spipbb;
	include_spip('base/spipbb');
	reset($tables_spipbb);
	while ( list($key,$val) = each($tables_spipbb) )
	{
		$res=sql_query("DROP TABLE IF EXISTS $val ");
	}
} // spipbb_delete_tables

//----------------------------------------------------------------------------
// [fr] Met a jour les metas du plugin
// [en] Upgrade plugin metas
//----------------------------------------------------------------------------
function spipbb_upgrade_metas()
{
	spipbb_init_metas($GLOBALS['spipbb']['spipbb_id_rubrique']);
} // spipbb_delete_metas

//----------------------------------------------------------------------------
// [fr] Initialisation des valeurs de meta du plugin aux defauts
// [en] Init plugin meta to default values
//----------------------------------------------------------------------------
function spipbb_save_metas()
{
	$spipbb_meta=$GLOBALS['spipbb'];

	if ($id_rubrique = intval(_request('spipbb_id_rubrique')))
		$spipbb_meta['spipbb_id_rubrique'] =  $id_rubrique;
	if ($squelette_groupeforum = _request('spipbb_squelette_groupeforum'))
		$spipbb_meta['spipbb_squelette_groupeforum'] =  $squelette_groupeforum;
	if ($squelette_filforum = _request('spipbb_squelette_filforum'))
		$spipbb_meta['spipbb_squelette_filforum'] =  $squelette_filforum;
	if ($id_groupe_mot = intval(_request('spipbb_id_groupe_mot')))
		$spipbb_meta['spipbb_id_groupe_mot'] =  $id_groupe_mot;
	if ($id_mot_ferme = intval(_request('spipbb_id_mot_ferme')))
		$spipbb_meta['spipbb_id_mot_ferme'] =  $id_mot_ferme;
	if ($id_mot_annonce = intval(_request('spipbb_id_mot_annonce')))
		$spipbb_meta['spipbb_id_mot_annonce'] = $id_mot_annonce;
	if ($id_mot_postit = intval(_request('spipbb_id_mot_postit')))
		$spipbb_meta['spipbb_id_mot_postit'] = $id_mot_postit;

	// final - sauver

	include_spip('inc/meta');
	ecrire_meta('spipbb', serialize($spipbb_meta));
	if (defined('_INC_SPIPBB_192')) ecrire_metas(); // Code 192
	$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
	spip_log('spipbb : save_metas OK');

} // spipbb_save_metas

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
// Pour etre affich� dans la liste, le fichier doit etre dans exec/
// s'appeler spipbb_admin_XXXX
// et contenir une fonction / un objet ? associ� ... element de tableau ?
// ------------------------------------------------------------------------------
function spipbb_admin_gauche($id_rubrique=0,$adm="")
{
	$modules = array();

	$dir = @opendir(_DIR_PLUGIN_SPIPBB."exec/");

	$setmodules = 1; // permet d'activer le lien lors de l'include
	while( $file = @readdir($dir) )
	{
		if( preg_match("/^spipbb_admin.*?\.php$/", $file) )
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
	$res = "\n";
	while( list($cat, $action_array) = each($modules) )
	{
		$cat = _T('spipbb:admin_cat_'.$cat); // on traduit le nom de chaque categorie

		$res .= debut_boite_info(true). "<b>".$cat."</b>";
		ksort($action_array);
		while( list($action, $file) = each($action_array) )
		{
			$file = substr($file,0,-4); // supprimer l'extension
			$action = _T('spipbb:admin_action_'.$action) ; // on traduit le nom de chaque action(exec)
			if ( $adm <> $file ) {
				$lien = generer_url_ecrire($file, "id_rubrique=".$id_rubrique) ;
				$res .= "<a href='".$lien."' class='verdana2'>
					<div style='margin-top:2px;' class='bouton36blanc'
					onMouseOver=\"changeclass(this,'bouton36gris')\"
					onMouseOut=\"changeclass(this,'bouton36blanc')\">".$action.
					"</div></a>\n";
			}
			else {
				$res .= "<div style='margin-top:2px;' class='bouton36blanc'
					onMouseOver=\"changeclass(this,'bouton36gris')\"
					onMouseOut=\"changeclass(this,'bouton36blanc')\">".$action.
					"</div>\n";
			}
		}
		$res .= fin_boite_info(true)."\n";
	}
	$res .= "\n";

	// -- liste des fonctions

/*
Permissions
Delester
Smilies /avatars
Censure
Administration des Groupes
Gestion
Permissions
SPAM WORD Log
Administration des Utilisateurs
Controle du bannissement
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

// ------------------------------------------------------------------------------
// [fr] Verifie que les rubriques et les articles sont bien numerotes et les
// [fr] renumerote si besoin
// ------------------------------------------------------------------------------
function spipbb_renumerote()
{
	$id_secteur = $GLOBALS['spipbb']['spipbb_id_rubrique'];
	// les rubriques

	$result = sql_select("id_rubrique, titre", "spip_rubriques", array(
			"id_secteur='".$id_secteur."'",
			"id_rubrique!='".$id_secteur."'" ),	// array where
			'', array('titre') );
	$numero = 10;
	while ( $row = sql_fetch($result) )
	{
		$titre = supprimer_numero($row['titre']);
		$id_rubrique = $row['id_rubrique'];
		$titre = $numero . ". ".trim($titre);
		@sql_updateq('spip_rubriques', array(
						'titre'=>$titre
						),
				"id_rubrique='$id_rubrique'");
		$numero = $numero + 10;
	} // while

	// les articles

	$result = sql_select("A.id_article , A.titre", array("spip_articles AS A", "spip_rubriques AS R"),
			array("A.id_rubrique=R.id_rubrique","A.id_secteur='".$id_secteur."'"),
			'', array("R.titre", "A.titre") );
	$numero = 10;
	while ( $row = sql_fetch($result) )
	{
		$titre = supprimer_numero($row['titre']);
		$id_article = $row['id_article'];
		$titre = $numero . ". ".trim($titre);
		@sql_updateq('spip_articles', array(
						'titre'=>$titre
						),
				"id_article='$id_article'");
		$numero = $numero + 10;
	} // while
} // spipbb_renumerote

?>
