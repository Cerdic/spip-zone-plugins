<?php
define('_DIR_PLUGIN_VERIFCORE',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

function verifcore_ajouterOnglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['verifcore']= new Bouton(
											  "../"._DIR_PLUGIN_VERIFCORE."/img/verifcore-24.gif", 'Verifmisajour',
											  generer_url_ecrire("config_verifcore"));
  return $flux;
}

?>