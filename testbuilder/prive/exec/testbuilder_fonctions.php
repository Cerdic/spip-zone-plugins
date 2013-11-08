<?php
/*
 * Plugin TestBuilder
 * (c) 2010 Cedric MORIN Yterium
 * Distribue sous licence GPL
 *
 */


include_spip('inc/tb_lib');

function tb_link($url,$texte){
	return "<a href='$url'>$texte</a>";
}

function tb_dirs($dir=""){
	$dir = trim($dir);
	if (!$dir
			OR substr($dir,0,1)=="/"
			OR substr($dir,strlen(_DIR_RACINE),2)==".."
			OR !@is_dir($dir) OR !is_readable($dir) OR !$d = @opendir($dir)) {

		$plugins_dist = defined('_DIR_PLUGINS_DIST')?_DIR_PLUGINS_DIST:_DIR_EXTENSIONS;
		if ($d)
			closedir($d);
		return array(
			basename(_DIR_RESTREINT_ABS)=>_DIR_RESTREINT?_DIR_RESTREINT:"./",
			basename(_DIR_PLUGINS)=>_DIR_PLUGINS,
			basename($plugins_dist)=>$plugins_dist,
			"prive"=>_DIR_RACINE."prive/",
		);
	}

	$dir = rtrim($dir,'/');
	$dirs = array("../"=>($dir=="." OR dirname($dir)==rtrim(_DIR_RACINE,'/'))?"":rtrim(dirname($dir),'/')."/");
	$maxdirs = 1000;
	while (($f = readdir($d)) !== false && ($nbdirs<$maxdirs)) {
		if ($f[0] != '.' # ignorer . .. .svn etc
		AND $f != 'CVS'
		AND $f != 'remove.txt'
		AND is_dir("$dir/$f")) {
			$dirs[$f] = "$dir/$f/";
		}
		$nbdirs++;
	}
	closedir($d);
	return $dirs;
}

function tb_files($dir=""){
	if (!$dir
			OR substr($dir,0,1)=="/"
			OR substr($dir,strlen(_DIR_RACINE),2)=="..")
			return array();
	$dir = rtrim($dir,'/').'/';
	$files = preg_files($dir,"\.php$",1000,false);

	$liste = array();
	foreach($files as $f){
		$liste[strtolower(basename($f))]=$f;
	}
	return $liste;
}
?>