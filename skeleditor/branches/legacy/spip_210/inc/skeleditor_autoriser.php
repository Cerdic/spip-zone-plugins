<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function skeleditor_autoriser(){}

/**
 * Par defaut seuls les webmestres peuvent editer les squelettes
 * mais il suffit de personaliser ce droit pour modifier ce reglage
 *
 */
function autoriser_skeleditor_dist($faire, $type, $id, $qui, $opt){
	include_spip('inc/skeleditor');
  return (skeleditor_path_editable() AND autoriser('webmestre','','',$qui));
}
function autoriser_skeleditor21_dist($faire, $type, $id, $qui, $opt){
	include_spip('inc/skeleditor');
  return (skeleditor_path_editable() AND autoriser('webmestre','','',$qui));
}

// autorisation des boutons
function autoriser_skeleditor_ajout_bouton_dist($faire, $type, $file, $qui, $opt) {

  return (autoriser('skeleditor','','',$qui));
}


function autoriser_squelette_voir_dist($faire, $type, $file, $qui, $opt) {
	if (!preg_match(",[.]("._SE_EXTENSIONS.")$,ims",$file)
		OR strpos(substr($file,strlen(_DIR_RACINE)),'../')!==FALSE
		OR substr($file,0,1)=='/')
		return false;
  if (!autoriser('skeleditor','','',$qui))
		return false;

	$dirs = creer_chemin();
	foreach($dirs as $d){
		if (strncmp($d,$file,strlen($d))==0)
			return true;
	}

  return false;
}

/**
 * Autoriser a supprimer un squelette
 *
 */
function autoriser_squelette_supprimer_dist($faire, $type, $file, $qui, $opt) {

  return (autoriser('modifier','squelette',$file,$qui));
}

/**
 * Autoriser a upload un squelette
 *
 */
function autoriser_squelette_upload_dist($faire, $type, $path, $qui, $opt) {
  return (autoriser('creerdans','squelette',$path,$qui));
}

/**
 * Autoriser a download un squelette
 *
 */
function autoriser_squelette_download_dist($faire, $type, $file, $qui, $opt) {
  return (autoriser('modifier','squelette',$file,$qui));
}

/**
 * Autoriser a editer un squelette
 *
 */
function autoriser_squelette_modifier_dist($faire, $type, $file, $qui, $opt) {
	include_spip('inc/skeleditor');
	$files_editable = skeleditor_files_editables();
  return (autoriser('skeleditor','','',$qui) AND in_array($file,$files_editable));
}

/**
 * Autoriser a creer un squelette
 *
 */
function autoriser_squelette_creer_dist($faire, $type, $file, $qui, $opt) {

  return (autoriser('skeleditor','','',$qui)
		AND (preg_match(",[.]("._SE_EXTENSIONS.")$,i",$file)) // extension autorisee
		AND autoriser('creerdans','squelette',dirname($file))); // dans un chemin autorise
}

/**
 * Autoriser a creer dans un dossier squelette
 *
 */
function autoriser_squelette_creerdans_dist($faire, $type, $path, $qui, $opt) {
	include_spip('inc/skeleditor');
	$path = rtrim($path,'/');
	$path_editable = skeleditor_path_editable();
	$files_editable = skeleditor_files_editables($path_editable);
	
  return (autoriser('skeleditor','','',$qui)
					AND ($path==rtrim($path_editable,'/') OR in_array($path,array_map('dirname',$files_editable))));
}


// security
function check_file_allowed($file,$files_editable,$new = false) {
	if (in_array($file,$files_editable))	return true;  // known file
	 else if ($new){ // new file ?
		 if (in_array(dirname($file),array_map('dirname',$files_editable)))	return true; // known directory
	}
	return false;
}

?>