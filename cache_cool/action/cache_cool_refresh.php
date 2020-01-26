<?php
/*
 * Plugin Cache Cool
 * (c) 2009 Cedric
 * Distribue sous licence GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;


function action_cache_cool_refresh_dist(){
	// lever le flag pour dire que l'on peut utiliser cette action pour refresh
	if (!isset($GLOBALS['meta']['cache_cool_action_refresh']) OR $GLOBALS['meta']['cache_cool_action_refresh']<$_SERVER['REQUEST_TIME']-86400){
		ecrire_meta('cache_cool_action_refresh',$_SERVER['REQUEST_TIME']);
		spip_log("action_cache_cool_refresh : cache_cool_action_refresh mis a ".$_SERVER['REQUEST_TIME'],'cachecool'._LOG_DEBUG);
	}
	if (defined('_DIR_PLUGIN_MEMOIZATION')
		AND $id = _request('id')){
		if (!function_exists('cache_get'))
			include_spip('inc/memoization');
		#spip_log("action_cache_cool_refresh : lecture sur cachecool-$id ("._CACHE_NAMESPACE.") : ".count(cache_get("cachecool-$id")),'cachecool'._LOG_DEBUG);
		if ($pile = cache_get("cachecool-$id")
	    AND is_array($pile)){
			spip_log("action_cache_cool_refresh : cache_cool_process $id",'cachecool'._LOG_DEBUG);
			$GLOBALS['cache_cool_queue'] = $pile;
			cache_del("cachecool-$id");
			cache_cool_process(true);
		}
	}
}