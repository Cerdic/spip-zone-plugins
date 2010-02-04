<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

define('_SE_EXTENSIONS_IMG',"jpg|png|gif|ico|bmp");
define('_SE_EXTENSIONS',_SE_EXTENSIONS_IMG."|htm|html|xml|svg|php|php3|php4|py|sh|sql|css|rdf|txt|nfo|log|js|as|csv");

/**
 * Determiner le dossier de travail
 *
 * @return string
 */
function skeleditor_path_editable(){
	// charger les plugin qui peuvent definir un squelette
	if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){
		// chargement optimise precompile
		include_once(_DIR_SESSIONS."charger_plugins_fonctions.php");
	}
	$path = creer_chemin();
  return reset($path);
}

/**
 * Lister les fichiers editables
 */
function skeleditor_files_editables($path=null){
	if (is_null($path))
		$path = skeleditor_path_editable();

	$files_editable = preg_files($path,'[.]('._SE_EXTENSIONS.')$');
	#$files_editable = sort_directory_first($files_editable,$dossier_squelettes); // utile ?
	return $files_editable;
}


// tri la liste des fichiers en placant ceux a la racine en premier
function sort_directory_first($files,$root) {
  $files_root = array();
  $files_directory = array();
  foreach($files as $file) {
      if (dirname($file)."/" != $root) $files_directory[] = $file;
                                  else $files_root[] = $file;
  }
  return array_merge($files_root,$files_directory);
}

function skeleditor_get_file_content_type_ctrl($fichier){
	if (preg_match(",("._SE_EXTENSIONS_IMG.")$,ims",$fichier)){
		$type = 'img';
		$ctrl = md5(filemtime($fichier));
		$content = null;
	}
	else {
		$type = 'txt';
		lire_fichier($fichier, $content);
		$ctrl = md5($content);
	}
	return array($content,$type,$ctrl);
}
?>