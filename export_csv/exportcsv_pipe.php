<?php
/*##############################################################
 * ExportCSV
 * Export des articles / rubriques SPIP en fichiers CSV.
 *
 * Auteur :
 * Stéphanie De Nadaï 
 * webdesigneuse.net
 * © 2008 - Distribué sous licence GNU/GPL
 *
##############################################################*/

if (!defined('_DIR_PLUGIN_EXPORTCSV')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_EXPORTCSV',(_DIR_PLUGINS.end($p))."/");
}
// répertoire images
if (!defined("_DIR_IMG_EXPORTCSV")) {
	define('_DIR_IMG_EXPORTCSV', _DIR_PLUGIN_EXPORTCSV.'img_pack/');
}
// prefixe du plugin
if (!defined("_PLUGIN_NAME_EXPORTCSV")) {
	define('_PLUGIN_NAME_EXPORTCSV', 'exportcsv');
}

function exportcsv_ajouter_boutons($boutons_admin) {
	// si on est admin ou admin-restreint
	if ($GLOBALS['connect_statut'] == "0minirezo" 
	AND $GLOBALS["options"]=="avancees" AND 
	(!isset($GLOBALS['meta']["activer_exportcsv"]) OR $GLOBALS['meta']["activer_exportcsv"]!="non")) {

	  // on voit le bouton dans la barre "naviguer" (édition)
		$boutons_admin['naviguer']->sousmenu["exportcsv_tous"]= new Bouton(
		_DIR_IMG_EXPORTCSV."exportcsv-24.png",  // icone
		_T("exportcsv:extract_data") //titre
		);
	}
	return $boutons_admin;
}
function exportcsv_affiche_gauche($flux){
	if (_request('exec') == 'articles' OR _request('exec') == 'controle_petition') {
		if ($GLOBALS['connect_statut'] == "0minirezo" 
		AND $GLOBALS["options"] == "avancees" AND 
		(!isset($GLOBALS['meta']["activer_exportcsv"]) OR $GLOBALS['meta']["activer_exportcsv"]!="non")) {
			include_spip('inc/exportcsv_petition');
			$flux['data'] .= exportcsv_afficher_petition($flux['args']['id_article']);
		}
	}
	return $flux;
}
// css privé
function exportcsv_header_prive($flux) {
	$flux .= "\n".'<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_EXPORTCSV.'exportcsv_styles.css" />'."\n";
	return $flux;
}
?>