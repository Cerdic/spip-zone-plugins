
<?php

if (!defined('_DIR_PLUGIN_DIDASPIP')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_DIDASPIP',(_DIR_PLUGINS.end($p)));
}

function exec_supprdidaspip(){
	global $updatetable;
	global $connect_statut;
	//global $modif;
	
	include_spip ("inc/presentation");
	include_spip ("inc/documents");
	include_spip('inc/indexation');
	include_spip ("inc/logos");
	include_spip ("inc/session");
//charger les chaines de caractères
include("dida_lang.php");
//charger les fonctions
include("dida_fonctions.php");
	//
	// Recupere les donnees
	//

	//debut_gauche();
$commencer_page = charger_fonction('commencer_page', 'inc') ;
	echo $commencer_page(_T('supprdidaspip'),"", "") ;

	//////////////////////////////////////////////////////
	// Boite "voir en ligne"
	//
/*
	debut_boite_info();

	echo propre(_T('gestdoc:info_doc'));

	fin_boite_info();
*/
//echo _DIR_PLUGIN_DIDASPIP; 
	global $connect_statut;
	if ($connect_statut != '0minirezo') {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}
	
	$dir = @opendir(_DIR_IMG."didapages/");
	/*while (false !== ($app = readdir($dir))) 
		if ($app!='.' and $app!='..' and is_dir("../IMG/didapages/".$app)){
			if (is_file('admin/travail/'.$app.'/'.$_GET['cours'].'.blo')) unlink('admin/travail/'.$app.'/'.$_GET['cours'].'.blo');
			if (is_file('admin/travail/'.$app.'/'.$_GET['cours'].'.log')) unlink('admin/travail/'.$app.'/'.$_GET['cours'].'.log');
			if (is_file('admin/travail/'.$app.'/'.$_GET['cours'].'.xml')) unlink('admin/travail/'.$app.'/'.$_GET['cours'].'.xml');
			if (is_file('admin/travail/'.$app.'/'.$_GET['cours'].'.dat')) unlink('admin/travail/'.$app.'/'.$_GET['cours'].'.dat');
		}
	closedir($dir);*/
	//supprimer le cours
	/*rmdirr("cours/".$_GET['cours']);*/
	@rmdirr(_DIR_IMG."didapages/".$_GET['cours']);
	//afficher la liste
	//ouf ! allez hop, on affiche la liste des cours
	include("dida_menu.php");
	include("dida_pagecours.php");



}
?>
