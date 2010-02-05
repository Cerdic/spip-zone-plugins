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


// variante repliee de la fonction de l'affichage de l'arbre des repertoires
// http://doc.spip.org/@tree_open_close_dir
function skeleditor_tree_open_close_dir(&$current,$target,$current_file){
	if ($current == $target) return "";
	$tcur = explode("/",$current);
	$ttarg = explode("/",$target);
	$tcom = array();
	$output = "";
	// la partie commune
	while (reset($tcur)==reset($ttarg)){
		$tcom[] = array_shift($tcur);
		array_shift($ttarg);
	}
	// fermer les repertoires courant jusqu'au point de fork
	while($close = array_pop($tcur)){
		$output .= fin_block();
	}
	$chemin = implode("/",$tcom)."/";
	// ouvrir les repertoires jusqu'a la cible
	while($open = array_shift($ttarg)){
		$chemin .= $open . "/";
		$closed = ((strncmp($current_file, ltrim($chemin,'/'), strlen(ltrim($chemin,'/')))==0)?"":" closed");

		$output .= bouton_block_depliable("<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/se-folder-16.png' alt='directory'/> $open",!$closed,md5($chemin));
		$output .= "<div class='dir$closed' id='".md5($chemin)."'>\n";
	}
	$current = $target;
	return $output;
}

function skeleditor_cree_chemin($path_base,$file){
	$chemin = $path_base;
	$sous = explode('/',$file);
	$filename = array_pop($sous); // inutilise ici

	$chemin_ok = "";
	while($chemin AND count($sous) AND $s = array_shift($sous)){
		$chemin_ok = $chemin;
		$chemin = sous_repertoire($chemin, $s);
	}

	return array($chemin, $chemin?'':"$chemin_ok/$s");
}

?>