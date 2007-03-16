<?php

if (!defined('_DIR_PLUGIN_PHPMV')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PHPMV',(_DIR_PLUGINS.end($p)).'/');
}
if (!isset($GLOBALS['meta']['_DIR_PLUGIN_PHPMV']) OR $GLOBALS['meta']['_DIR_PLUGIN_PHPMV']!=_DIR_PLUGIN_PHPMV){
	include_spip("inc/phpmv_install");
	phpmv_verif_install();
}
define('_PHPMV_DIR_CONFIG',$GLOBALS['meta']['phpmv_dir_config']);
define('_PHPMV_DIR_DATA',$GLOBALS['meta']['phpmv_dir_data']);
define('_DIR_PLUGIN_PHPMV',$GLOBALS['meta']['_DIR_PLUGIN_PHPMV']);

function phpmv_get_code(){
	return '<!-- phpmyvisites -->
			<script src="'.url_de_base().find_in_path('spip_phpmyvisites.js').'" type="text/javascript"></script>
			<noscript>
			<div style="display:none;">
			<img src="'.generer_url_public('phpmyvisites','var_nophpmv=1',false).'" alt="phpMyVisites" class="phpmyvisitestag" />
			</div>
			</noscript>
			<!-- /phpmyvisites -->';	
}

function phpmv_insert_body($texte){
	if (!isset($GLOBALS['meta']['phpmv_flag_insert_body'])){
		include_spip("inc/meta");
		ecrire_meta('phpmv_flag_insert_body','oui');
		ecrire_metas();
	}
	return $texte.phpmv_get_code();
}
function phpmv_insert_head($texte){
	$i_site = 1;
	return $texte .
	  '<script type="text/javascript"><!--
var a_vars = Array();var pagename=\'\';var phpmyvisitesSite = '.$i_site.';var phpmyvisitesURL = "'.($url = generer_url_public('phpmyvisites','var_nophpmv=1',true)).'";
//-->
</script>';
}

?>
