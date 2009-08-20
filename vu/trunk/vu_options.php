<?php

/* Definir une bonne fois pour touts les repertoires par defaut */
	// le repertoire parent : semble poser probleme... et apres tout quelle utilite ?
		//	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		//	define('_DIR_VU',(_DIR_PLUGINS.end($p)));

	// le repertoire des images
	define("_DIR_VU_IMG_PACK", _DIR_PLUGIN_VU."img_pack/");
	// le repertoire prive
	define("_DIR_VU_PRIVE", _DIR_PLUGIN_VU."prive/");



?>
