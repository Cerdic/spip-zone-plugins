<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_CHECKLINK',(_DIR_PLUGINS.end($p)));

include_spip('base/checklink');

function checklink_taches_generales_cron($taches_generales){
	$taches_generales['checklink_verification'] = 180;
	return $taches_generales;
}
?>