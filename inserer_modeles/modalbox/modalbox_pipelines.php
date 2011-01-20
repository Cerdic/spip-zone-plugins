<?php

function inserer_modeles_modalbox_insert_head($flux){
	if (!defined('_DIR_PLUGIN_MEDIABOX') && !defined('_DIR_PLUGIN_GESTDOC') && !defined('_DIR_PLUGIN_MEDIAS')) {
		// Insertion des librairies js
		$flux .='<script src="'.find_in_path('modalbox/jquery.simplemodal-1.3.3.js').'" type="text/javascript"></script>';
		$flux .='<script src="'.find_in_path('modalbox/modalbox.js').'" type="text/javascript"></script>';
		$css = generer_url_public('modalbox/style_prive_plugin_modalbox');
		$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	}

	return $flux;
}

?>
