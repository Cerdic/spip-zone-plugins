<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

function habillages_ajouter_onglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['config_habillages']= new Bouton(
	_DIR_PLUGIN_HABILLAGES.'/img_pack/habillage_prive-22.png', 'Habillages', generer_url_ecrire("config_habillages"));
  return $flux;
}

function habillages_header_prive($flux){

	global $exec;
	
	if (file_exists("img_pack/theme.xml")) {
	
	$theme_file = "img_pack/theme.xml";
	$plugin_directory = _DIR_PLUGIN_HABILLAGES;
	$open_theme_file = fopen($theme_file, 'r');
	$theme_file_size = filesize ($theme_file);
	$read_theme_file = fread ($open_theme_file, $theme_file_size);
	$search_theme_name = eregi("<prefixe>(.*)</prefixe>", $read_theme_file, $theme_name);
	$flux .= '<link rel="stylesheet" href="'.$plugin_directory.'/prive/themes/'.$theme_name[1].'/img_pack/style.css" type="text/css" />'."\n";
	$flux .= '<meta http-equiv="Pragma" content="no-cache">'."\n";
	$flux .= '<meta http-equiv="expires" content="0">'."\n";
	return $flux;	
	}
	else {
		$flux .= '<meta http-equiv="Pragma" content="no-cache">'."\n";
		$flux .= '<meta http-equiv="expires" content="0">'."\n";
		return $flux;
	}
}
?>
