<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function formulaires_configurer_seo_metas_charger_dist(){

	$config = unserialize($GLOBALS['meta']['seo']);
	$valeurs = isset($config['meta_tags']) ? $config['meta_tags'] : array();
	if(isset($valeurs['tag']) and is_array($valeurs['tag'])){
		foreach($valeurs['tag'] as $tag => $val){
			$valeurs[$tag] = $val;
			unset($valeurs['tag'][$tag]);
		}
	}
	if(isset($valeurs['default']) and is_array($valeurs['default'])){
		foreach($valeurs['default'] as $tag => $val){
			$valeurs['default_'.$tag] = $val;
			unset($valeurs['default'][$tag]);
		}
	}
	unset($valeurs['tag']);
	unset($valeurs['default']);

	$valeurs['editable'] = true;
	
	if (!autoriser('configurer', 'configuration')) {
		$valeurs['editable'] = false;
	}
	
	return $valeurs;
}

function formulaires_configurer_seo_metas_traiter_dist(){
	$config = unserialize($GLOBALS['meta']['seo']);
	if (!isset($config['meta_tags'])) {
		$config['meta_tags'] = array();
	}
	$config['meta_tags']['activate'] = _request('activate','no');
	$config['meta_tags']['activate_editing'] = _request('activate','no');
	
	$config['meta_tags']['tag']['title'] = _request('title');
	$config['meta_tags']['tag']['description'] = _request('description');
	$config['meta_tags']['tag']['keywords'] = _request('keywords');
	$config['meta_tags']['tag']['copyright'] = _request('copyright');
	$config['meta_tags']['tag']['author'] = _request('author');
	$config['meta_tags']['tag']['robots'] = _request('robots');
	
	$config['meta_tags']['default']['title'] = _request('default_title');
	$config['meta_tags']['default']['description'] = _request('default_description');
	$config['meta_tags']['default']['keywords'] = _request('default_keywords');
	$config['meta_tags']['default']['copyright'] = _request('default_copyright');
	$config['meta_tags']['default']['author'] = _request('default_author');
	$config['meta_tags']['default']['robots'] = _request('default_robots');
	
	$config = serialize($config);
	ecrire_meta('seo',$config);
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}
?>
