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
  return (autoriser('webmestre','','',$qui));
}

// autorisation des boutons
function autoriser_skeleditor_ajout_bouton_dist($faire, $type, $id, $qui, $opt) {

  return (autoriser('skeleditor','','',$qui));
}

/**
 * Autoriser a supprimer un squelette
 *
 */
function autoriser_squelette_supprimer_dist($faire, $type, $id, $qui, $opt) {

  return (autoriser('modifier','squelette',$id,$qui));
}

/**
 * Autoriser a download un squelette
 *
 */
function autoriser_squelette_download_dist($faire, $type, $id, $qui, $opt) {
  return (autoriser('modifier','squelette',$id,$qui));
}

/**
 * Autoriser a editer un squelette
 *
 */
function autoriser_squelette_modifier_dist($faire, $type, $id, $qui, $opt) {
	$files_editable = skeleditor_files_editables();
  return (autoriser('skeleditor','','',$qui) AND in_array($file,$files_editable));
}

/**
 * Autoriser a creer un squelette
 *
 */
function autoriser_squelette_creer_dist($faire, $type, $id, $qui, $opt) {

  return (autoriser('skeleditor','','',$qui)
					AND autoriser('creerdans','squelette',dirname($id)));
}

/**
 * Autoriser a creer dans un dossier squelette
 *
 */
function autoriser_squelette_creerdans_dist($faire, $type, $id, $qui, $opt) {
	$files_editable = skeleditor_files_editables();

  return (autoriser('skeleditor','','',$qui)
					AND in_array($id,array_map('dirname',$files_editable)));
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