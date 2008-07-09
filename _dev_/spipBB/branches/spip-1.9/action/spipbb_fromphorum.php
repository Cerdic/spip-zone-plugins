<?php
#-----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                         #
#  File    : action/spipbb_fromphorum- import de phorum     #
#  Authors :  Chryjs, 2008                                  #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs  #
#  Contact : chryjs!@!free!.!fr                             #
#-----------------------------------------------------------#

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

global $spipbb_fromphorum; // stockage des informations et des etapes

include_spip('inc/minipres');
include_spip('inc/spipbb_init');
include_spip('inc/presentation');

ini_set('max_execution_time',600); // pas toujours possible mais requis pour etape 2 et surtout 3!

// ------------------------------------------------------------------------------
// [fr] Verification et declenchement de l'operation
// ------------------------------------------------------------------------------
function action_spipbb_fromphorum()
{
	global $spip_lang_left, $spipbb_fromphorum, $dir_lang, $time_start;
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_rubrique = intval($arg);
	if (empty($id_rubrique))
	{
		minipres( _T('spipbb:admin_titre_page_spipbb_admin_migre',array('nom_base'=>'Phorum')), "<strong>"._T('avis_non_acces_page')."</strong>" );
		exit;
	}

	fromphorum_load_metas($id_rubrique);
	$step = $spipbb_fromphorum['etape'];

	$go_back = generer_url_ecrire("naviguer","id_rubrique=$id_rubrique");
	$link_back  = icone(_T('icone_retour'), $go_back, "rubrique-12.gif", "rien.gif", ' ',false);
	$corps = $link_back;

//$corps  .= "\n<div $dir_lang style='width:98%;height: 98%;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>" ;

	switch ($step) {
	case 1 :
		// migration des utilisateurs(auteurs)
		$corps .= migre_utilisateurs();
		$form   = "<input type='hidden' name='etape' id='etape' value='3'>";
		$form  .= "<div align='right'><input class='fondo' type='submit' value='"._T('bouton_suivant')."' /></div>" ;
		$corps .= generer_action_auteur("spipbb_fromphorum",$id_rubrique, $retour,$form," method='post' name='formulaire'");
		$corps .= $link_back;
		$spipbb_fromphorum['etape']++;
		fromphorum_save_metas();
		break;
	case 2 :
		// migration des categories(rubriques) et des titres de forums(articles)
		$corps .= migre_categories_forums();
		$form   = "<input type='hidden' name='etape' id='etape' value='2'>";
		$form  .= "<div align='right'><input class='fondo' type='submit' value='"._T('bouton_suivant')."' /></div>" ;
		$corps .= generer_action_auteur("spipbb_fromphorum",$id_rubrique, $retour, $form," method='post' name='formulaire'");
		$corps .= $link_back;
		$spipbb_fromphorum['etape']++;
		fromphorum_save_metas();
		break;
	case 3 :
		// migration des threads(forums)
		$corps .= migre_threads();
		$corps .= $link_back;
		fromphorum_delete_metas();
		break;
	default : // manque mp
		$corps .= "<strong>"._T('avis_non_acces_page')."</strong>";
	}
	$time = array_sum(explode(' ', microtime())) - $time_start;
	$corps .= "\n<!-- Elapsed: $time secondes -->";

	echo minipres(_T('spipbb:import_titre_etape',array('nom_base'=>'Phorum'))." $step",$corps);

	exit;
} // action_spipbb_fromphorum

// ------------------------------------------------------------------------------
// [fr] connecte a la base contenant les forums phorum
// ------------------------------------------------------------------------------
function select_phorum_db()
{
	global $spipbb_fromphorum;
	mysql_select_db($spipbb_fromphorum['phorumdb'],$spipbb_fromphorum['phorum_connect'])
		or die(_T('spipbb:migre_erreur_db',array('nom_base'=>'Phorum')).":select_phorum_db");
} // select_phorum_db

// ------------------------------------------------------------------------------
// [fr] connecte a la base contenant spip - pas encore compatible multi base
// ------------------------------------------------------------------------------
function select_spip_db() {
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
	//    $result = preg_replace('/\[quote(:)?[A-Fa-f0-9]*="([^"]*)"\]/','<div class="quote"><p>\\2 <i>a �crit :</i></p><p>',$result);
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
function fromphorum_convert($texte) {
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
function fromphorum_init_metas($spiprubid)
{
	global $spipbb_fromphorum; // stockage des informations et des etapes

	$spipbb_fromphorum=array();
	$spipbb_fromphorum['spiprubid'] = ($spiprubid==0) ? $GLOBALS['spipbb']['id_secteur'] : $spiprubid ;
	$spipbb_fromphorum['spiprub_from_catid'] = array();
	$spipbb_fromphorum['spip_art_from_forumid'] = array();
	$spipbb_fromphorum['spip_auteur_from_user_id'] = array();
	$spipbb_fromphorum['spip_post_from_post_id'] = array();
	$spipbb_fromphorum['spip_lang'] = $GLOBALS['meta']['langue_site'];
	$spipbb_fromphorum['connexion'] = $GLOBALS['connexions'][0];
	$spipbb_fromphorum['prefixe'] = $GLOBALS['connexions'][0]['prefixe'];
	$spipbb_fromphorum['link'] = $GLOBALS['connexions'][0]['link'];
	$spipbb_fromphorum['db'] = $GLOBALS['connexions'][0]['db'];
	$spipbb_fromphorum['statut_abonne'] = _SPIPBB_STATUT_ABONNE;
	$spipbb_fromphorum['mc_annonce_id'] = $GLOBALS['spipbb']['id_mot_annonce'];
	$spipbb_fromphorum['mc_postit_id'] = $GLOBALS['spipbb']['id_mot_postit'];
	$spipbb_fromphorum['mc_ferme_id'] = $GLOBALS['spipbb']['id_mot_ferme'];

	// [fr] recupere les parametres de connexion
	// [en] Grab the connection parameters

	$source_nr = intval(_request('fromphorum_source'));
	$filename = _request('fromphorum_filename_'.$source_nr);
	$tablename = _request('fromphorum_table_'.$source_nr);
	if ($filename) {
		global $dbhost,$dbuser,$dbpasswd,$dbname,$table_prefix;
		define('PHORUM',true); // allow full inclusion
		require($filename);
		$spipbb_fromphorum['phorum_host'] = $PHORUM['DBCONFIG']['server'];
		$spipbb_fromphorum['phorum_login'] = $PHORUM['DBCONFIG']['user'];;
		$spipbb_fromphorum['phorum_pass'] = $PHORUM['DBCONFIG']['password'];;
		$spipbb_fromphorum['phorumdb'] = $PHORUM['DBCONFIG']['name'];;
		$spipbb_fromphorum['phorumroot'] = dirname($filename); // remove config.php
		$spipbb_fromphorum['PR'] = $PHORUM['DBCONFIG']['table_prefix']."_";
	} else if ($tablename) {
		// Meme base que spip
		$f = _FILE_CONNECT ;
		$handle = fopen ($f, "r");
		$contents = fread ($handle, filesize ($f));
		fclose ($handle);
		$r=preg_match("#spip_connect_db\('([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)#",$contents,$params);

		$spipbb_fromphorum['phorum_host'] = $params[1];
		$spipbb_fromphorum['phorum_login'] = $params[3];
		$spipbb_fromphorum['phorum_pass'] = $params[4];
		$spipbb_fromphorum['phorumdb'] = $params[5];
		$spipbb_fromphorum['phorumroot'] = _request('fromphorum_table_path_'.$source_nr);
		$spipbb_fromphorum['PR'] = substr($tablename,0,-6); // enlever config du nom
	} else {
		$spipbb_fromphorum['phorum_host'] = _request('phorum_host');
		$spipbb_fromphorum['phorum_login'] = _request('phorum_login');
		$spipbb_fromphorum['phorum_pass'] = _request('phorum_pass');
		$spipbb_fromphorum['phorumdb'] = _request('phorum_base');
		$spipbb_fromphorum['phorumroot'] = _request('phorum_root');
		$spipbb_fromphorum['PR'] = _request('phorum_prefix');
	}
	if (empty($spipbb_fromphorum['phorum_host'])) $spipbb_fromphorum['phorum_host']="localhost";
	$spipbb_fromphorum['spiproot'] = _DIR_RACINE;
	$spipbb_fromphorum['phorum_connect'] = @mysql_connect($spipbb_fromphorum['phorum_host'],$spipbb_fromphorum['phorum_login'],$spipbb_fromphorum['phorum_pass']) or
			die(_T('spipbb:migre_erreur_db',array('nom_base'=>'Phorum')).":fromphorum_init_metas");

	select_phorum_db();
	$spipbb_fromphorum['phorum_lang'] = $spipbb_fromphorum['phorum_lang'] ? $spipbb_fromphorum['phorum_lang'] :
						$spipbb_fromphorum['spip_lang'];

	// recuperation du secteur ou seront implantes les forums
	select_spip_db();
	$result = sql_select('id_secteur','spip_rubriques', "id_rubrique='".$spipbb_fromphorum['spiprubid']."'");
	$row = sql_fetch($result) or die(_T('spipbb:migre_erreur_db_spip'));
	$spipbb_fromphorum['spip_id_secteur'] = $row['id_secteur'];

	$spipbb_fromphorum['go'] = ( _request('phorum_test') != 'oui' );
//die ("go : "._request('phorum_test')." : ".$spipbb_fromphorum['go']);
	$spipbb_fromphorum['etape'] = 1;
}

// ------------------------------------------------------------------------------
// [fr] Chargement a partir des meta
// ------------------------------------------------------------------------------
function fromphorum_load_metas($spiprubid)
{
	global $spipbb_fromphorum;
	include_spip('inc/meta');
	lire_metas();
	if (isset($GLOBALS['meta']['spipbb_fromphorum']))
	{
		$spipbb_fromphorum = unserialize($GLOBALS['meta']['spipbb_fromphorum']);
		// rappel de connexion
		$spipbb_fromphorum['phorum_connect'] =
		@mysql_connect($spipbb_fromphorum['phorum_host'],$spipbb_fromphorum['phorum_login'],$spipbb_fromphorum['phorum_pass']) or
			die(_T('spipbb:migre_erreur_db',array('nom_base'=>'Phorum')).":fromphorum_load_metas");
	}
	else
	{
		fromphorum_init_metas($spiprubid);
	}
} // fromppbb_load_metas

// ------------------------------------------------------------------------------
// [fr] Sauvergade dans les meta
// ------------------------------------------------------------------------------
function fromphorum_save_metas()
{
	global $spipbb_fromphorum;
	if (is_array($spipbb_fromphorum))
	{
		include_spip('inc/meta');
		ecrire_meta('spipbb_fromphorum', serialize($spipbb_fromphorum));
		ecrire_metas();
		spipbb_log('OK',3,"A_fp_save_metas");
	}
} // fromphorum_save_metas

// ------------------------------------------------------------------------------
// [fr] Suppression dans les meta
// ------------------------------------------------------------------------------
function fromphorum_delete_metas()
{
	global $spipbb_fromphorum;

	if (isset($GLOBALS['meta']['spipbb_fromphorum']) or isset($GLOBALS['spipbb_fromphorum'])) {
		include_spip('inc/meta');
		effacer_meta('spipbb_fromphorum');
		ecrire_metas();
		unset($GLOBALS['spipbb_fromphorum']);
		unset($GLOBALS['meta']['spipbb_fromphorum']);
		unset($spipbb_fromphorum);
		spipbb_log('OK',3,"A_fp_delete_metas");
	}
} // fromphorum_delete_metas

// ------------------------------------------------------------------------------
// [fr] migration des categories et des forums - etape 1
// ------------------------------------------------------------------------------
function migre_categories_forums() {
	global $spipbb_fromphorum;

	$res = "<p>Traduction en cours...</p>";

	$res .= "<p>Implantation des forums dans la rubrique ".$spipbb_fromphorum['spiprubid']."</p>\n";
	$res .= "<p>Les annonces recevront le mot clef ".$spipbb_fromphorum['mc_annonce_id']."</p>\n";
	$res .= "<p>Les post its recevront le mot clef ".$spipbb_fromphorum['mc_postit_id']."</p>\n";
	$res .= "<p>Les sujets clos recevront le mot clef ".$spipbb_fromphorum['mc_ferme_id']."</p>\n";
	$res .= "<hr />";
	//die ("arret pour debug");

	select_phorum_db();

	// transfert des categories
	// 1 categorie = 1 sous rubrique de la rubrique affectee aux forums
	//
	select_phorum_db();

	$result = mysql_query("SELECT * FROM ".$spipbb_fromphorum['PR']."forums WHERE folder_flag=1",$spipbb_fromphorum['phorum_connect']) or
		die("Impossible de recuperer les categories");
	// cat_id /forum_id   	  cat_title / name + description   	  cat_order /display_order
	$spiprub = 0;
	select_spip_db();

	while ($row = mysql_fetch_assoc($result)) {
		$rub_name = $row['display_order'] . ". " . fromphorum_convert($row['name']);
		$rub_texte = fromphorum_convert($row['description']);
		// [fr] Verifier si une sous-rubrique de ce nom n'existe pas deja dans cette rubrique
		$verif = sql_getfetsel("id_rubrique","spip_rubriques",
				"titre='$rub_name' AND id_parent=".$spipbb_fromphorum['spiprubid']);
		if (empty($verif)) {
			if ($spipbb_fromphorum['go']) {
				// malheureusement les groupes phorum n'ont pas de langue associee
				$spip_id = sql_insertq('spip_rubriques', array(
							'id_parent'=>$spipbb_fromphorum['spiprubid'],
							'titre'=>$rub_name,
							'id_secteur'=>$spipbb_fromphorum['spip_id_secteur'],
							'statut'=>'publie',
							'texte'=> $rub_texte,
							'lang'=>substr($row['language'],0,2),
							'statut_tmp'=>'publie')
							);
				$res .= "<p>Groupe $rub_name [ $spip_id ] : $rub_texte</p>";
				// memorise la relation entre les id de categories et les rubriques
				$spipbb_fromphorum['spiprub_from_catid'][$row['cat_id']] = $spip_id;
			}
			else {
				$res .= "<p>$rub_name</p>";
				$query = "INSERT INTO spip_rubriques (id_parent,titre,texte,id_secteur,statut,lang,statut_tmp) VALUES (".
						"'".$spipbb_fromphorum['spiprubid']."','$rub_name','$rub_texte_','".
						$spipbb_fromphorum['spip_id_secteur']."','publie','".
						substr($row['language'],0,2)."','publie')";
				$res .= "<p>$query</p>";
				$spipbb_fromphorum['spiprub_from_catid'][$row['cat_id']] = $spiprub++;
			} // (go)
		}
		else {
			$res .= "<p>$rub_name existe : $verif</p>";
			$spipbb_fromphorum['spiprub_from_catid'][$row['cat_id']] = $verif;
		} // empty(verif)
	}

	//
	// transfert des forums
	// 1 forum = 1 article dans la rubrique categorie
	//
	select_phorum_db();
	$result = mysql_query("SELECT * FROM ".$spipbb_fromphorum['PR']."forums WHERE folder_flag=0",$spipbb_fromphorum['phorum_connect']) or
		die(_T('spipbb:import_erreur_forums'));

	$spipart=0;
	select_spip_db();
	// cat_id /forum_id   	  cat_title / name + description   	  cat_order /display_order

	while ($row = mysql_fetch_assoc($result)) {
		$rub = $spipbb_fromphorum['spiprub_from_catid'][$row['cat_id']];
		$titre = $row['display_order'] . ". " . fromphorum_convert($row['name']);
		$descriptif = fromphorum_convert($row['description']);
		$date = date("Y-m-d H:i:s");
		// verifier que ce forum n existe pas deja
		// on peut avoir des forums dans la racine avec phorum --> palliatif (a ameliorer)
		if (empty($rub) or intval($rub)==0 ) $rub=$spipbb_fromphorum['spiprubid'];
		$verif = sql_getfetsel("id_article","spip_articles","titre='$titre' AND id_rubrique=$rub");
		if (empty($verif)) {
			if ($spipbb_fromphorum['go']) {
				$spip_id = sql_insertq('spip_articles', array(
								'id_rubrique'=>$rub,
								'titre'=>$titre,
								'descriptif'=>$descriptif,
								'statut'=>'publie',
								'id_secteur'=>$spipbb_fromphorum['spip_id_secteur'],
								'date'=>$date,
								'lang'=>substr($row['language'],0,2) )
							);
				$spipbb_fromphorum['spip_art_from_forumid'][$row['forum_id']] = $spip_id;
				$res .= "<p>Forum $titre [ $spip_id ]</p>";
			}
			else {
				$res .= "<p>$rub, $titre, $descriptif</p>";
				$query = "INSERT INTO spip_articles (id_rubrique,titre,descriptif,statut,id_secteur,date,lang) VALUES (".
						"'$rub','$titre','$descriptif','publie', '".
						$spipbb_fromphorum['spip_id_secteur']."', '$date', '".
						substr($row['language'],0,2)."' )";
				$res .= "<p>$query</p>";
				$spipbb_fromphorum['spip_art_from_forumid'][$row['forum_id']] = $spipart++;
			} // (go)
		}
		else {
			$res .= "<p>$rub, $titre, $descriptif existe : $verif</p>";
			$spipbb_fromphorum['spip_art_from_forumid'][$row['forum_id']] = $verif;
		} // empty(verif)
	}

	return $res;
} // migre_categories_forums

// ------------------------------------------------------------------------------
// [fr] migration des utilisateurs
// ------------------------------------------------------------------------------
function migre_utilisateurs() {
	global $spipbb_fromphorum;

	$res = "<p>Traduction en cours...</p>";

	$res .= "<hr />";
	//die ("arret pour debug");

	//
	// transfert des utilisateurs
	//
	$res .= "<p>Transfert des utilisateurs</p>";
	select_phorum_db();
	$result = mysql_query("select * FROM ".$spipbb_fromphorum['PR']."users",$spipbb_fromphorum['phorum_connect']) or
		die("Impossible de recuperer les utilisateurs");

	$compte_user = 0;
	$spipaut = 0;

	select_spip_db();
	while ($row = mysql_fetch_assoc($result))
	{
		// on commence par la date de la derniere visite et
		// par la date d'inscription pour eliminer les gus
		// qui sont inscrit depuis longtemps mais qui ne
		// sont jamais venus sur les forums
		$date_inscription = $row['date_added']; //UNIX_TIMESTAMP format time() cf include/api/user.php
		$aujourdhui = time();
		$phorumdate = $row['date_last_active']; //UNIX_TIMESTAMP format time() cf include/api/user.php
		$phorumactive = $row['active'];

		if (! (($aujourdhui - $date_inscription) > (6 * 30 * 24 * 3600) && $phorumactive < 1))
		{	// si inscription > 6 mois et 0 jamais revenu on jette, sinon on inscrit dans spip :
			// phorum stocke un timestamp Unix, SPIP, un timestamp iso
			$enligne = date("Y-m-d H:i:s",$phorumdate);
			if ($phorumdate == 0) {
				$statut = 'nouveau';
			}
			else {
				if ($row['admin'] == 0) {
					$statut = $spipbb_fromphorum['statut_abonne'];
				}
				else {
					$statut = '0minirezo';
				}
			}

			// transcoder_page
			// corriger_caracteres

			$nom = addslashes($row['username']);
			$email = $row['email'];
			$pass = $row['password'];
			$site = "";
			$user_lang = $row['user_language'];
			$user_lang = $user_lang ? $user_lang : $spipbb_fromphorum['spip_lang'] ;
			$voir_en_ligne = 'non' ; // utiliser plutot la valeur globale de SpipBB
			$auteur_extra=array();

			$bio = '';

			// utilisation du champ extra si existe
			if (is_array($GLOBALS['champs_extra']) AND is_array($GLOBALS['champs_extra']['auteurs']))
			{
				$auteur_extra['Localisation'] = "" ;
				$auteur_extra['Emploi'] = "" ;
				$auteur_extra['Loisirs'] = "" ;
				$auteur_extra['Numero_ICQ'] = "" ;
				$auteur_extra['Nom_AIM'] = "" ;
				$auteur_extra['Nom_Yahoo'] = "" ;
				$auteur_extra['Nom_MSNM'] = "" ;
				$auteur_extra['avatar'] = "" ;
				$auteur_extra['signature'] = fromphorum_convert($row['signature']) ;
			}
			else
			{ // sinon on stocke dans la bio
				$bio =  item_cond($bio,trim(""));
				$bio = item_cond($bio,trim(""));
				$bio = item_cond($bio,trim(""));
				$bio .= item(trim(""),"-ICQ: %s\n");
				$bio .= item(trim(""),"-AIM: %s\n");
				$bio .= item(trim(""),"-YIM: %s\n");
				$bio .= item(trim(""),"-MSN: %s\n");
			}
			$auteur_extra = serialize($auteur_extra);

			if ($bio != '') { $bio .= "\n"; }

			// converti le bbcode en code SPIP
			$bio = bbcode_to_raccourcis_spip($bio);
			$bio = fromphorum_convert($bio);

			// verifier que cet auteur n existe pas deja
			$spip_id = sql_getfetsel("id_auteur","spip_auteurs","nom='$nom' AND login='$nom'");
			if (empty($spip_id)) {
				// ecrit dans la base SPIP
				$auteur_spip = false;
				if ($spipbb_fromphorum['go']) {
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
					$spipbb_fromphorum['spip_auteur_from_user_id'][$row['user_id']] = $spip_id;
				}
				else
				{
					$res .= "<p>$nom $enligne</p>";
					$query = "INSERT INTO spip_auteurs ".
						"(nom,bio,email,nom_site,url_site,login,pass,statut,en_ligne,imessage,lang,extra)".
						" VALUES ('".$nom."','".$bio."','".$email."','".
						$site."','".$site."','".$nom."','$pass','$statut',".
						"'$enligne','$voir_en_ligne','$lang','$auteur_extra')";
					$spipbb_fromphorum['spip_auteur_from_user_id'][$row['user_id']] = $spipaut++;
					$spip_id = $spipbb_fromphorum['spip_auteur_from_user_id'][$row['user_id']];
				} // ($spipbb_fromphorum['go'])
			}
			else {
				$res .= "<p>$nom existe : $verif</p>";
				$spipbb_fromphorum['spip_auteur_from_user_id'][$row['user_id']] = $spip_id;
				$auteur_spip = true;
			} // emtpy(spip_id)

			// si c'est un nouvel admin, on le restreint a la rubrique du forum
			// verifier que cette restriction n existe pas deja
			$verif = sql_getfetsel("id_auteur","spip_auteurs_rubriques",
					"id_auteur=$spip_id AND id_rubrique=".$spipbb_fromphorum['spiprubid']);
			if (empty($verif)) {
				if ( ($statut == '0minirezo') AND (! $auteur_spip) ) {
					if ($spipbb_fromphorum['go']) {
						$l_id = sql_insertq('spip_auteurs_rubriques', array(
									'id_auteur'=>$spip_id,
									'id_rubrique'=>$spipbb_fromphorum['spiprubid'])
								);
					}
					else {
						$res .= "<p>Ajout admin restreint</p>";
						$query = "INSERT INTO spip_auteurs_rubriques (id_auteur,id_rubrique) ".
							"VALUES ('$spip_id','".$spipbb_fromphorum['spiprubid']."')";
						$res .= "<p>$query</p>";
					}
				}
			}
			else {
				$res .= "<p>Deja admin restreint</p>";
			}

			if (($compte_user % 20) == 0) { $res .= sprintf("<p>[%d]",$compte_user); }
			$res .= '.';
			$compte_user++;
		}
	}
	$res .= "\n<p> Ajout de $compte_user utilisateurs.</p>" ;

	return $res;
} // migre_utilisateurs

// ------------------------------------------------------------------------------
// [fr] importation des topics et posts
// ------------------------------------------------------------------------------
function migre_threads() {
	global $spipbb_fromphorum;

	$res .= "<p>Import des topics et des posts</p>";

	select_phorum_db();
	$query = "SELECT * FROM ".$spipbb_fromphorum['PR']."messages ".
		" ORDER BY message_id";

	$result = mysql_query($query,$spipbb_fromphorum['phorum_connect']) or
		die("Impossible de recuperer les posts : ".mysql_error($spipbb_fromphorum['phorum_connect']));

	$compte_posts = 0;
	$topic_post = array();
	$poid = 0;
	select_spip_db();
	while ($row = mysql_fetch_assoc($result))
	{
		// le premier post rencontre pour un topic donne est
		// le post qui a lance le topic (normalement)
		$topic_id = $row['message_id'];
		if (empty($row['parent_id'])) {
			$id_parent = 0;
		}
		else {
			$id_parent = $topic_post[$topic_id];
		}

		// traite le cas des posts qui appartiennent a un forum qui n'existe plus
		if (! isset($spipbb_fromphorum['spip_art_from_forumid'][$row['forum_id']])) { continue; }

		$id_article = $spipbb_fromphorum['spip_art_from_forumid'][$row['forum_id']];
		$date_heure = date("Y-m-d H:i:s",$row['datestamp']);
		$lastmod = Date("Y-m-d H:i:s", $row['modifystamp']);
		$titre = fromphorum_convert($row['subject']);

		$texte = fromphorum_convert($row['body']);

		$poster_id = $row['user_id'];
		if (! isset($spipbb_fromphorum['spip_auteur_from_user_id'][$poster_id])) {
			$id_auteur = 0;
		}
		else {
			$id_auteur = $spipbb_fromphorum['spip_auteur_from_user_id'][$poster_id];
		}

		$username = fromphorum_convert($row['author']);
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

		if($row['status'] == 2) $statut = "publie";
		else $statut = "off";

		// converti le bbcode en raccourcis SPIP
		$titre = bbcode_to_raccourcis_spip($titre);
		$texte = bbcode_to_raccourcis_spip($texte);


		if (empty($id_parent)) {
			$id_thread=$id_forum ;

			// verifier que ce topic n existe pas deja
			// tres couteux : $verif = sql_getfetsel("id_forum","spip_forum","id_parent=$id_parent
			// AND id_article=$id_article AND texte='".$texte."'");
			$verif=0;

			if (empty($verif)) {
				if ($spipbb_fromphorum['go']) {
					$insert_id = sql_insertq('spip_forum', array(
							'id_parent'=>$id_parent,
							'id_article'=>$id_article,
							'email_auteur'=>$email_auteur,
							'date_heure'=>$date_heure,
							'titre'=>$titre,
							'texte'=>$texte,
							'auteur'=>$auteur,
							'statut'=>$statut,
							'id_auteur'=>$id_auteur,
							'date_thread'=>$date_heure,
							'maj' => $lastmod )
							);
				}
				else {
					$res .= "<p>Ajout thread</p>";
					$query = "INSERT INTO spip_forum ".
						"(id_parent,id_article,email_auteur,date_heure,titre,texte,auteur,".
						"statut,id_auteur,date_thread,maj) VALUES ".
						"('$id_parent','$id_article','".$email_auteur."','$date_heure',".
						"'".$titre."','".$texte."','".$auteur.
						"','$statut','$id_auteur','$date_heure','$lastmod')";
					$res .= "<p>$query</p>";
					$insert_id=$poid++;
				}
			}
			else {
				$res .= "<p>Forum existe :$verif</p>";
				$insert_id = $verif;
			}
		}

		if (empty($id_parent)) {$id_thread=$insert_id;} else {$id_thread=$id_parent;}

		if ($spipbb_fromphorum['go']) {
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

		if ($row['closed']!=0) {
			if ($spipbb_fromphorum['mc_ferme_id'] != 0 ) {
				if ($spipbb_fromphorum['go']) {
					$l_id = sql_insertq('spip_mots_forum', array(
								'id_mot'=>$spipbb_fromphorum['mc_ferme_id'],
								'id_forum'=>$insert_id)
							);
				}
				else {
					$res .= "<p>Ferme</p>";
					$query = "INSERT INTO spip_mots_forum (id_mot,id_forum) VALUES (".
							$spipbb_fromphorum['mc_ferme_id'].",$insert_id)";
					$res .= "<p>$query</p>";
				}
				$res .= "<p>Ferme - id_php_bb = $topic_id - spip_id = $insert_id</p>";
			}
		}

		if ( $row['sort'] != 2)
		{
			// Traitement des post-its
			if ($spipbb_fromphorum['mc_postit_id'] != 0 && $row['sort'] == 1) {
				if ($spipbb_fromphorum['go']) {
					$l_id = sql_insertq('spip_mots_forum', array(
								'id_mot'=>$spipbb_fromphorum['mc_postit_id'],
								'id_forum'=>$insert_id)
							);
				}
				else {
					$res .= "<p>Post-it</p>";
					$query = "INSERT INTO spip_mots_forum (id_mot,id_forum) VALUES (".
							$spipbb_fromphorum['mc_postit_id'].",$insert_id)";
					$res .= "<p>$query</p>";
				}
				$res .= "<p>Post it - id_php_bb = $topic_id - spip_id = $insert_id</p>";
			}

		} // traitement des messages speciaux

		if (($compte_posts % 100) == 0) { $res .= sprintf("<p>[%d]",$compte_posts); }
		$res .= '.';
		$compte_posts ++;
	}
	$res .= "\n<p> Ajout de $compte_posts posts.</p>" ;

	return $res;
} // migre_threads

?>
