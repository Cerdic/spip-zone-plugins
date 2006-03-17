<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
ini_set("zlib.output_compression","0"); // pour permettre l'affichage au fur et a mesure


include_spip('exec/export'); // celui dans le meme repertoire, pas celui de ecrire
include_spip('inc/admin');
include_spip('base/serial');
include_spip('base/auxiliaires');
include_spip('inc/indexation'); // pour la fonction primary_index_table 
include_spip('inc/flock');

global $IMPORT_tables_noexport;
$IMPORT_tables_noexport[]='spip_ajax_fonc';
$IMPORT_tables_noexport[]='spip_caches';
$IMPORT_tables_noexport[]='spip_meta';
$IMPORT_tables_noexport[]='spip_index';
$IMPORT_tables_noexport[]='spip_index_dico';
$IMPORT_tables_noexport[]='spip_referers';
$IMPORT_tables_noexport[]='spip_referers_articles';
$IMPORT_tables_noexport[]='spip_visites';
$IMPORT_tables_noexport[]='spip_visites_articles';


//
// Ecrire un fichier de maniere un peu sure
//
// zippe les fichiers .gz

function ecrire_fichier_ajout ($fichier, $contenu, $ecrire_quand_meme = false) {

	// Ne rien faire si on est en preview, debug, ou si une erreur
	// grave s'est presentee (compilation du squelette, MySQL, etc)
	if (($GLOBALS['var_preview'] OR ($GLOBALS['var_mode'] == 'debug')
	OR defined('spip_interdire_cache'))
	AND !$ecrire_quand_meme)
		return;

	$gzip = (substr($fichier, -3) == '.gz');

	#spip_timer('ecrire_fichier');

	// verrouiller le fichier destination
	if ($fp = @fopen($fichier, 'a'))
		@flock($fp, LOCK_EX);
	else
		return false;

	// ecrire les donnees, compressees le cas echeant
	// (on ouvre un nouveau pointeur sur le fichier, ce qui a l'avantage
	// de le recreer si le locker qui nous precede l'avait supprime...)
	if ($gzip) $contenu = gzencode($contenu);
	#@ftruncate($fp,0); // pas de troncage : c'est un ajout
	$s = @fputs($fp, $contenu, $a = strlen($contenu));

	$ok = ($s == $a);

	// liberer le verrou et fermer le fichier
	@flock($fp, LOCK_UN);
	@fclose($fp);

	if (!$ok) {
		spip_log("echec ecriture fichier $fichier");
		@unlink($fichier);
	}

	return $ok;
}

function rammasse_parties($archive, $gz, $partfile){
	/*$_fputs = ($gz) ? gzputs : fputs;

	$f = ($gz) ? gzopen($archive, "ab") : fopen($archive, "ab");
	if (!$f) {
		echo _T('avis_erreur_sauvegarde', array('type'=>'.', 'id_objet'=>'. .'));
		exit;
	}

	$cpt=0;
	while(file_exists($partfile.".$cpt")){
	  $fpart = fopen($partfile.".$cpt",'rb');
	  while (!feof($fpart)){
			$part = fread($fpart,8192);
			$_fputs($f,$part);
		}
		fclose ($fpart);
		unlink($partfile.".$cpt");
		$cpt++;
	}
	if ($gz) gzclose($f);
	else fclose($f);
	*/
	$cpt=0;
	while(file_exists($f = $partfile.".$cpt")){
		$contenu = "";
		if (lire_fichier ($f, $contenu))
			if (!ecrire_fichier_ajout($archive,$contenu))
			{
				echo _T('avis_erreur_sauvegarde', array('type'=>'.', 'id_objet'=>'. .'));
				exit;
			}
		unlink($f);
		$cpt++;
	}
}

function exec_export_all_dist()
{
  global $archive, $debut_limit, $etape, $gz, $spip_version, $spip_version_affichee, $version_archive;

	echo "<img src='"._DIR_PLUGIN_SUPER_DUMP."/super_dump.png' width='48' height='48' alt='Super Dump'/>";
	if (!$archive) {
		if ($gz) $archive = "dump.xml.gz";
		else $archive = "dump.xml";
	}
	$partfile = $archive.".part";
	
	// utiliser une version fraiche des metas (ie pas le cache)
	include_ecrire('inc_meta');
	lire_metas();

	$action = _T('info_exportation_base', array('archive' => $archive));
	if (!isset($GLOBALS['meta']["status_dump"])){
	  $start = true;
	}
	else{
		$status_dump = explode("::",$GLOBALS['meta']["status_dump"]);
		if (($status_dump[0]!=$gz)||($status_dump[1]!=$archive))
			$start = true;
		else
			$start = ($status_dump[2]==0)&&($status_dump[3]==0);
	}
	if ($start){
	  // phase admin en debut de dump
	  // apres, on continue sans verif : 
		// sur la duree du dump cela genere de demandes recurrentes d'authent
		debut_admin(generer_url_post_ecrire("export_all","archive=$archive&gz=$gz"), $action);
		fin_admin($action);
	}

	install_debut_html(_T('info_sauvegarde'));

	//if (!$etape) echo "<p><blockquote><font size=2>"._T('info_sauvegarde_echouee')." <a href='" . generer_url_ecrire("export_all","reinstall=non&etape=1&gz=$gz") . "'>"._T('info_procedez_par_etape')."</a></font></blockquote><p>";


	$_fputs = ($gz) ? gzputs : fputs;

	if ($start){
		$status_dump = "$gz::$archive::0::0";
		ecrire_meta("status_dump", "$status_dump");
		$status_dump = explode("::",$status_dump);
		ecrire_metas();
		echo _L("Debut du Sauvegarde")."<br/>";
		$f = ($gz) ? gzopen(_DIR_SESSIONS . $archive, "wb") : fopen(_DIR_SESSIONS . $archive, "wb");
		if (!$f) {
			echo _T('avis_erreur_sauvegarde', array('type'=>'.', 'id_objet'=>'. .'));
			exit;
		}

		$_fputs($f, "<"."?xml version=\"1.0\" encoding=\"".$GLOBALS['meta']['charset']."\"?".">\n<SPIP version=\"$spip_version_affichee\" version_base=\"$spip_version\" version_archive=\"$version_archive\">\n\n");
		if ($gz) gzclose($f);
		else fclose($f);
	}
	else{
		echo _L("Sauvegarde en cours : Etape ".$status_dump[2]." ligne ".$status_dump[3])."<br/>";
		$f = ($gz) ? gzopen(_DIR_SESSIONS . $archive, "ab") : fopen(_DIR_SESSIONS . $archive, "ab");
		if (!$f) {
			echo _T('avis_erreur_sauvegarde', array('type'=>'.', 'id_objet'=>'. .'));
			exit;
		}
		if ($gz) gzclose($f);
		else fclose($f);
	}

	// construction de la liste des tables pour le dump :
	// toutes les tables principales
	// + toutes les tables auxiliaires
	// - les tables relations (exportees en tant que liens avec chaque objet lie)
	$tables_for_dump = array();
	$tables_for_link = array();
	$tables_pointees = array();
	global $IMPORT_tables_noexport;
	global $tables_principales;
	global $tables_auxiliaires;
	global $table_des_tables;
	global $tables_relations;

	$liste_tables = array_merge(array_keys($tables_principales),array_keys($tables_auxiliaires));
	foreach($liste_tables as $table){
		$name = preg_replace("{^spip_}","",$table);
		$tables_for_link[$table] = array();
	  if (!isset($tables_pointees[$table]) && !in_array($table,$IMPORT_tables_noexport)){
			$tables_for_dump[] = $table;
			$tables_pointees[$table] = 1;
			$relation = $tables_relations[$table];
			if (!$relation) $relation = $tables_relations[$name];
			if ($relation){
				foreach($relation as $id=>$link_table){
					if (isset($tables_auxiliaires["$link_table"])||isset($tables_principales["$link_table"])){
						$tables_for_link[$table]["$link_table"]=$id;
						$tables_pointees["$link_table"] = 1;
					}
					else if (isset($tables_auxiliaires["spip_$link_table"])||isset($tables_principales["spip_$link_table"])){
						$tables_for_link[$table]["spip_$link_table"]=$id;
						$tables_pointees["spip_$link_table"] = 1;
					}
				}
			}
		}
	}

	ob_flush();flush();

	$status_dump = explode("::",$GLOBALS['meta']["status_dump"]);
	$etape = $status_dump[2];

	if ($etape >= count($tables_for_dump)){
		foreach($tables_for_dump as $i=>$table){
			export_objets($table, primary_index_table($table), $tables_for_link[$table],$fpart, false, $i, _L("Sauvegarde de la table $table"));
		}

		ob_flush();flush();
		rammasse_parties(_DIR_SESSIONS . $archive, $gz, _DIR_SESSIONS . $partfile);

		$f = ($gz) ? gzopen(_DIR_SESSIONS . $archive, "ab") : fopen(_DIR_SESSIONS . $archive, "ab");
		$_fputs ($f, build_end_tag("SPIP")."\n");
		if ($gz) gzclose($f);
		else fclose($f);
		
		effacer_meta("status_dump");
		ecrire_metas();
		echo "<p>"._T('info_sauvegarde_reussi_01')."</b><p>"._T('info_sauvegarde_reussi_02', array('archive' => $archive))." <a href='./'>"._T('info_sauvegarde_reussi_03')."</a> "._T('info_sauvegarde_reussi_04')."\n";
	}
	else{
		$max_time = ini_get('max_execution_time')*1000;
		$timeout = $max_time;
	  if ($start) $timeout = round($timeout/2);
		// script de rechargement auto sur timeout
		echo ("<script language=\"JavaScript\" type=\"text/javascript\">window.setTimeout('location.href=\"".generer_url_ecrire("export_all","archive=$archive&gz=$gz",true)."\";',$timeout);</script>\n");
		$cpt = 0;
		$paquets = 400;
		foreach($tables_for_dump as $i=>$table){
			// par paquets
			list($string,$status_dump)=export_objets($table, primary_index_table($table), $tables_for_link[$table],0, false, $i, _L("Sauvegarde de la table $table"),$paquets);
		  while ($string!=''){
				if ($cpt == 0)
					rammasse_parties(_DIR_SESSIONS . $archive, $gz, _DIR_SESSIONS . $partfile);

				// on ecrit dans un fichier generique
				ecrire_fichier (_DIR_SESSIONS .$partfile, $string);
				/*$fpart = fopen(_DIR_SESSIONS .$partfile,'wb');
				fputs($fpart, $string);
				fclose($fpart);*/
				// on le renomme avec un numero -> operation atomique en linux
				rename(_DIR_SESSIONS .$partfile,_DIR_SESSIONS .$partfile.".$cpt");
				$cpt ++;
				ecrire_meta("status_dump", implode("::",$status_dump));
				ecrire_metas();
				list($string,$status_dump)=export_objets($table, primary_index_table($table), $tables_for_link[$table],0, false, $i, _L("Sauvegarde de la table $table"),$paquets);
			}
			ecrire_meta("status_dump", implode("::",$status_dump));
			ecrire_metas();
		}
		// pour recharger la page tout de suite en finir le ramassage
		echo ("<script language=\"JavaScript\" type=\"text/javascript\">window.setTimeout('location.href=\"".str_replace("&amp;","&",generer_url_ecrire("export_all","archive=$archive&gz=$gz"))."\";',0);</script>\n");
	}

	install_fin_html();

}


?>