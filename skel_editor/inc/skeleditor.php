<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

define('_SE_EXTENSIONS_IMG',"jpg,png,gif,ico,bmp");
define('_SE_EXTENSIONS',_SE_EXTENSIONS_IMG.",htm,html,xml,svg,php,php3,php4,py,sh,sql,css,rdf,txt,nfo,log,js,as,csv,");

/**
 * Lister les fichiers editables
 */
function skeleditor_files_editables(){
	// charger les plugin qui peuvent definir un squelette
	if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){
		// chargement optimise precompile
		include_once(_DIR_SESSIONS."charger_plugins_fonctions.php");
	}
	$path = creer_chemin();
  $dossier_squelettes = reset($path);


	$files_editable = parse_path($dossier_squelettes,explode(',',_SE_EXTENSIONS));
	$files_editable = sort_directory_first($files_editable,$dossier_squelettes); // utile ?
	return $files_editable;
}

?>