<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Construction de la boite d'information d'un plugin
function plugins_boite_infos() {
	$informer = chercher_filtre('info_plugin');
	$infos = $informer('plugonet', 'tout');
	$infos['description'] = propre($infos['description']);
	return pipeline ('boite_infos', array('data' => '', 'args' => array('type'=>'plugin', 
																		'infos' => $infos)));
}

?>
