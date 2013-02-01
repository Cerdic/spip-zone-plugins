<?php
/**
 * MediaSPIP player
 * Lecteur multimédia HTML5 pour MediaSPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 * 2010-2012 - Distribué sous licence GNU/GPL
 * 
 * Fichier de définition des différents pipelines
 * 
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline insert_head
 * @param unknown_type $flux
 */
function mediaspip_player_insert_head($flux){
	$flux .= '
<script src="'.generer_url_public('mediaspip_medias_init.js').'" type="text/javascript"></script>
<script src="'.generer_url_public('mediaspip_player_lang.js').'" type="text/javascript"></script>
';
	return $flux;
}

function mediaspip_player_header_prive($flux){
	$flux .= '
<script src="'.generer_url_public('mediaspip_medias_init.js').'" type="text/javascript"></script>
<script src="'.generer_url_public('mediaspip_player_lang.js').'" type="text/javascript"></script>
<link rel="stylesheet" href="'.direction_css(find_in_path('html5_controls.css')).'" type="text/css" media="all" />
';
	return $flux;
}

function mediaspip_player_jqueryui_forcer($plugins){
	$plugins[] = 'jquery.ui.slider';
	return $plugins;
}

function mediaspip_player_jquery_plugins($plugins){
	$plugins[] = _DIR_LIB_MOUSEWHEEL.'jquery.mousewheel.js';
	$plugins[] = 'javascript/flowplayer-3.2.9.min.js';
	$plugins[] = 'javascript/mediaspip_player.js';
	$plugins[] = 'javascript/mediaspip_fallback_flash.js';
	return $plugins;
}

function mediaspip_player_insert_head_css($flux){
	$flux .= '
<link rel="stylesheet" href="'.direction_css(mediaspip_player_timestamp(find_in_path('html5_controls.css'))).'" type="text/css" media="all" />';
	return $flux;
}

function mediaspip_player_timestamp($fichier){
	if ($m = filemtime($fichier))
		return "$fichier?$m";
	return $fichier;
}
?>