<?php

if (!isset($GLOBALS['meta']['_PHPMV_DIR_CONFIG']) || !strlen($GLOBALS['meta']['_PHPMV_DIR_CONFIG'])){
	include_spip("inc/meta");
	ecrire_meta('_PHPMV_DIR_CONFIG',realpath(_DIR_SESSIONS . "phpmvconfig"));
	ecrire_meta('_PHPMV_DIR_DATA',realpath(_DIR_SESSIONS . "phpmvdatas"));
	ecrire_metas();
}
if (!defined(_DIR_PLUGIN_PHPMV)){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PHPMV',_DIR_PLUGINS.end($p));
}
if (!isset($GLOBALS['meta']['_DIR_PLUGIN_PHPMV']) OR $GLOBALS['meta']['_DIR_PLUGIN_PHPMV']!=_DIR_PLUGIN_PHPMV){
	include_spip("inc/meta");
	ecrire_meta('_DIR_PLUGIN_PHPMV',_DIR_PLUGIN_PHPMV);
	ecrire_metas();
}

function phpmv_get_code(){
	$i_site = 1;
	return '<!-- phpmyvisites -->
			<div style="display:none;">
			<script type="text/javascript">
			<!--
			var a_vars = Array();
			var pagename=\'\';
			
			var phpmyvisitesSite = '.$i_site.';
			var phpmyvisitesURL = "'.($url = generer_url_public('phpmyvisites','var_nophpmv=1',true)).'";
			//-->
			</script>
			<script src="'.url_de_base().find_in_path('spip_phpmyvisites.js').'" type="text/javascript"></script>
			<noscript>
			<img src="'.generer_url_public('phpmyvisites','var_nophpmv=1',false).'" alt="phpMyVisites" class="phpmyvisitestag" />
			</noscript>
			</div>
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

?>