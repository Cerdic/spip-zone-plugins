<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGE_PRIVE',(_DIR_PLUGINS.end($p)));

function habillage_prive_ajouter_onglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['config_habillage_prive']= new Bouton(
	'../'._DIR_PLUGIN_HABILLAGE_PRIVE.'/spip_death.png', 'Configurer habillage Prive', generer_url_ecrire("config_habillage_prive"));
  return $flux;
}

?>
