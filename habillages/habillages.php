<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

function habillages_ajouter_onglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['config_habillages']= new Bouton(
	'../'._DIR_PLUGIN_HABILLAGES.'/img_pack/habillage_prive-22.png', 'Habillages', generer_url_ecrire("config_habillages"));
  return $flux;
}

?>
