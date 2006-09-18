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
	
	include_spip('inc/meta');
  	$theme_link = lire_meta('habillage_prive');

  	$flux .= '<link rel="stylesheet" href="'.$theme_link.'style.css" type="text/css" />'."\n";
	$flux .= '<meta http-equiv="Pragma" content="no-cache">'."\n";
	$flux .= '<meta http-equiv="expires" content="0">'."\n";
	return $flux;	

}
?>
