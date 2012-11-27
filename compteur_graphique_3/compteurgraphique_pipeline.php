<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_COMPTEURGRAPHIQUE',(_DIR_PLUGINS.end($p)));

function compteurgraphique_declarer_tables_interfaces($interface){
		$interface['table_des_tables']['compteurgraphique']='compteurgraphique';
	return $interface;
}
?>