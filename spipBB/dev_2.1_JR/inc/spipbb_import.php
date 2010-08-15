<?php
#--------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                            #
#  File    : inc/spipbb_import                                 #
#  Authors : Chryjs, 2008 et als                               #
#            2004+ Jean-Luc Bechennec certaines fonctions      #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs     #
#  Contact : chryjs!@!free!.!fr                                #
# [fr] Librairie de fonctions communes pour l'import de forums #
# [en] Functions and data required for importing forums        #
#--------------------------------------------------------------#

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
// [fr] Protection contre les inclusions multiples (ne devrait jamais arriver)
// [en] Protects against multiples includes (should never occur)
if (defined("_INC_SPIPBB_IMPORT")) return; else define("_INC_SPIPBB_IMPORT", true);

spipbb_log('included',2,__FILE__);

include_spip('inc/editer_article');

// ------------------------------------------------------------------------------
// [fr] Chargement des metas
// [en] Load metas
// ------------------------------------------------------------------------------
function import_load_metas($origine,$spiprubid=0)
{
	global $spipbb_import;
	include_spip('inc/meta');
	lire_metas();
	if (isset($GLOBALS['meta']['spipbb_import']))
	{
		$spipbb_import = unserialize($GLOBALS['meta']['spipbb_import']);
		// ce n'est pas la fonction pour faire cela !
		// rappel de connexion
		//$spipbb_import['phpbb_connect'] =
		//@mysql_connect($spipbb_fromphpbb['phpbb_host'],$spipbb_fromphpbb['phpbb_login'],$spipbb_fromphpbb['phpbb_pass']) or
		//	die(_T('spipbb:fromphpbb_erreur_db_phpbb'));
	}
	else
	{
		import_init_metas($origine,$spiprubid);
	}
} // import_load_metas

// ------------------------------------------------------------------------------
// [fr] Initialisation des metas
// [en] Metas initialization
// ------------------------------------------------------------------------------
function import_init_metas($origine,$spiprubid=0)
{
	global $spipbb_import; // stockage des informations et des etapes

	$spipbb_import=array();
	$spipbb_import['origine']=$origine;
	$spipbb_import['spiprubid'] = ($spiprubid==0) ? $GLOBALS['spipbb']['id_secteur'] : $spiprubid ;
	$spipbb_import['spiprub_from_catid'] = array();
	$spipbb_import['spip_art_from_forumid'] = array();
	$spipbb_import['spip_auteur_from_user_id'] = array();
	$spipbb_import['spip_post_from_post_id'] = array();
	$spipbb_import['spip_lang'] = $GLOBALS['meta']['langue_site'];
	$spipbb_import['connexion'] = $GLOBALS['connexions'][0];
	$spipbb_import['prefixe'] = $GLOBALS['connexions'][0]['prefixe'];
	$spipbb_import['link'] = $GLOBALS['connexions'][0]['link'];
	$spipbb_import['db'] = $GLOBALS['connexions'][0]['db'];
	$spipbb_import['statut_abonne'] = _SPIPBB_STATUT_ABONNE ;
/*
	$spipbb_import['mc_annonce_id'] = $GLOBALS['spipbb']['id_mot_annonce'];
	$spipbb_import['mc_postit_id'] = $GLOBALS['spipbb']['id_mot_postit'];
	$spipbb_import['mc_ferme_id'] = $GLOBALS['spipbb']['id_mot_ferme'];
*/

/*
	A mettre dans une fonction specifique

	// [fr] recupere les parametres de connexion
	// [en] Grab the connection parameters

	$source_nr = intval(_request('fromphpbb_source'));
	$filename = _request('fromphpbb_filename_'.$source_nr);
	$tablename = _request('fromphpbb_table_'.$source_nr);
	if ($filename) {
		global $dbhost,$dbuser,$dbpasswd,$dbname,$table_prefix;
		require($filename);
		$spipbb_import['phpbb_host'] = $dbhost;
		$spipbb_import['phpbb_login'] = $dbuser;
		$spipbb_import['phpbb_pass'] = $dbpasswd;
		$spipbb_import['phpdb'] = $dbname;
		$spipbb_import['phpbbroot'] = dirname($filename); // remove config.php
		$spipbb_import['PR'] = $table_prefix;
	} else if ($tablename) {
		// Meme base que spip
		$f = _FILE_CONNECT ;
		$handle = fopen ($f, "r");
		$contents = fread ($handle, filesize ($f));
		fclose ($handle);
		$r=preg_match("#spip_connect_db\('([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)#",$contents,$params);

		$spipbb_import['phpbb_host'] = $params[1];
		$spipbb_import['phpbb_login'] = $params[3];
		$spipbb_import['phpbb_pass'] = $params[4];
		$spipbb_import['phpdb'] = $params[5];
		$spipbb_import['phpbbroot'] = _request('fromphpbb_table_path_'.$source_nr);
		$spipbb_import['PR'] = substr($tablename,0,-6); // enlever config du nom
	} else {
		$spipbb_import['phpbb_host'] = _request('phpbb_host');
		$spipbb_import['phpbb_login'] = _request('phpbb_login');
		$spipbb_import['phpbb_pass'] = _request('phpbb_pass');
		$spipbb_import['phpdb'] = _request('phpbb_base');
		$spipbb_import['phpbbroot'] = _request('phpbb_root');
		$spipbb_import['PR'] = _request('phpbb_prefix');
	}
	if (empty($spipbb_import['phpbb_host'])) $spipbb_import['phpbb_host']="localhost";
	$spipbb_import['spiproot'] = _DIR_RACINE;
	$spipbb_import['phpbb_connect'] =
		@mysql_connect($spipbb_import['phpbb_host'],$spipbb_import['phpbb_login'],$spipbb_import['phpbb_pass']) or
			die(_T('spipbb:fromphpbb_erreur_db_phpbb'));
	select_phpbb_db();
	$result = @mysql_query("SELECT config_value FROM ".$spipbb_import['PR'].
			"config WHERE config_name='default_lang'") or
			die(_T('spipbb:fromphpbb_erreur_db_phpbb_config'));
	$row = @mysql_fetch_assoc($result);
	$spipbb_import['phpbb_lang'] = substr($row['config_value'],0,2);
	$spipbb_import['phpbb_lang'] = $spipbb_import['phpbb_lang'] ? $spipbb_import['phpbb_lang'] :
						$spipbb_import['spip_lang'];
*/

	$spipbb_import['spiproot'] = _DIR_RACINE;

	$spipbb_import['host'] = '';
	$spipbb_import['login'] = '';
	$spipbb_import['pass'] = '';
	$spipbb_import['db'] = '';
	$spipbb_import['chemin'] = '';
	$spipbb_import['prefixe_table'] = '';

	// recuperation du secteur ou seront implantes les forums
	select_spip_db();
	$result = sql_select('id_secteur','spip_rubriques', "id_rubrique='".$spipbb_import['spiprubid']."'");
	$row = sql_fetch($result) or die(_T('spipbb:import_erreur_db_spip'));
	$spipbb_import['spip_id_secteur'] = $row['id_secteur'];

//	$spipbb_import['go'] = ( _request('phpbb_test') != 'oui' );
	$spipbb_import['etape'] = 0;
}

// ------------------------------------------------------------------------------
// Fonction supprimee en SVN... a remplacer ?
// etait dans : inc/editer_article
// ------------------------------------------------------------------------------

if (version_compare($GLOBALS['spip_version_code'],_SPIPBB_REV_EDITER_ARTRUB,'>')) {
if (!function_exists('')) {
function editer_article_rubrique($id_rubrique, $id_secteur, $config, $aider)
{
	$chercher_rubrique = charger_fonction('chercher_rubrique', 'inc');

	$opt = $chercher_rubrique($id_rubrique, 'article', $config['restreint']);

	$msg = _T('titre_cadre_interieur_rubrique') .
	  ((preg_match('/^<input[^>]*hidden[^<]*$/', $opt)) ? '' : $aider("artrub"));

	if ($id_rubrique == 0) $logo = "racine-site-24.gif";
	elseif ($id_secteur == $id_rubrique) $logo = "secteur-24.gif";
	else $logo = "rubrique-24.gif";

	return debut_cadre_couleur($logo, true, "", $msg) . $opt .fin_cadre_couleur(true);
} } } // editer_article_rubrique

// ------------------------------------------------------------------------------
// [fr] Module de chargement des fonctions d'import suivant la nature du traitement
// [en] Load import function according to the requirements
// ------------------------------------------------------------------------------
function import_charger_fonction($origine,$fonction) {
	$f = $fonction."_".$origine;
	if (function_exists($f)) return $f;

	include_spip('inc/minipres');
	echo minipres(_T('forum_titre_erreur'), _T('spipbb:import_charger_fonction_dpt').$f);
	exit;
}

// ------------------------------------------------------------------------------
// [fr] Genere les listes de sources possibles avec phpbb
// ------------------------------------------------------------------------------
function import_genere_liste_sources_phpbb(&$radio) {
	// [fr] On va essayer de "deviner" ou on peut trouver un fichier de conf phpbb
	// [en] We try to "guess" where is the phpbb config file
	$phpbb_subdirs = array('.', 'forum','phpBB','phpBB2','phpBB3','FORUM','PHPBB','PHPBB2','PHPBB3');
	$phpbb_roots = array( realpath(_DIR_RACINE), $GLOBALS['_SERVER']['DOCUMENT_ROOT'] );
	$liste_fichiers = "";
	$radio=0;

	while ( list($k,$rootdir) = each($phpbb_roots) ) {
		while ( list($key, $subdir) = each($phpbb_subdirs) ) {
			$filename = $rootdir."/".$subdir."/config.php" ;
			if ( file_exists($filename) AND is_readable($filename) ) {
				@include_once($filename);
				if (defined('PHPBB_INSTALLED') and (substr($dbms,0,5)=="mysql") ) {
					$conf['filename'] = $filename;
					$contexte = array(
						'filename'=>$filename,
						'key'=>$radio,
						);
					$liste_fichiers .= recuperer_fond("prive/spipbb_admin_fromphpbb_fichiers", $contexte) ;
					$radio++;
				} // defined and mysql only
			} // file_exists
		} // while subdirs
	} // while rootdirs

	// on peut essayer de deviner aussi si phpbb est installe sur la meme base que spip ?
	$struc_mini_config_phpbb=array(
						"config_name"	=> "varchar(255)",
						"config_value" 	=> "varchar(255)",
						);
	// c: 10/2/8 compat multibases
	$req=sql_showtable("%_config");
	$liste_config=array();
	while ($row = sql_fetch($req)) {
		// on compare la desc avec le mini puis la valeur
		$liste_config[]=join("",$row);
	}

	reset($liste_config);
	while ( list(,$table_config) = each($liste_config) ) {
		if ($table_config) {
			$structure=sql_showtable($table_config);
			$idem=true;
			while ( list($k,$v) = each($struc_mini_config_phpbb) AND $idem )
			{
				$param=preg_split("/\s/",$structure['field'][$k]);
				$champ=strtolower($param[0]);
				$champ=preg_replace("/^char(.*)/","varchar\\1",$champ); // char(x)==varchar(x) ?
				$champ=preg_replace("#^timestamp.*#","timestamp",$champ); // timestamp(14)==timestamp ?
				//$structure['field'][$k]=$champ;
				$idem = ($struc_mini_config_phpbb[$k]==$champ);
			}
			if ($idem) {
				$phpbbversion=sql_fetsel("config_value",$table_config,"config_name='version'");

				// chryjs :le 25/11/07 il ne reste plus qu'a ajouter au formulaire et recuperer les infos de config a l'arrivee

				if ($phpbbversion) {
					// on recupere le chemin vers les avatars
					$avatar_path=sql_fetsel("config_value",$table_config,"config_name='avatar_path'");
					$script_path=sql_fetsel("config_value",$table_config,"config_name='script_path'");
					//echo $phpbbversion['config_value'];script_path
					$contexte = array(
						'avatar_path'=>$script_path['config_value']."/".$avatar_path['config_value'],
						'tablename'=>$table_config,
						'key'=>$radio,
						);
					$liste_fichiers .= recuperer_fond("prive/spipbb_admin_fromphpbb_tables", $contexte) ;
					$radio++;
				} // if ($phpbbversion)
			} // if ($idem) on a trouve une table de meme config que phpbb_
		} //if ($table_config)
	} // while

	return $liste_fichiers;
} // import_genere_liste_sources_phpbb

// ------------------------------------------------------------------------------
// [fr] Genere les listes de sources possibles avec phorum (a partir de v5)
// ------------------------------------------------------------------------------
function import_genere_liste_sources_phorum(&$radio) {
	// [fr] On va essayer de "deviner" ou on peut trouver un fichier de conf Phorum
	// [en] We try to "guess" where is the Phorum config file
	$phorum_subdirs = array('.', 'phorum','forum');
	$phorum_roots = array( realpath(_DIR_RACINE), $GLOBALS['_SERVER']['DOCUMENT_ROOT'] );
	$liste_fichiers = "";
	$radio=0;

	while ( list($k,$rootdir) = each($phorum_roots) ) {
		while ( list($key, $subdir) = each($phorum_subdirs) ) {
			$filename = $rootdir."/".$subdir."/include/db/config.php" ;
			if ( file_exists($filename) AND is_readable($filename) ) {
				define('PHORUM',true); // allow full inclusion
				@include_once($filename);
				if ($PHORUM AND is_array($PHORUM)) {
					$contexte = array(
						'filename'=>$filename,
						'key'=>$radio,
						);
					$liste_fichiers .= recuperer_fond("prive/spipbb_admin_fromphorum_fichiers", $contexte) ;
					$radio++;
				} // defined and mysql only
			} // file_exists
		} // while subdirs
	} // while rootdirs

	// on peut essayer de deviner aussi si phorum est installe sur la meme base que spip ?
	$struc_mini_config_phorum=array(
						"name"	=> "varchar(255)",
//						"type"	=> "enum('V','S')",
						"data" 	=> "text",
						);
	$req=sql_query("SHOW TABLES LIKE '%_settings'");
	$liste_config=array();
	while ($row = sql_fetch($req)) {
		// on compare la desc avec le mini puis la valeur
		$liste_config[]=join("",$row);
	}

	reset($liste_config);
	while ( list(,$table_config) = each($liste_config) ) {
		if ($table_config) {
			$structure=sql_showtable($table_config);
			$idem=true;
			while ( list($k,$v) = each($struc_mini_config_phorum) AND $idem )
			{
				$param=preg_split("/\s/",$structure['field'][$k]);
				$champ=strtolower($param[0]);
				$champ=preg_replace("/^char(.*)/","varchar\\1",$champ); // char(x)==varchar(x) ?
				$champ=preg_replace("#^timestamp.*#","timestamp",$champ); // timestamp(14)==timestamp ?
				//$structure['field'][$k]=$champ;
				$idem = ($struc_mini_config_phorum[$k]==$champ);
			}
			if ($idem) {
				$phorumversion=sql_fetsel("data",$table_config,"name='internal_version'"); // ex: 2007031400

				if ($phorumversion) {
					$contexte = array(
						'tablename'=>$table_config,
						'key'=>$radio,
						);
					$liste_fichiers .= recuperer_fond("prive/spipbb_admin_fromphorum_tables", $contexte) ;
					$radio++;
				}
			} // on a trouve une table de meme config que phorum_
		}
	}
	return $liste_fichiers;
} // import_genere_liste_sources_phorum


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
// [fr] connecte a la base contenant les forums
// ------------------------------------------------------------------------------
function select_import_db()
{
	global $spipbb_import;
	mysql_select_db($spipbb_import['db'],$spipbb_import['connect'])
		or die(_T('spipbb:import_erreur_db'));
} // select_import_db

// ------------------------------------------------------------------------------
// [fr] connecte a la base contenant spip - pas encore compatible multi base
// ------------------------------------------------------------------------------
function select_spip_db() {
	$f = _FILE_CONNECT ;
	if ($f AND is_readable($f)) include($f);
	else die(_T('spipbb:import_erreur_db_spip'));
} // select_spip_db

// ------------------------------------------------------------------------------
// [fr] Initialisation des metas
// [en] Metas initialization
// ------------------------------------------------------------------------------
function import_origine_metas_phpbb()
{
	global $spipbb_import; // stockage des informations et des etapes

	// [fr] recupere les parametres de connexion
	// [en] Grab the connection parameters

	$source_nr = intval(_request('import_source'));
	$filename = _request('import_filename_'.$source_nr);
	$tablename = _request('import_table_'.$source_nr);
	if ($filename) {
		global $dbhost,$dbuser,$dbpasswd,$dbname,$table_prefix;
		require($filename);
		$spipbb_import['host'] = $dbhost;
		$spipbb_import['login'] = $dbuser;
		$spipbb_import['pass'] = $dbpasswd;
		$spipbb_import['db'] = $dbname;
		$spipbb_import['root'] = dirname($filename); // remove config.php
		$spipbb_import['prefixe'] = $table_prefix;
	} else if ($tablename) {
		// Meme base que spip
		$f = _FILE_CONNECT ;
		$handle = fopen ($f, "r");
		$contents = fread ($handle, filesize ($f));
		fclose ($handle);
		$r=preg_match("#spip_connect_db\('([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)#",$contents,$params);

		$spipbb_import['host'] = $params[1];
		$spipbb_import['login'] = $params[3];
		$spipbb_import['pass'] = $params[4];
		$spipbb_import['db'] = $params[5];
		$spipbb_import['root'] = _request('import_table_path_'.$source_nr);
		$spipbb_import['prefixe'] = substr($tablename,0,-6); // enlever config du nom
	} else {
		$spipbb_import['host'] = _request('import_host');
		$spipbb_import['login'] = _request('import_login');
		$spipbb_import['pass'] = _request('import_pass');
		$spipbb_import['db'] = _request('import_base');
		$spipbb_import['root'] = _request('import_root');
		$spipbb_import['prefixe'] = _request('import_prefix');
	}
	if (empty($spipbb_import['host'])) $spipbb_import['host']="localhost";
	$spipbb_import['connect'] =
		@mysql_connect($spipbb_import['host'],$spipbb_import['login'],$spipbb_import['pass']) or
			die(_T('spipbb:import_erreur_db'));
	select_import_db();
	$result = @mysql_query("SELECT config_value FROM ".$spipbb_import['prefixe'].
			"config WHERE config_name='default_lang'") or
			die(_T('spipbb:import_erreur_db_config'));
	$row = @mysql_fetch_assoc($result);
	$spipbb_import['lang'] = substr($row['config_value'],0,2);
	$spipbb_import['lang'] = $spipbb_import['lang'] ? $spipbb_import['lang'] :
						$spipbb_import['spip_lang'];

	$spipbb_import['go'] = ( _request('phpbb_test') != 'oui' );
} // import_origine_metas_phpbb

// ------------------------------------------------------------------------------
// [fr] Initialisation
// ------------------------------------------------------------------------------
function import_origine_metas_phorum()
{
	global $spipbb_import; // stockage des informations et des etapes

	// [fr] recupere les parametres de connexion
	// [en] Grab the connection parameters

	$source_nr = intval(_request('import_source'));
	$filename = _request('import_filename_'.$source_nr);
	$tablename = _request('import_table_'.$source_nr);
	if ($filename) {
		define('PHORUM',true); // allow full inclusion
		require($filename);
		$spipbb_import['host'] = $PHORUM['DBCONFIG']['server'];
		$spipbb_import['login'] = $PHORUM['DBCONFIG']['user'];;
		$spipbb_import['pass'] = $PHORUM['DBCONFIG']['password'];;
		$spipbb_import['db'] = $PHORUM['DBCONFIG']['name'];;
		$spipbb_import['root'] = dirname($filename); // remove config.php
		$spipbb_import['prefixe'] = $PHORUM['DBCONFIG']['table_prefix']."_";
	} else if ($tablename) {
		// Meme base que spip
		$f = _FILE_CONNECT ;
		$handle = fopen ($f, "r");
		$contents = fread ($handle, filesize ($f));
		fclose ($handle);
		$r=preg_match("#spip_connect_db\('([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)'\s*,\s*'([^']*)#",$contents,$params);

		$spipbb_import['host'] = $params[1];
		$spipbb_import['login'] = $params[3];
		$spipbb_import['pass'] = $params[4];
		$spipbb_import['db'] = $params[5];
		$spipbb_import['root'] = _request('import_table_path_'.$source_nr);
		$spipbb_import['prefixe'] = substr($tablename,0,-6); // enlever config du nom
	} else {
		$spipbb_import['host'] = _request('import_host');
		$spipbb_import['login'] = _request('import_login');
		$spipbb_import['pass'] = _request('import_pass');
		$spipbb_import['db'] = _request('import_base');
		$spipbb_import['root'] = _request('import_root');
		$spipbb_import['prefixe'] = _request('import_prefix');
	}
	if (empty($spipbb_import['host'])) $spipbb_import['host']="localhost";

	$spipbb_import['connect'] =
		@mysql_connect($spipbb_import['host'],$spipbb_import['login'],$spipbb_import['pass']) or
			die(_T('spipbb:import_erreur_db'));

	select_import_db();
	$spipbb_import['lang'] = $spipbb_import['lang'] ? $spipbb_import['lang'] : $spipbb_import['spip_lang'];

	$spipbb_import['go'] = ( _request('phorum_test') != 'oui' );
}

// ------------------------------------------------------------------------------
// [fr]
// ------------------------------------------------------------------------------
function import_rappel_connexion()
{
	global $spipbb_import;
	// rappel de connexion
	$spipbb_import['connect'] =
		@mysql_connect($spipbb_import['host'],$spipbb_import['login'],$spipbb_import['pass']) or
			die(_T('spipbb:import_erreur_db_rappel_connexion'));
} // import_rappel_connexion

?>
