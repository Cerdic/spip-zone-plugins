<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : inc/spipbb - donnees et fonctions du plugin   #
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

if (!defined("_ECRIRE_INC_VERSION")) return;
spip_log(__FILE__.' : included','spipbb');
if (defined("_INC_SPIPBB")) return; else define("_INC_SPIPBB", true);

if (!function_exists('plugin_get_infos')) include_spip('inc/plugin');

$infos=plugin_get_infos(_DIR_PLUGIN_SPIPBB);
$GLOBALS['spipbb_version'] = $infos['version'];

if (version_compare(substr($GLOBALS['spip_version_code'],0,5),'1.927','<')) {
	include_spip('inc/spipbb_192'); // SPIP 1.9.2
}

//----------------------------------------------------------------------------
// [fr] Verifie que spipbb est bien configure et a jour
// [en] Checks that spipbb is configured and uptodate
//----------------------------------------------------------------------------
function spipbb_is_configured() {
	spip_log('inc/spipbb.php spipbb_is_configured() glob:'.$GLOBALS['meta']['spipbb'],'spipbb');
	if ( !isset($GLOBALS['meta']['spipbb']) ) return false;
	$local_spipbb = @unserialize($GLOBALS['meta']['spipbb']);
	if ( empty($local_spipbb['version']) ) return false;
	if ( empty($local_spipbb['configure']) ) return false;
	if ( version_compare(substr($local_spipbb['version'],0,5),$GLOBALS['spipbb_version'],'<') ) return false;
	$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
	return true;
} // spipbb_is_configured

//----------------------------------------------------------------------------
// [fr] Met a jour la version et initialise les metas
// [en] Upgrade release and init metas
//----------------------------------------------------------------------------
function spipbb_upgrade_all()
{
	spip_log('inc/spipbb.php spipbb_upgrade_all()','spipbb');
	$version_code = $GLOBALS['spipbb_version'] ;
	if ( isset($GLOBALS['meta']['spipbb'] ) ) {
		if ( isset($GLOBALS['spipbb']['version'] ) ) {
			$installed_version = $GLOBALS['spipbb']['version'];
		}
		else {
			$installed_version = '0.1.0' ; // first release didn't store the release level
		}
	}
	else {
		$installed_version = '0.0.0' ; // aka not installed
	}
	if ( $installed_version == '0.0.0' ) {
		spipbb_init_metas();
	}

	if ( version_compare(substr($installed_version,0,5),'0.2.2','<' ) ) { // 0.2.1 or schema
		include_spip('base/spipbb'); // inclure nouveau schema
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		spip_log('inc/spipbb.php spipbb_upgrade_all creer_base installed_version:'.$installed_version,'spipbb');
	}
	// Revoir la config des tables ?
	if (!spipbb_check_tables()) {
		spipbb_delete_tables(); // on drop tout ?
		include_spip('base/spipbb'); // inclure nouveau schema
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		spip_log('inc/spipbb.php spipbb_upgrade_all recreer_base installed_version:'.$installed_version,'spipbb');
	}

	if ( version_compare(substr($installed_version,0,5),$version_code,'<' ) ) {
		spipbb_upgrade_metas($installed_version,$version_code);
	}

	spip_log('inc/spipbb.php : spipbb_upgrade_all() END iv:$installed_version: vc:$version_code','spipbb');
} /* spipbb_upgrade_all */

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
	unset($spipbb_meta);
	$spipbb_meta=array();
	$spipbb_meta['configure'] = $old_meta['configure'] ? $old_meta['configure'] :'non';
	$spipbb_meta['version']= $GLOBALS['spipbb_version'];
	$spipbb_meta['id_secteur'] = $old_meta['id_secteur'] ? $old_meta['id_secteur'] : 0;
	$spipbb_meta['config_id_secteur'] = $old_meta['config_id_secteur'] ? $old_meta['config_id_secteur'] : 'non';

	$spipbb_meta['squelette_groupeforum']= $old_meta['squelette_groupeforum'] ? $old_meta['squelette_groupeforum'] : "groupeforum";
	$spipbb_meta['squelette_filforum']= $old_meta['squelette_filforum'] ? $old_meta['squelette_filforum'] : "filforum";
	if ( find_in_path("groupeforum.html") AND find_in_path("filforum.html") )
		$spipbb_meta['config_squelette'] = 'oui';
	else 
		$spipbb_meta['config_squelette'] = 'non';

	// les mots cles specifiques
	$spipbb_meta['id_groupe_mot'] = $old_meta['id_groupe_mot'] ? $old_meta['id_groupe_mot'] : 0;
	$spipbb_meta['config_groupe_mots'] = $old_meta['config_groupe_mots'] ? $old_meta['config_groupe_mots'] : 'non';
	$spipbb_meta['id_mot_ferme'] = $old_meta['id_mot_ferme'] ? $old_meta['id_mot_ferme'] : 0;
	$spipbb_meta['id_mot_annonce'] = $old_meta['id_mot_annonce'] ? $old_meta['id_mot_annonce'] : 0;
	$spipbb_meta['id_mot_postit'] = $old_meta['id_mot_postit'] ? $old_meta['id_mot_postit'] : 0;
	$spipbb_meta['config_mot_cles'] = $old_meta['config_mot_cles'] ? $old_meta['config_mot_cles'] : 'non';

	// gafospip
	#stockage des champs supplementaires
	$spipbb_meta['support_auteurs'] = $old_meta['support_auteurs'] ? $old_meta['support_auteurs'] : 'extra'; //$options_sap = array('extra','table','autre');
	$spipbb_meta['table_support'] = $old_meta['table_support'] ? $old_meta['table_support'] : '';
	#champs supplementaires auteurs
	$champs_requis = array('date_crea_spipbb','avatar','annuaire_forum','refus_suivi_thread');
	$champs_definis=array();
	foreach ($GLOBALS['champs_sap_spipbb'] as $champ => $params) {
		$champs_definis[]=$champ;
	}
	$champs_optionnels = array_diff($champs_definis,$champs_requis);
	foreach ($champs_optionnels as $champ_a_valider) {
		$spipbb_meta['affiche_'.$champ_a_valider]=$old_meta['affiche_'.$champ_a_valider] ? $old_meta['affiche_'.$champ_a_valider] : 'oui';
	}
	# autres parametres
	$spipbb_meta['fixlimit'] = $old_meta['fixlimit'] ? $old_meta['fixlimit'] : 10;
	$spipbb_meta['lockmaint'] = $old_meta['lockmaint'] ? $old_meta['lockmaint'] : 600;
	$spipbb_meta['affiche_bouton_abus'] = $old_meta['affiche_bouton_abus'] ? $old_meta['affiche_bouton_abus'] : 'non';
	$spipbb_meta['affiche_bouton_rss'] = $old_meta['affiche_bouton_rss'] ? $old_meta['affiche_bouton_rss'] : 'un';
	$spipbb_meta['affiche_avatar'] = $old_meta['affiche_avatar'] ? $old_meta['affiche_avatar'] : 'oui';
	$spipbb_meta['taille_avatar_suj'] = $old_meta['taille_avatar_suj'] ? $old_meta['taille_avatar_suj'] : 50;
	$spipbb_meta['taille_avatar_cont'] = $old_meta['taille_avatar_cont'] ? $old_meta['taille_avatar_cont'] : 80;
	$spipbb_meta['taille_avatar_prof'] = $old_meta['taille_avatar_prof'] ? $old_meta['taille_avatar_prof'] : 80;
	$spipbb_meta['affiche_bouton_abus'] = $old_meta['affiche_bouton_abus'] ? $old_meta['affiche_bouton_abus'] : 'non';
	$spipbb_meta['affiche_bouton_rss'] = $old_meta['affiche_bouton_rss'] ? $old_meta['affiche_bouton_rss'] : 'un';
	// chemin icones et smileys ?

	// spam words
	$spipbb_meta['config_spam_words'] = $old_meta['config_spam_words'] ? $old_meta['config_spam_words'] : 'non';
	$spipbb_meta['sw_nb_spam_ban'] = $old_meta['sw_nb_spam_ban'] ? $old_meta['sw_nb_spam_ban'] : 3;
	$spipbb_meta['sw_ban_ip'] = $old_meta['sw_ban_ip'] ? $old_meta['sw_ban_ip'] : "non";
	$spipbb_meta['sw_admin_can_spam'] = $old_meta['sw_admin_can_spam'] ? $old_meta['sw_admin_can_spam'] : "non";
	$spipbb_meta['sw_modo_can_spam'] = $old_meta['sw_modo_can_spam'] ? $old_meta['sw_modo_can_spam'] : "non";
	$spipbb_meta['sw_send_pm_warning'] = $old_meta['sw_send_pm_warning'] ? $old_meta['sw_send_pm_warning'] : "non";
	$spipbb_meta['sw_warning_from_admin'] = $old_meta['sw_warning_from_admin'] ? $old_meta['sw_warning_from_admin'] : 1; // id_auteur
	$spipbb_meta['sw_warning_pm_titre'] = $old_meta['sw_warning_pm_titre'] ? $old_meta['sw_warning_pm_titre'] : _T('spipbb:sw_pm_spam_warning_titre');
	$spipbb_meta['sw_warning_pm_message'] = $old_meta['sw_warning_pm_message'] ? $old_meta['sw_warning_pm_message'] : _T('spipbb:sw_pm_spam_warning_message');

	// final - sauver
	spipbb_delete_metas(); // [fr] Nettoyage des traces [en] remove old metas

	include_spip('inc/meta');
	ecrire_meta('spipbb', serialize($spipbb_meta));
	if (defined('_INC_SPIPBB_192')) ecrire_metas(); // Code 192
	$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
	spip_log('inc/spipbb.php  : init_metas END '.$GLOBALS['meta']['spipbb'],'spipbb');
} // spipbb_init_metas

//----------------------------------------------------------------------------
// Importe les metas de GAFOSPIP s'ils existent, retourne un tableau
//----------------------------------------------------------------------------
function spipbb_import_gafospip_metas()
{
	if (isset($GLOBALS['meta']['gaf_install'])) {
		$tbl_conf=@unserialize($GLOBALS['meta']['gaf_install']);
		$spipbb_meta=array();
		$spipbb_meta['config_id_secteur']=$tbl_conf['groupe']; # num groupe
		$spipbb_meta['fixlimit']=$tbl_conf['fixlimit']; # n lignes
		$spipbb_meta['lockmaint']=$tbl_conf['lockmaint']=intval(_request('lockmaint')); # n secondes
		$spipbb_meta['support_auteurs']=$tbl_conf['support_auteurs']; # extra / table / autre
		$spipbb_meta['table_support']=$tbl_conf['table_support']; # nom table generique, ex. : auteurs_profils
		#$tbl_conf['champs_gaf']; # array xx,yy,zz
		$spipbb_meta['taille_avatar_suj']=$tbl_conf['taille_avatar_suj']; # nbr pix
		$spipbb_meta['taille_avatar_cont']=$tbl_conf['taille_avatar_cont']; # nbr pix
		$spipbb_meta['taille_avatar_prof']=$tbl_conf['taille_avatar_prof']; # nbr pix
		$spipbb_meta['affiche_avatar']=$tbl_conf['affiche_avatar']; # oui/non
		$spipbb_meta['affiche_bouton_abus']=$tbl_conf['affiche_bouton_abus']; # oui/non
		$spipbb_meta['affiche_bouton_rss']=$tbl_conf['affiche_bouton_rss']; # non/un/tout
		###$tbl_conf['affiche_signature']; # oui/non

		# affichage champ 'nnn_nnn'
		#champs supplementaires auteurs
		$champs_requis = array('date_crea_spipbb','avatar','annuaire_forum','refus_suivi_thread');
		$champs_definis=array();
		foreach ($GLOBALS['champs_sap_spipbb'] as $champ => $params) {
			$champs_definis[]=$champ;
		}
		$champs_optionnels = array_diff($champs_definis,$champs_requis);
		foreach ($champs_optionnels as $champ_a_valider) {
			$spipbb_meta['affiche_'.$champ_a_valider]=$tbl_conf['affiche_'.$champ_a_valider];
		}
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
		spip_log('inc/spipbb.php : delete_metas OK','spipbb');
	}
} // spipbb_delete_metas

//----------------------------------------------------------------------------
// [fr] Met a jour les metas du plugin
// [en] Upgrade plugin metas
//----------------------------------------------------------------------------
function spipbb_upgrade_metas($installed_version='',$version_code='')
{
	if ( version_compare(substr($installed_version,0,5),'0.3.0','<' ) ) {
		spipbb_init_metas();
	} // else si on fait des changements apres la 0.3.0
	spip_log('inc/spipbb.php : spipbb_upgrade_metas OK','spipbb');
} // spipbb_upgrade_metas

//----------------------------------------------------------------------------
// [fr] Initialisation des valeurs de meta du plugin aux defauts
// [en] Init plugin meta to default values
//----------------------------------------------------------------------------
function spipbb_save_metas()
{
	$GLOBALS['spipbb']['config_id_secteur'] = empty($GLOBALS['spipbb']['id_secteur']) ? 'non' : 'oui';
	$GLOBALS['spipbb']['config_groupe_mots'] = empty($GLOBALS['spipbb']['id_groupe_mot']) ? 'non' : 'oui';
	$GLOBALS['spipbb']['config_mot_cles'] = ( empty($GLOBALS['spipbb']['id_mot_ferme']) or
			empty($GLOBALS['spipbb']['id_mot_annonce']) or 
			empty($GLOBALS['spipbb']['id_mot_postit']) ) ? 'non' : 'oui';
	if ( find_in_path($GLOBALS['spipbb']['squelette_groupeforum']) AND 
		find_in_path($GLOBALS['spipbb']['squelette_filforum']) )
		$GLOBALS['spipbb']['config_squelette'] = 'oui';

	include_spip('inc/meta');
	ecrire_meta('spipbb', serialize($GLOBALS['spipbb']));
	if (defined('_INC_SPIPBB_192')) ecrire_metas(); // Code 192
	$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
	spip_log('inc/spipbb.php : spipbb_save_metas OK '.$GLOBALS['meta']['spipbb'],'spipbb');
} // spipbb_save_metas

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
	spip_log('inc/spipbb.php : spipbb_delete_tables END liste:'.$liste,'spipbb');
} // spipbb_delete_tables

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
	spip_log('inc/spipbb.php : spipbb_check_tables END','spipbb');
	return $res;
} // spipbb_check_tables

//----------------------------------------------------------------------------
// [fr] Verifier une table de la base de d du plugin
// [en] Check plugin database tables
//----------------------------------------------------------------------------
function spipbb_check_une_table($nom_table,$tables_principales)
{
	$res = sql_showtable($nom_table,true);
	if (!$res or !is_array($res)) { $res=array(); $res['field']=array(); }
	// une petite manip pour s'affranchir des differents formats des bases variees
	// on ne s'interresse pas aux index (pour le moment)
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
		spip_log(__FILE__.": spipbb_check_une_table diff(".$nom_table.") res:".join(",",$res['field']).":orig:".join(",",$table_origine['field']),'spipbb');
		return false ;
	}
	else return true;
} // spipbb_check_une_table

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
	spip_log('inc/spipbb.php : spipbb_creer_groupe_mot END','spipbb');
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
// [fr] Verifie que les rubriques et les articles sont bien numerotes et les
// [fr] renumerote si besoin
// ------------------------------------------------------------------------------
function spipbb_renumerote()
{
	$id_secteur = $GLOBALS['spipbb']['id_secteur'];
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
