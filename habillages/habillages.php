<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

function habillages_ajouter_onglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['config_habillages']= new Bouton(
	'habillage_prive-22.png', 'Habillages', generer_url_ecrire("config_habillages"));
  return $flux;
}

function habillages_header_prive($flux){

	global $exec;
	
	$options_file = "mes_options.php";
	$plugin_directory = _DIR_PLUGIN_HABILLAGES;
	$open_options_file = fopen($options_file, 'r');
	$options_file_size = filesize ($options_file);
	$read_options_file = fread ($open_options_file, $options_file_size);
	$search_template_name = eregi("$plugin_directory/prive/themes/(.*)/img_pack/", $read_options_file, $template_name);
	$flux .= '<link rel="stylesheet" href="'.$plugin_directory.'/prive/themes/'.$template_name[1].'/img_pack/style.css" />'."\n";
	
	return $flux;
}
?>
