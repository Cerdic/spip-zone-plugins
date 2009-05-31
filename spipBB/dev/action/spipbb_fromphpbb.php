<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : action/spipbb_fromphpbb - import de phpbb     #
#  Authors : chryjs, 2007                                  #
#            2004+ Jean-Luc Bechennec certaines fonctions  #
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

// [fr] Etape de realisation de la migration
// [en] Migration step

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

global $time_start;
$time_start = array_sum(explode(' ', microtime()));;

global $spipbb_fromphpbb; // stockage des informations et des etapes

include_spip('inc/minipres');
include_spip('inc/spipbb_init');
include_spip('inc/presentation');

ini_set('max_execution_time',600); // pas toujours possible mais requis pour etape 2 et surtout 3!

// ------------------------------------------------------------------------------
// [fr] Verification et declenchement de l'operation
// ------------------------------------------------------------------------------
function action_spipbb_fromphpbb()
{
	global $spip_lang_left, $spipbb_fromphpbb, $dir_lang, $time_start;
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_rubrique = intval($arg);
	if (empty($id_rubrique))
	{
		minipres( _T('spipbb:admin_titre_page_spipbb_admin_migre',array('nom_base'=>'PhpBB')), "<strong>"._T('avis_non_acces_page')."</strong>" );
		exit;
	}

	fromppbb_load_metas($id_rubrique);
	$step = $spipbb_fromphpbb['etape'];

	$go_back = generer_url_ecrire("naviguer","id_rubrique=$id_rubrique");
	$link_back  = icone(_T('icone_retour'), $go_back, "rubrique-12.gif", "rien.gif", ' ',false);
	$corps = $link_back;

//$corps  .= "\n<div $dir_lang style='width:98%;height: 98%;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>" ;

	switch ($step) {
	case 1 :
		// migration des categories(rubriques) et des titres de forums(articles)
		$corps .= migre_categories_forums();
		$form   = "<input type='hidden' name='etape' id='etape' value='2'>";
		$form  .= "<div align='right'><input class='fondo' type='submit' value='"._T('bouton_suivant')."' /></div>" ;
		$corps .= generer_action_auteur("spipbb_fromphpbb",$id_rubrique, $retour, $form," method='post' name='formulaire'");
		$corps .= $link_back;
		$spipbb_fromphpbb['etape']++;
		fromphpbb_save_metas();
		break;
	case 2 :
		// migration des utilisateurs(auteurs)
		$corps .= migre_utilisateurs();
		$form   = "<input type='hidden' name='etape' id='etape' value='3'>";
		$form  .= "<div align='right'><input class='fondo' type='submit' value='"._T('bouton_suivant')."' /></div>" ;
		$corps .= generer_action_auteur("spipbb_fromphpbb",$id_rubrique, $retour,$form," method='post' name='formulaire'");
		$corps .= $link_back;
		$spipbb_fromphpbb['etape']++;
		fromphpbb_save_metas();
		break;
	case 3 :
		// migration des threads(forums)
		$corps .= migre_threads();
		$corps .= $link_back;
		fromphpbb_delete_metas();
		break;
	default : // manque mp
		$corps .= "<strong>"._T('avis_non_acces_page')."</strong>";
	}
	$time = array_sum(explode(' ', microtime())) - $time_start;
	$corps .= "\n<!-- Elapsed: $time secondes -->";

	echo minipres(_T('spipbb:import_titre_etape',array('nom_base'=>'PhpBB'))." $step",$corps);

	exit;
} // action_spipbb_fromphpbb

// ------------------------------------------------------------------------------
// [fr] connecte a la base contenant les forums phpbb
// ------------------------------------------------------------------------------
function select_phpbb_db()
{
	global $spipbb_fromphpbb;
	mysql_select_db($spipbb_fromphpbb['phpdb'],$spipbb_fromphpbb['phpbb_connect'])
		or die(_T('spipbb:migre_erreur_db',array('nom_base'=>'PhpBB')));
} // select_phpbb_db

// ------------------------------------------------------------------------------
// [fr] connecte a la base contenant spip - pas encore compatible multi base
// ------------------------------------------------------------------------------
function select_spip_db() {
//	global $spipbb_fromphpbb;
//	global $connexions;
	$f = _FILE_CONNECT ;
	if ($f AND is_readable($f)) include($f);
	else die(_T('spipbb:migre_erreur_db_spip'));
	// mysql_select_db($connexions[0]['db'],$connexions[0]['link']) ;
} // select_spip_db

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function item_cond($deja,$item) {
	if ($deja != '') {
		$deja .= ",\n";
	}
	$deja .= $item;
	return $deja;
} // item_cond

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function item($item,$enveloppe) {
	$result = '';
	if ($item != '') {
		$result = sprintf($enveloppe,$item);
	}
	return $result;
} // item

// ------------------------------------------------------------------------------
// [fr] Converti les raccourcis bbcode au format SPIP
// ------------------------------------------------------------------------------
function bbcode_to_raccourcis_spip($texte) {
	$bbcode = array(
	"[list]", "[*]", "[/list]",
	"[img]", "[/img]",
	"[b]", "[/b]",
	"[u]", "[/u]",
	"[i]", "[/i]",
	'[/size]', '[/color]',
	"[code]", "[/code]",
	"[/quote]");

	$spipcode = array(
	"", "-", "",
	"<img>", "</img>",
	"{{", "}}",
	"{_", "_}",
	"{", "}",
	'', '',
	"<code>", "</code>",
	"</quote>");

	//return $texte;

	// on remplace les < et > dans un premier temps
	$result = str_replace('<','&lt;',$texte);
	$result = str_replace('>','&gt;',$result);

	// on continue en virant les numeros de serie dans le bbcode
	$result = preg_replace('/\[(\/?[^:]+):[a-f0-9]+/','[\\1',$result);
	// quote
	$result = preg_replace('/\[quote="?([^"\]]*)"?\]/','<quote>',$result);
	// color
	$result = preg_replace('/\[color="?([^"\]]*)"?\]/','',$result);
	// size
	$result = preg_replace('/\[size="?([^"\]]*)"?\]/','',$result);
	// mail
	$result = preg_replace('/\[mail="?([^"\]]*)"?\]([^\[]*)\[\/mail\]/','[\\2->\\1]',$result);
	// url
	$result = preg_replace('/\[url="?([^"\]]*)"?\]([^\[]*)\[\/url\]/','[\\2->\\1]',$result);
	$result = preg_replace('/\[url\]([^\[]*)\[\/url\]/','[->\\1]',$result);
	//$result = preg_replace('/\[size="([^"]*)"\]/','
	//    $result = $texte;
	// on applique les transformations simples
	$result = str_replace($bbcode, $spipcode, $result);
	//    $result = preg_replace('/\[quote(:)?[A-Fa-f0-9]*="([^"]*)"\]/','<div class="quote"><p>\\2 <i>a ecrit :</i></p><p>',$result);
	//    $result = preg_replace('/\[\/quote(:)?[A-Fa-f0-9]*\]/','</p></div>',$result);
	$result = nl2br($result);

	// les smileys
	//$result = preg_replace('/:([a-z]+):/','<img class="smile" src="smiles/icon_\\1.gif" width="15" height="15" />',$result);

	return $result;
} // bbcode_to_raccourcis_spip

// ------------------------------------------------------------------------------
// [fr] transcodages divers
// [en] Transcode conversions
// ------------------------------------------------------------------------------
function fromphpbb_convert($texte) {
	if (function_exists('mb_detect_encoding')) $encoding=mb_detect_encoding($texte);
	spipbb_log('convert:'.$encoding,3,__FILE__);
/*
	if (is_ascii($texte)) {
		$charset='ascii';
		return corriger_caracteres($texte);
	}
*/
	if (is_utf8($texte)) {
		$charset='utf-8';
	}
	else {
		$charset='iso-8859-1';
	}
	spipbb_log('charset:'.$charset,3,__FILE__);

	return corriger_caracteres(importer_charset($texte, $charset));
}

// ------------------------------------------------------------------------------
// [fr] Initialisation
// ------------------------------------------------------------------------------
function fromphpbb_init_metas($spiprubid=0)
{
	global $spipbb_fromphpbb; // stockage des informations et des etapes

	$spipbb_fromphpbb=array();
	$spipbb_fromphpbb['spiprubid'] = ($spiprubid==0) ? $GLOBALS['spipbb']['id_secteur'] : $spiprubid ;
	$spipbb_fromphpbb['spiprub_from_catid'] = array();
	$spipbb_fromphpbb['spip_art_from_forumid'] = array();
	$spipbb_fromphpbb['spip_auteur_from_user_id'] = array();
	$spipbb_fromphpbb['spip_post_from_post_id'] = array();
	$spipbb_fromphpbb['spip_lang'] = $GLOBALS['meta']['langue_site'];
	$spipbb_fromphpbb['connexion'] = $GLOBALS['connexions'][0];
	$spipbb_fromphpbb['prefixe'] = $GLOBALS['connexions'][0]['prefixe'];
	$spipbb_fromphpbb['link'] = $GLOBALS['connexions'][0]['link'];
	$spipbb_fromphpbb['db'] = $GLOBALS['connexions'][0]['db'];
	$spipbb_fromphpbb['statut_abonne'] = _SPIPBB_STATUT_ABONNE ;
	$spipbb_fromphpbb['mc_annonce_id'] = $GLOBALS['spipbb']['id_mot_annonce'];
	$spipbb_fromphpbb['mc_postit_id'] = $GLOBALS['spipbb']['id_mot_postit'];
	$spipbb_fromphpbb['mc_ferme_id'] = $GLOBALS['spipbb']['id_mot_ferme'];

	// [fr] recupere les parametres de connexion
	// [en] Grab the connection parameters

	$source_nr = intval(_request('fromphpbb_source'));
	$filename = _request('fromphpbb_filename_'.$source_nr);
	$tablename = _request('fromphpbb_table_'.$source_nr);
	if ($filename) {
		global $dbhost,$dbuser,$dbpasswd,$dbname,$table_prefix;
		require($filename);
		$spipbb_fromphpbb['phpbb_host'] = $dbhost;
		$spipbb_fromphpbb['phpbb_login'] = $dbuser;
		$spipbb_fromphpbb['phpbb_pass'] = $dbpasswd;
		$spipbb_fromphpbb['phpdb'] = $dbname;
		$spipbb_fromphpbb['phpbbroot'] = dirname($filename); // remove config.php
		$spipbb_fromphpbb['PR'] = $table_prefix;
	} else if ($tablename) {
		// Meme base que spip
		$f = _FILE_CONNECT ;
		$handle = fopen ($f, "r");
		$contents = fread ($handle, filesize ($f));
		fclose ($handle);
		$r=preg_match("#spip_connect_db\('([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)#",$contents,$params);

		$spipbb_fromphpbb['phpbb_host'] = $params[1];
		$spipbb_fromphpbb['phpbb_login'] = $params[3];
		$spipbb_fromphpbb['phpbb_pass'] = $params[4];
		$spipbb_fromphpbb['phpdb'] = $params[5];
		$spipbb_fromphpbb['phpbbroot'] = _request('fromphpbb_table_path_'.$source_nr);
		$spipbb_fromphpbb['PR'] = substr($tablename,0,-6); // enlever config du nom
	} else {
		$spipbb_fromphpbb['phpbb_host'] = _request('phpbb_host');
		$spipbb_fromphpbb['phpbb_login'] = _request('phpbb_login');
		$spipbb_fromphpbb['phpbb_pass'] = _request('phpbb_pass');
		$spipbb_fromphpbb['phpdb'] = _request('phpbb_base');
		$spipbb_fromphpbb['phpbbroot'] = _request('phpbb_root');
		$spipbb_fromphpbb['PR'] = _request('phpbb_prefix');
	}
	if (empty($spipbb_fromphpbb['phpbb_host'])) $spipbb_fromphpbb['phpbb_host']="localhost";
	$spipbb_fromphpbb['spiproot'] = _DIR_RACINE;
	$spipbb_fromphpbb['phpbb_connect'] =
		@mysql_connect($spipbb_fromphpbb['phpbb_host'],$spipbb_fromphpbb['phpbb_login'],$spipbb_fromphpbb['phpbb_pass']) or
			die(_T('spipbb:migre_erreur_db_phpbb',array('nom_base'=>'PhpBB')));
	select_phpbb_db();
	$result = @mysql_query("SELECT config_value FROM ".$spipbb_fromphpbb['PR'].
			"config WHERE config_name='default_lang'") or
			die(_T('spipbb:fromphpbb_erreur_db_phpbb_config'));
	$row = @mysql_fetch_assoc($result);
	$spipbb_fromphpbb['phpbb_lang'] = substr($row['config_value'],0,2);
	$spipbb_fromphpbb['phpbb_lang'] = $spipbb_fromphpbb['phpbb_lang'] ? $spipbb_fromphpbb['phpbb_lang'] :
						$spipbb_fromphpbb['spip_lang'];

	// recuperation du secteur ou seront implantes les forums
	select_spip_db();
	$result = sql_select('id_secteur','spip_rubriques', "id_rubrique='".$spipbb_fromphpbb['spiprubid']."'");
	$row = sql_fetch($result) or die(_T('spipbb:migre_erreur_db_spip'));
	$spipbb_fromphpbb['spip_id_secteur'] = $row['id_secteur'];

	$spipbb_fromphpbb['go'] = ( _request('phpbb_test') != 'oui' );
	$spipbb_fromphpbb['etape'] = 1;
}

// ------------------------------------------------------------------------------
// [fr] Chargement a partir des meta
// ------------------------------------------------------------------------------
function fromppbb_load_metas($spiprubid)
{
	global $spipbb_fromphpbb;
	include_spip('inc/meta');
	lire_metas();
	if (isset($GLOBALS['meta']['spipbb_fromphpbb']))
	{
		$spipbb_fromphpbb = unserialize($GLOBALS['meta']['spipbb_fromphpbb']);
		// rappel de connexion
		$spipbb_fromphpbb['phpbb_connect'] =
		@mysql_connect($spipbb_fromphpbb['phpbb_host'],$spipbb_fromphpbb['phpbb_login'],$spipbb_fromphpbb['phpbb_pass']) or
			die(_T('spipbb:migre_erreur_db',array('nom_base'=>'PhpBB')));
	}
	else
	{
		fromphpbb_init_metas($spiprubid);
	}
} // fromppbb_load_metas

// ------------------------------------------------------------------------------
// [fr] Sauvergade dans les meta
// ------------------------------------------------------------------------------
function fromphpbb_save_metas()
{
	global $spipbb_fromphpbb;
	if (is_array($spipbb_fromphpbb))
	{
		include_spip('inc/meta');
		ecrire_meta('spipbb_fromphpbb', serialize($spipbb_fromphpbb));
		ecrire_metas();
		spipbb_log('OK',3,"A_f_save_metas");
	}
} // fromphpbb_save_metas

// ------------------------------------------------------------------------------
// [fr] Suppression dans les meta
// ------------------------------------------------------------------------------
function fromphpbb_delete_metas()
{
	global $spipbb_fromphpbb;

	if (isset($GLOBALS['meta']['spipbb_fromphpbb']) or isset($GLOBALS['spipbb_fromphpbb'])) {
		include_spip('inc/meta');
		effacer_meta('spipbb_fromphpbb');
		ecrire_metas();
		unset($GLOBALS['spipbb_fromphpbb']);
		unset($GLOBALS['meta']['spipbb_fromphpbb']);
		unset($spipbb_fromphpbb);
		spipbb_log('OK',3,"A_f_delete_metas");
	}
} // fromphpbb_delete_metas

// ------------------------------------------------------------------------------
// [fr] migration des categories et des forums - etape 1
// ------------------------------------------------------------------------------
function migre_categories_forums() {
	global $spipbb_fromphpbb;

	$res = "<h1>"._T('spipbb:fromphpbb_migre_categories')."</h1>";

	$res .= "<p>"._T('spipbb:fromphpbb_migre_categories_dans_rub_dpt') .$spipbb_fromphpbb['spiprubid']."</p>\n";
	$res .= "<p>"._T('spipbb:fromphpbb_migre_categories_kw_ann_dpt') .$spipbb_fromphpbb['mc_annonce_id']."</p>\n";
	$res .= "<p>"._T('spipbb:fromphpbb_migre_categories_kw_postit_dpt') .$spipbb_fromphpbb['mc_postit_id']."</p>\n";
	$res .= "<p>"._T('spipbb:fromphpbb_migre_categories_kw_ferme_dpt').$spipbb_fromphpbb['mc_ferme_id']."</p>\n";
	$res .= "<hr />";
	//die ("arret pour debug");

	select_phpbb_db();

	// transfert des categories
	// 1 categorie = 1 sous rubrique de la rubrique affectee aux forums
	//
	select_phpbb_db();

	$result = mysql_query("SELECT * FROM ".$spipbb_fromphpbb['PR']."categories",$spipbb_fromphpbb['phpbb_connect']) or
		die(_T('spipbb:fromphpbb_migre_categories_impossible'));
	// cat_id   	  cat_title   	  cat_order
	$spiprub = 0;
	select_spip_db();

	while ($row = mysql_fetch_assoc($result)) {
		$rub_name = $row['cat_order'] . ". " . fromphpbb_convert($row['cat_title']);
		// [fr] Verifier si une sous-rubrique de ce nom n'existe pas deja dans cette rubrique
		$verif = sql_getfetsel("id_rubrique","spip_rubriques",
				"titre='$rub_name' AND id_parent=".$spipbb_fromphpbb['spiprubid']);
		if (empty($verif)) {
			if ($spipbb_fromphpbb['go']) {
				// malheureusement les groupes phpbb n'ont pas de langue associee
				$spip_id = sql_insertq('spip_rubriques', array(
							'id_parent'=>$spipbb_fromphpbb['spiprubid'],
							'titre'=>$rub_name,
							'id_secteur'=>$spipbb_fromphpbb['spip_id_secteur'],
							'statut'=>'publie',
							'lang'=>$spipbb_fromphpbb['phpbb_lang'],
							'statut_tmp'=>'publie')
							);
				$res .= "<p>"._T('spipbb:fromphpbb_migre_categories_groupe')." $rub_name [ $spip_id ]</p>";
				// memorise la relation entre les id de categories et les rubriques
				$spipbb_fromphpbb['spiprub_from_catid'][$row['cat_id']] = $spip_id;
			}
			else {
				$res .= "<p>$rub_name</p>";
				$query = "INSERT INTO spip_rubriques (id_parent,titre,id_secteur,statut,lang,statut_tmp) VALUES (".
						"'".$spipbb_fromphpbb['spiprubid']."','$rub_name','".
						$spipbb_fromphpbb['spip_id_secteur']."','publie','".
						$spipbb_fromphpbb['phpbb_lang']."','publie')";
				$res .= "<p>$query</p>";
				$spipbb_fromphpbb['spiprub_from_catid'][$row['cat_id']] = $spiprub++;
			} // (go)
		}
		else {
			$res .= "<p>$rub_name "._T('spipbb:fromphpbb_migre_existe_dpt')." $verif</p>";
			$spipbb_fromphpbb['spiprub_from_catid'][$row['cat_id']] = $verif;
		} // empty(verif)
	}

	//
	// transfert des forums
	// 1 forum = 1 article dans la rubrique categorie
	//
	select_phpbb_db();
	$result = mysql_query("SELECT * FROM ".$spipbb_fromphpbb['PR']."forums",$spipbb_fromphpbb['phpbb_connect']) or
		die(_T('spipbb:import_erreur_forums'));

	$spipart=0;
	select_spip_db();

	while ($row = mysql_fetch_assoc($result)) {
		$rub = $spipbb_fromphpbb['spiprub_from_catid'][$row['cat_id']];
		$titre = $row['forum_order'] . ". " . fromphpbb_convert($row['forum_name']);
		$descriptif = fromphpbb_convert($row['forum_desc']);
		$date = date("Y-m-d H:i:s");
		// verifier que ce forum n existe pas deja
		$verif = sql_getfetsel("id_article","spip_articles","titre='$titre' AND id_rubrique=$rub");
		if (empty($verif)) {
			if ($spipbb_fromphpbb['go']) {
				$spip_id = sql_insertq('spip_articles', array(
								'id_rubrique'=>$rub,
								'titre'=>$titre,
								'descriptif'=>$descriptif,
								'statut'=>'publie',
								'id_secteur'=>$spipbb_fromphpbb['spip_id_secteur'],
								'date'=>$date,
								'lang'=>$spipbb_fromphpbb['phpbb_lang'] )
							);
				$spipbb_fromphpbb['spip_art_from_forumid'][$row['forum_id']] = $spip_id;
				$res .= "<p>"._T('spipbb:fromphpbb_migre_categories_forum')." $titre [ $spip_id ]</p>";
			}
			else {
				$res .= "<p>$rub, $titre, $descriptif</p>";
				$query = "INSERT INTO spip_articles (id_rubrique,titre,descriptif,statut,id_secteur,date,lang) VALUES (".
						"'$rub','$titre','$descriptif','publie', '".
						$spipbb_fromphpbb['spip_id_secteur']."', '$date', '".
						$spipbb_fromphpbb['phpbb_lang']."' )";
				$res .= "<p>$query</p>";
				$spipbb_fromphpbb['spip_art_from_forumid'][$row['forum_id']] = $spipart++;
			} // (go)
		}
		else {
			$res .= "<p><strong>$rub, $titre, $descriptif"._T('spipbb:fromphpbb_migre_existe_dpt')." $verif</strong></p>";
			$spipbb_fromphpbb['spip_art_from_forumid'][$row['forum_id']] = $verif;
		} // empty(verif)
	}

	return $res;
} // migre_categories_forums

// ------------------------------------------------------------------------------
// [fr] migration des utilisateurs
// ------------------------------------------------------------------------------
function migre_utilisateurs() {
	global $spipbb_fromphpbb;

	$res = "<h1>"._T('spipbb:fromphpbb_migre_utilisateurs')."</h1>";

	$res .= "<hr />";
	//die ("arret pour debug");

	//
	// transfert des utilisateurs
	//
	select_phpbb_db();
	$result = mysql_query("select * FROM ".$spipbb_fromphpbb['PR']."users",$spipbb_fromphpbb['phpbb_connect']) or
		die(_T('spipbb:fromphpbb_migre_utilisateurs_impossible'));

	$compte_user = 0;
	$spipaut = 0;

	select_spip_db();
	while ($row = mysql_fetch_assoc($result))
	{
		// on commence par la date de la derniere visite et
		// par la date d'inscription pour eliminer les gus
		// qui sont inscrit depuis longtemps mais qui ne
		// sont jamais venus sur les forums
		$date_inscription = $row['user_regdate'];
		$aujourdhui = time();
		$phpbbdate = $row['user_lastvisit'];

		if (! (($aujourdhui - $date_inscription) > (6 * 30 * 24 * 3600) && $phpbbdate == 0))
		{	// si inscription > 6 mois et 0 jamais revenu on jette, sinon on inscrit dans spip :
			// phpbb stocke un timestamp Unix, SPIP, un timestamp iso
			$enligne = date("Y-m-d H:i:s",$phpbbdate);
			if ($phpbbdate == 0) {
				$statut = 'nouveau';
			}
			else {
				if ($row['user_level'] == 0) {
					$statut = $spipbb_fromphpbb['statut_abonne'];
				}
				else {
					$statut = '0minirezo';
				}
			}

			// transcoder_page
			// corriger_caracteres

			$nom = addslashes($row['username']);
			$email = $row['user_email'];
			$pass = $row['user_password'];
			$site = $row['user_website'];
			if ( (!preg_match(',^https?://[^.]+\.[^.]+.*/.*$,', $site)) ) { $site = ""; } // on vire les url non conformes
			$user_lang = $row['user_lang'];
			$user_lang = $user_lang ? $user_lang : $spipbb_fromphpbb['spip_lang'] ;
			$voir_en_ligne = ($row['user_allow_viewonline']==1) ? 'oui' : 'non' ;
			$auteur_extra=array();

			$bio = '';

			// utilisation du champ extra si existe
			if (is_array($GLOBALS['champs_extra']) AND is_array($GLOBALS['champs_extra']['auteurs']))
			{
				$auteur_extra['Localisation'] = fromphpbb_convert($row['user_from']) ;
				$auteur_extra['Emploi'] = fromphpbb_convert($row['user_occ']) ;
				$auteur_extra['Loisirs'] = fromphpbb_convert($row['user_interests']) ;
				$auteur_extra['Numero_ICQ'] = $row['user_icq'] ;
				$auteur_extra['Nom_AIM'] = $row['user_aim'] ;
				$auteur_extra['Nom_Yahoo'] = $row['user_yim'] ;
				$auteur_extra['Nom_MSNM'] = $row['user_msnm'] ;
				$auteur_extra['avatar'] = $row['user_avatar'] ;
				$auteur_extra['signature'] = $row['user_sig'] ;
			}
			else
			{ // sinon on stocke dans la bio
				$bio =  item_cond($bio,trim($row['user_occ']));
				$bio = item_cond($bio,trim($row['user_from']));
				$bio = item_cond($bio,trim($row['user_interests']));
				$bio .= item(trim($row['user_icq']),"-ICQ: %s\n");
				$bio .= item(trim($row['user_aim']),"-AIM: %s\n");
				$bio .= item(trim($row['user_yim']),"-YIM: %s\n");
				$bio .= item(trim($row['user_msnm']),"-MSN: %s\n");
			}
			$auteur_extra = serialize($auteur_extra);

			if ($bio != '') { $bio .= "\n"; }

			// converti le bbcode en code SPIP
			$bio = bbcode_to_raccourcis_spip($bio);
			$bio = fromphpbb_convert($bio);

			// verifier que cet auteur n existe pas deja
			$spip_id = sql_getfetsel("id_auteur","spip_auteurs","nom='$nom' AND login='$nom'");
			if (empty($spip_id)) {
				// ecrit dans la base SPIP
				$auteur_spip = false;
				if ($spipbb_fromphpbb['go']) {
					$spip_id = sql_insertq('spip_auteurs', array(
									'nom'=>$nom,
									'bio'=>$bio,
									'email'=>$email,
									'nom_site'=>$site,
									'url_site'=>$site,
									'login'=>$nom,
									'pass'=>$pass,
									'statut'=>$statut,
									'en_ligne'=>$enligne,
									'imessage'=>$voir_en_ligne,
									'lang'=>$user_lang,
									'extra'=>$auteur_extra )
								);
					$spipbb_fromphpbb['spip_auteur_from_user_id'][$row['user_id']] = $spip_id;
				}
				else
				{
					$res .= "<p>$nom $enligne</p>";
					$query = "INSERT INTO spip_auteurs ".
						"(nom,bio,email,nom_site,url_site,login,pass,statut,en_ligne,imessage,lang,extra)".
						" VALUES ('".$nom."','".$bio."','".$email."','".
						$site."','".$site."','".$nom."','$pass','$statut',".
						"'$enligne','$voir_en_ligne','$lang','$auteur_extra')";
					$spipbb_fromphpbb['spip_auteur_from_user_id'][$row['user_id']] = $spipaut++;
					$spip_id = $spipbb_fromphpbb['spip_auteur_from_user_id'][$row['user_id']];
				} // ($spipbb_fromphpbb['go'])
			}
			else {
				$res .= "<p>$nom "._T('spipbb:fromphpbb_migre_existe_dpt')." $verif</p>";
				$spipbb_fromphpbb['spip_auteur_from_user_id'][$row['user_id']] = $spip_id;
				$auteur_spip = true;
			} // emtpy(spip_id)

			$logofile = $row['user_avatar'];
			if ( $spipbb_fromphpbb['go'] AND ($logofile) )
			{
				// change from gif to jpg
				//$logofile = str_replace('.gif','.jpg',$logofile);
				$logo_url = $spipbb_fromphpbb['phpbbroot'] . '/images/avatars/' . $logofile;
				$logo_ext = substr($logofile,-3,3);
				if (file_exists($logo_url))
				{
					// copie l'avatar dans IMG sous le nom auton#ID_AUTEUR.ext
					$dest_url = _DIR_RACINE . "/IMG/auton$spip_id.".$logo_ext;
					@copy($logo_url,$dest_url);
				}
				//$logodata = file_get_contents($logo_url);
			}

			// si c'est un nouvel admin, on le restreint a la rubrique du forum
			// verifier que cette restriction n existe pas deja
			$verif = sql_getfetsel("id_auteur","spip_auteurs_rubriques",
					"id_auteur=$spip_id AND id_rubrique=".$spipbb_fromphpbb['spiprubid']);
			if (empty($verif)) {
				if ( ($statut == '0minirezo') AND (! $auteur_spip) ) {
					if ($spipbb_fromphpbb['go']) {
						$l_id = sql_insertq('spip_auteurs_rubriques', array(
									'id_auteur'=>$spip_id,
									'id_rubrique'=>$spipbb_fromphpbb['spiprubid'])
								);
					}
					else {
						$res .= "<p>"._T('spipbb:fromphpbb_migre_utilisateurs_admin_restreint_add'). "</p>";
						$query = "INSERT INTO spip_auteurs_rubriques (id_auteur,id_rubrique) ".
							"VALUES ('$spip_id','".$spipbb_fromphpbb['spiprubid']."')";
						$res .= "<p>$query</p>";
					}
				}
			}
			else {
				$res .= "<p>"._T('spipbb:fromphpbb_migre_utilisateurs_admin_restreint_already'). "</p>";
			}

			if (($compte_user % 20) == 0) { $res .= sprintf("<p>[%d]",$compte_user); }
			$res .= '.';
			$compte_user++;
		}
	}
	$res .= "\n<p>"._T('spipbb:fromphpbb_migre_utilisateurs_total_dpt'). " $compte_user</p>" ;

	return $res;
} // migre_utilisateurs

// ------------------------------------------------------------------------------
// [fr] importation des topics et posts
// ------------------------------------------------------------------------------
function migre_threads() {
	global $spipbb_fromphpbb;

	$res = "<h1>"._T('spipbb:fromphpbb_migre_thread')."</h1>";

	select_phpbb_db();
	$query = "SELECT * FROM ".$spipbb_fromphpbb['PR']."topics,".$spipbb_fromphpbb['PR'].
		"posts,".$spipbb_fromphpbb['PR']."posts_text WHERE (".$spipbb_fromphpbb['PR'].
		"topics.topic_id = ".$spipbb_fromphpbb['PR']."posts.topic_id AND ".$spipbb_fromphpbb['PR'].
		"posts.post_id = ".$spipbb_fromphpbb['PR']."posts_text.post_id) ORDER BY ".
		$spipbb_fromphpbb['PR']."posts.post_id";

	$result = mysql_query($query,$spipbb_fromphpbb['phpbb_connect']) or
		die(_T('spipbb:fromphpbb_migre_thread_impossible_dpt').mysql_error($spipbb_fromphpbb['phpbb_connect']));

	$compte_posts = 0;
	$topic_post = array();
	$poid = 0;
	select_spip_db();
	while ($row = mysql_fetch_assoc($result))
	{
		// le premier post rencontre pour un topic donne est
		// le post qui a lance le topic (normalement)
		$topic_id = $row['topic_id'];
		if (! isset($topic_post[$topic_id])) {
			$id_parent = 0;
		}
		else {
			$id_parent = $topic_post[$topic_id];
		}

		// traite le cas des posts qui appartiennent a un forum qui n'existe plus
		if (! isset($spipbb_fromphpbb['spip_art_from_forumid'][$row['forum_id']])) { continue; }

		$id_article = $spipbb_fromphpbb['spip_art_from_forumid'][$row['forum_id']];
		$date_heure = date("Y-m-d H:i:s",$row['post_time']);

		if ($id_parent == 0) {
			$titre = fromphpbb_convert($row['topic_title']);
		}
		else {
			if ($row['post_subject'] == '') {
			$titre = fromphpbb_convert($row['topic_title']);
			}
			else {
			$titre = fromphpbb_convert($row['post_subject']);
			}
		}

		$texte = fromphpbb_convert($row['post_text']);

		$poster_id = $row['poster_id'];
		if (! isset($spipbb_fromphpbb['spip_auteur_from_user_id'][$poster_id])) {
			$id_auteur = 0;
		}
		else {
			$id_auteur = $spipbb_fromphpbb['spip_auteur_from_user_id'][$poster_id];
		}

		$username = fromphpbb_convert($row['post_username']);
		if ($username != '') {
			$auteur = $username;
		}
		else {
			$query = "SELECT nom FROM spip_auteurs WHERE id_auteur=$id_auteur";
			$r_auteur = sql_query($query) or die("Erreur SQL($query)");
			if ($auteur_info = sql_fetch($r_auteur)) {
				$auteur = $auteur_info['nom'];
			}
			else {
				$auteur = 'anonyme';
			}
		}

		// recupere l'email
		$query = "SELECT email FROM spip_auteurs WHERE id_auteur=$id_auteur";
		$r_email = sql_query($query) or die("Erreur SQL($query)");
		if ($row_email = sql_fetch($r_email)) {
			$email_auteur = $row_email['email'];
		}
		else {
			$email_auteur = 'nobody@nowhere.nodomain';
		}


		// converti le bbcode en raccourcis SPIP
		$titre = bbcode_to_raccourcis_spip($titre);
		$texte = bbcode_to_raccourcis_spip($texte);
		$ip_post = decode_ip($row['poster_ip']);

		if (empty($id_parent)) {
			$id_thread=$id_forum ;

			// verifier que ce topic n existe pas deja
			// tres couteux : $verif = sql_getfetsel("id_forum","spip_forum","id_parent=$id_parent
			// AND id_article=$id_article AND texte='".$texte."'");
			$verif=0;

			if (empty($verif)) {
				if ($spipbb_fromphpbb['go']) {
					$insert_id = sql_insertq('spip_forum', array(
							'id_parent'=>$id_parent,
							'id_article'=>$id_article,
							'email_auteur'=>$email_auteur,
							'date_heure'=>$date_heure,
							'titre'=>$titre,
							'texte'=>$texte,
							'auteur'=>$auteur,
							'statut'=>$statut,
							'ip'=>$ip_post,
							'id_auteur'=>$id_auteur,
							'date_thread'=>$date_heure)
							);
				}
				else {
					$res .= "<p>"._T('spipbb:fromphpbb_migre_thread_ajout')."</p>";
					$query = "INSERT INTO spip_forum ".
						"(id_parent,id_article,email_auteur,date_heure,titre,texte,auteur,".
						"statut,ip,id_auteur,date_thread) VALUES ".
						"('$id_parent','$id_article','".$email_auteur."','$date_heure',".
						"'".$titre."','".$texte."','".$auteur.
						"','$statut','$ip_post','$id_auteur','$date_heure')";
					$res .= "<p>$query</p>";
					$insert_id=$poid++;
				}
			}
			else {
				$res .= "<p>"._T('spipbb:fromphpbb_migre_thread_existe_dpt')."$verif</p>";
				$insert_id = $verif;
			}
		}

		if (empty($id_parent)) {$id_thread=$insert_id;} else {$id_thread=$id_parent;}

		if ($spipbb_fromphpbb['go']) {
			@sql_updateq('spip_forum', array(
					'id_thread'=>$id_thread
					),
					"id_forum='$insert_id'");
		}
		else {
			$query = "UPDATE spip_forum SET (id_thread='$id_thread') WHERE id_forum='$insert_id'" ;
			$res .= "<p>$query</p>";
		}

		if (! isset($topic_post[$topic_id])) {
			$topic_post[$topic_id] = $insert_id;
		}

		// Ajout d'un mot-cle adequat pour etiqueter les topics qui sont des
		// post-it (la colonne concernee est topic_type)
		// 0 = topic normal
		// 1 = post-it (un post-it apparait en tete des sujets sur la premiere page)
		// 2 = annonce (une annonce apparait en tete des sujets sur toutes les pages
		// des topics)

		if ($row['topic_status']==1) {
			// Traitement d'un message ferme
			if ($spipbb_fromphpbb['mc_ferme_id'] != 0) {
				if ($spipbb_fromphpbb['go']) {
					$l_id = sql_insertq('spip_mots_forum', array(
								'id_mot'=>$spipbb_fromphpbb['mc_ferme_id'],
								'id_forum'=>$insert_id)
							);
				}
				else {
					$res .= "<p>"._T('spipbb:fromphpbb_migre_thread_ferme')."</p>";
					$query = "INSERT INTO spip_mots_forum (id_mot,id_forum) VALUES (".
							$spipbb_fromphpbb['mc_ferme_id'].",$insert_id)";
					$res .= "<p>$query</p>";
				}
				$res .= "<p>"._T('spipbb:fromphpbb_migre_thread_ferme')." - id_php_bb = $topic_id - spip_id = $insert_id</p>";
			}
		}

		if ($id_parent == 0 && $row['topic_type'] != 0)
		{
			// Traitement des post-its
			if ($spipbb_fromphpbb['mc_postit_id'] != 0 && $row['topic_type'] == 1) {
				if ($spipbb_fromphpbb['go']) {
					$l_id = sql_insertq('spip_mots_forum', array(
								'id_mot'=>$spipbb_fromphpbb['mc_postit_id'],
								'id_forum'=>$insert_id)
							);
				}
				else {
					$res .= "<p>"._T('spipbb:fromphpbb_migre_thread_postit')."</p>";
					$query = "INSERT INTO spip_mots_forum (id_mot,id_forum) VALUES (".
							$spipbb_fromphpbb['mc_postit_id'].",$insert_id)";
					$res .= "<p>$query</p>";
				}
				$res .= "<p>"._T('spipbb:fromphpbb_migre_thread_postit')." - id_php_bb = $topic_id - spip_id = $insert_id</p>";
			}
			// Traitement des annonces
			if ($spipbb_fromphpbb['mc_annonce_id'] != 0 && ( ($row['topic_type'] == 2) OR ($row['topic_type'] == 3))) {
				if ($spipbb_fromphpbb['go']) {
					$l_id = sql_insertq('spip_mots_forum', array(
								'id_mot'=>$spipbb_fromphpbb['mc_annonce_id'],
								'id_forum'=>$insert_id)
							);
				}
				else {
					$res .= "<p>"._T('spipbb:fromphpbb_migre_thread_annonce')."</p>";
					$query = "INSERT INTO spip_mots_forum (id_mot,id_forum) VALUES (".
							$spipbb_fromphpbb['mc_annonce_id'].",$insert_id)";
					$res .= "<p>$query</p>";
				}
				$res .= "<p>"._T('spipbb:fromphpbb_migre_thread_annonce')." - id_php_bb = $topic_id - spip_id = $insert_id</p>";
			}
			// Traitement des messages ferme ???

		} // traitement des messages speciaux

		if (($compte_posts % 100) == 0) { $res .= sprintf("<p>[%d]",$compte_posts); }
		$res .= '.';
		$compte_posts ++;
	}
	$res .= "\n<p>"._T('spipbb:fromphpbb_migre_thread_total_dpt'). " $compte_posts</p>" ;

	return $res;
} // migre_threads

// Import de phpBB includes/functions
function decode_ip($int_ip)
{
	$hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
	return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
} // decode_ip

?>
