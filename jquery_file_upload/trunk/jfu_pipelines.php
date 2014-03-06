<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline jqueryui_forcer (plugin jQueryUI)
 * 
 * On ajoute le chargement des js nécessaires
 * @param array $plugins Un tableau des scripts déjà demandé au chargement
 * @retune array $plugins Le tableau complété avec les scripts que l'on souhaite 
 */
function jfu_jqueryui_plugins($plugins){
	if(!defined('_DIR_PLUGIN_BOOTSTRAP'))
		$plugins[] = "jquery.ui.button";
	$plugins[] = "jquery.ui.progressbar";
	return $plugins;
}

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * Ajout de scripts javascripts dans le head
 *
 * @param array $plugins Un tableau des scripts déjà demandés
 * @return array $plugins Le tableau modifié avec les scripts que l'on souhaite 
 */
function jfu_jquery_plugins($plugins){
	$plugins[] = "javascript/tmpl.min.js";
	$plugins[] = "javascript/load-image.min.js";
	$plugins[] = "javascript/jfu_spip_fonctions.js";
	$plugins[] = "javascript/jquery_file_upload/js/jquery.iframe-transport.js";
	$plugins[] = "javascript/jquery_file_upload/js/jquery.fileupload.js";
	$plugins[] = "javascript/jquery_file_upload/js/jquery.fileupload-ui.js";
	return $plugins;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * On ajoute les js compilé
 * 
 * @param $flux string
 * 		Le contenu du insert_head modifié
 * @return $flux string
 * 		Le contenu du insert_head modifié
 */
function jfu_insert_head($flux){
	$flux .= '
<script src="'.produire_fond_statique('jfu_locales.js').'" type="text/javascript"></script>
';
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * On ajoute les js compilé
 * 
 * @param $flux string
 * 		Le contenu du insert_head modifié
 * @return $flux string
 * 		Le contenu du insert_head modifié
 */
function jfu_header_prive($flux){
	$flux .= '
<script src="'.produire_fond_statique('jfu_locales.js').'" type="text/javascript"></script>
';
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head_css (SPIP)
 * Ajoute des styles par défaut des plugins que l'on utilise
 * 
 * @param string $flux le contenu textuel du pipeline
 * @return string $flux le contenu textuel modifié du pipeline
 */
function jfu_insert_head_css($flux){
	$flux .= '
<link rel="stylesheet" href="'.direction_css(find_in_path('css/jquery.fileupload-ui.css')).'" type="text/css" media="all" />
<link rel="stylesheet" href="'.direction_css(find_in_path('css/jquery.fileupload-ui_spip.css')).'" type="text/css" media="all" />
';
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head_css (SPIP)
 * Ajoute des styles par défaut des plugins que l'on utilise
 * 
 * @param string $flux le contenu textuel du pipeline
 * @return string $flux le contenu textuel modifié du pipeline
 */
function jfu_header_prive_css($flux){
	$flux .= '
<link rel="stylesheet" href="'.direction_css(find_in_path('css/jquery.fileupload-ui.css')).'" type="text/css" media="all" />
<link rel="stylesheet" href="'.direction_css(find_in_path('css/jquery.fileupload-ui_spip.css')).'" type="text/css" media="all" />
';
	return $flux;
}
?>