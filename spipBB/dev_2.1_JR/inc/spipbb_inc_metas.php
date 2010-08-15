<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : inc/spipbb_inc_metas                          #
#  Authors : chryjs, 2007 et als                           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#  [fr] Gestion des metas propre a spipbb                  #
#  [en] Manage spipbb metas                                #
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

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined("_INC_SPIPBB_COMMON")) include_spip('inc/spipbb_common');

spipbb_log("included",3,__FILE__);

//----------------------------------------------------------------------------
// [fr] Initialisation des valeurs de meta du plugin aux defauts
// [en] Init plugin meta to default values
//----------------------------------------------------------------------------
function spipbb_init_metas()
{
	// priorite a la config de spipbb
	$old_meta = @unserialize($GLOBALS['meta']['spipbb']); // recupere de vieilles metas

	// chargement de la config de gafospip le vrai !!
	if (!is_array($old_meta)) $old_meta=spipbb_import_gafospip_metas();

	reset($old_meta);
	while (list($k,$v)=each($old_meta)) {
		$old_meta[$k]=strtolower($v); // tout est en minuscules maintenant.
	}

	unset($spipbb_meta);

	$spipbb_meta=array();

	$spipbb_meta['configure'] = isset($old_meta['configure']) ? $old_meta['configure'] :'non';
	$spipbb_meta['version']= $GLOBALS['spipbb_plug_version'];
	$spipbb_meta['id_secteur'] = isset($old_meta['id_secteur']) ? $old_meta['id_secteur'] : 0;
	$spipbb_meta['config_id_secteur'] = isset($old_meta['config_id_secteur']) ? $old_meta['config_id_secteur'] : 'non';

	$spipbb_meta['squelette_groupeforum']= isset($old_meta['squelette_groupeforum']) ? $old_meta['squelette_groupeforum'] : "groupeforum";
	$spipbb_meta['squelette_filforum']= isset($old_meta['squelette_filforum']) ? $old_meta['squelette_filforum'] : "filforum";
	if ( find_in_path($spipbb_meta['squelette_groupeforum'].".html") AND find_in_path($spipbb_meta['squelette_filforum'].".html") )
		$spipbb_meta['config_squelette'] = 'oui';
	else
		$spipbb_meta['config_squelette'] = 'non';

	// les mots cles specifiques
	$spipbb_meta['id_groupe_mot'] = isset($old_meta['id_groupe_mot']) ? $old_meta['id_groupe_mot'] : 0;
	$spipbb_meta['config_groupe_mots'] = isset($old_meta['config_groupe_mots']) ? $old_meta['config_groupe_mots'] : 'non';
	$spipbb_meta['id_mot_ferme'] = isset($old_meta['id_mot_ferme']) ? $old_meta['id_mot_ferme'] : 0;
	$spipbb_meta['id_mot_annonce'] = isset($old_meta['id_mot_annonce']) ? $old_meta['id_mot_annonce'] : 0;
	$spipbb_meta['id_mot_postit'] = isset($old_meta['id_mot_postit']) ? $old_meta['id_mot_postit'] : 0;
	$spipbb_meta['config_mot_cles'] = isset($old_meta['config_mot_cles']) ? $old_meta['config_mot_cles'] : 'non';

	# autres parametres
	$spipbb_meta['fixlimit'] = isset($old_meta['fixlimit']) ? $old_meta['fixlimit'] : 30;
	$spipbb_meta['lockmaint'] = isset($old_meta['lockmaint']) ? $old_meta['lockmaint'] : 600;
	$spipbb_meta['affiche_bouton_abus'] = isset($old_meta['affiche_bouton_abus']) ? $old_meta['affiche_bouton_abus'] : 'non';
	$spipbb_meta['affiche_bouton_rss'] = isset($old_meta['affiche_bouton_rss']) ? $old_meta['affiche_bouton_rss'] : 'un';

	$spipbb_meta['affiche_bouton_abus'] = isset($old_meta['affiche_bouton_abus']) ? $old_meta['affiche_bouton_abus'] : 'non';
	$spipbb_meta['affiche_bouton_rss'] = isset($old_meta['affiche_bouton_rss']) ? $old_meta['affiche_bouton_rss'] : 'un';
	$spipbb_meta['affiche_membre_defaut'] = isset($old_meta['affiche_membre_defaut']) ? $old_meta['affiche_membre_defaut'] : 'non'; // c: 27/12/7 par defaut non pour respecter demande Scoty
	$spipbb_meta['log_level'] = isset($old_meta['log_level']) ? $old_meta['log_level'] : _SPIPBB_LOG_LEVEL; // c: 27/12/7 par defaut le niveau de log est a 3 (voir inc/spipbb_common
	$spipbb_meta['derniere_verif'] = isset($old_meta['derniere_verif']) ? $old_meta['derniere_verif'] : 0; // pas de date de verification par defaut
	$spipbb_meta['version_distant'] = isset($old_meta['version_distant']) ? $old_meta['version_distant'] : '0'; // pas de date de version distante par defaut

	// chemin icones et smileys ?

	// spam words
	$spipbb_meta['config_spam_words'] = isset($old_meta['config_spam_words']) ? $old_meta['config_spam_words'] : 'non';
	$spipbb_meta['sw_nb_spam_ban'] = isset($old_meta['sw_nb_spam_ban']) ? $old_meta['sw_nb_spam_ban'] : 3;
	$spipbb_meta['sw_ban_ip'] = isset($old_meta['sw_ban_ip']) ? $old_meta['sw_ban_ip'] : "non";
	$spipbb_meta['sw_admin_can_spam'] = isset($old_meta['sw_admin_can_spam']) ? $old_meta['sw_admin_can_spam'] : "non";
	$spipbb_meta['sw_modo_can_spam'] = isset($old_meta['sw_modo_can_spam']) ? $old_meta['sw_modo_can_spam'] : "non";
	$spipbb_meta['sw_send_pm_warning'] = isset($old_meta['sw_send_pm_warning']) ? $old_meta['sw_send_pm_warning'] : "non";
	$spipbb_meta['sw_warning_from_admin'] = isset($old_meta['sw_warning_from_admin']) ? $old_meta['sw_warning_from_admin'] : 1; // id_auteur
	$spipbb_meta['sw_warning_pm_titre'] = isset($old_meta['sw_warning_pm_titre']) ? $old_meta['sw_warning_pm_titre'] : _T('spipbb:sw_pm_spam_warning_titre');
	$spipbb_meta['sw_warning_pm_message'] = isset($old_meta['sw_warning_pm_message']) ? $old_meta['sw_warning_pm_message'] : _T('spipbb:sw_pm_spam_warning_message');

	// [fr] Nettoyage des traces [en] remove old metas
	spipbb_delete_metas();

	// Ecrire metas
	include_spip('inc/meta');
	ecrire_meta('spipbb', serialize($spipbb_meta));
	if (defined('_INC_SPIPBB_192')) ecrire_metas(); // Code 192

	// Redef. GLOBALS
	$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);

	spipbb_log('END '.$GLOBALS['meta']['spipbb'],3,"init_metas");
} // spipbb_init_metas

//----------------------------------------------------------------------------
// [fr] Liste des metas utilises avec valeur par defaut
// [en] metas used and their default values
//----------------------------------------------------------------------------
function spipbb_liste_metas()
{
	return array(
	'configure' 				=> 'non',
	'version' 					=> $GLOBALS['spipbb_plug_version'],
	'id_secteur' 				=> 0,
	'config_id_secteur' 		=> 'non',
	'squelette_groupeforum' 	=> 'groupeforum',
	'squelette_filforum' 		=> 'filforum',
	'config_squelette'			=> 'non',
	'id_groupe_mot'				=> 0,
	'config_groupe_mots' 		=> 'non',
	'id_mot_ferme' 				=> 0,
	'id_mot_annonce'			=> 0,
	'id_mot_postit' 			=> 0,
	'config_mot_cles' 			=> 'non',
	'affiche_refus_suivi_thread'=> 'oui',
	'fixlimit' 					=> 30,
	'lockmaint' 				=> 600,
	'affiche_bouton_abus' 		=> 'non',
	'affiche_bouton_rss' 		=> 'un',
	'affiche_bouton_abus' 		=> 'non',
	'affiche_bouton_rss' 		=> 'un',
	'affiche_membre_defaut' 	=> 'non',
	'log_level' 				=> _SPIPBB_LOG_LEVEL,
	'derniere_verif'			=>  0,
	'version_distant'			=> '0',
	'config_spam_words'			=> 'non',
	'sw_nb_spam_ban'			=> 3,
	'sw_ban_ip'					=> 'non',
	'sw_admin_can_spam'			=> 'non',
	'sw_modo_can_spam'			=> 'non',
	'sw_send_pm_warning'		=> 'non',
	'sw_warning_from_admin'		=> 1,
	'sw_warning_pm_titre'		=> _T('spipbb:sw_pm_spam_warning_titre'),
	'sw_warning_pm_message' 	=> _T('spipbb:sw_pm_spam_warning_message'),
	);
} // spipbb_liste_metas


//----------------------------------------------------------------------------
// Importe les metas de GAFOSPIP s'ils existent, retourne un tableau
//----------------------------------------------------------------------------
function spipbb_import_gafospip_metas()
{
	if (isset($GLOBALS['meta']['gaf_install'])) {
		$tbl_conf=@unserialize($GLOBALS['meta']['gaf_install']);
		$spipbb_meta=array();
		$spipbb_meta['fixlimit']=$tbl_conf['fixlimit']; # n lignes
		$spipbb_meta['lockmaint']=$tbl_conf['lockmaint']; # n secondes


		return $spipbb_meta;
	}
	else return array();
} // spipbb_import_gafospip_metas


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
		spipbb_log('OK inc/spipbb_inc_metas : delete_metas',3,'spipbb_delete_metas');
	}
} // spipbb_delete_metas

//----------------------------------------------------------------------------
// [fr] Initialisation des valeurs de meta du plugin aux defauts
// [en] Init plugin meta to default values
//----------------------------------------------------------------------------
function spipbb_save_metas()
{
	$GLOBALS['spipbb']['config_id_secteur'] = empty($GLOBALS['spipbb']['id_secteur']) ? 'non' : 'oui';
	$GLOBALS['spipbb']['config_groupe_mots'] = empty($GLOBALS['spipbb']['id_groupe_mot']) ? 'non' : 'oui';
	$GLOBALS['spipbb']['config_mot_cles'] = (
			empty($GLOBALS['spipbb']['id_mot_ferme']) or
			empty($GLOBALS['spipbb']['id_mot_annonce']) or
			empty($GLOBALS['spipbb']['id_mot_postit']) ) ? 'non' : 'oui';
	if ( find_in_path($GLOBALS['spipbb']['squelette_groupeforum']) AND
		find_in_path($GLOBALS['spipbb']['squelette_filforum']) )
		$GLOBALS['spipbb']['config_squelette'] = 'oui';

	include_spip('inc/meta');
	ecrire_meta('spipbb', serialize($GLOBALS['spipbb']));
	$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
	spipbb_log('OK '.$GLOBALS['meta']['spipbb'],3,"spipbb_save_metas");
} // spipbb_save_metas

//----------------------------------------------------------------------------
// [fr] Met a jour les metas du plugin
// [en] Upgrade plugin metas
//----------------------------------------------------------------------------
function spipbb_upgrade_metas($installed_version='',$version_code='') {

	if($installed_version=='') {
		$installed_version=='0.0.0.0';
	}

	if ( version_compare($installed_version,$version_code,'<' ) ) {
		spipbb_init_metas();
	}
	// else si on fait des changements apres la 0.3.0

	spipbb_log("OK from:$installed_version:to:$version_code",3,"spipbb_upgrade_metas");
} // spipbb_upgrade_metas

//----------------------------------------------------------------------------
// [fr] Cree le mot cle donne dans le groupe donne et retourne son id_mot
//----------------------------------------------------------------------------
function spipbb_init_mot_cle($mot,$id_groupe)
{
echo "\n<br>call $mot $id_groupe";
	if (empty($mot) OR empty($id_groupe)) return 0;
	$groupe_mot = sql_fetsel ("titre","spip_groupes_mots",array("id_groupe"=>$id_groupe));
	$res = sql_fetsel('id_mot','spip_mots',"titre='$mot'");
	if (!$res) {
		$id_mot = sql_insertq("spip_mots",array(
				'titre'=>$mot,
				'id_groupe'=>$id_groupe,
				'descriptif'=> _T('spipbb:mot_'.$mot),
				'type' => $groupe_mot['titre'])
			);
echo "insert mot $mot ret $id_mot <br>\n";			
	} else $id_mot = $res['id_mot'];
	return $id_mot;
} // spipbb_init_mot_cle


?>