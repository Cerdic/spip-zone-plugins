<?php

/**
 * Copyright (c) 2009 Christian Paulus
 * Dual licensed under the MIT and GPL licenses.
 * */


// exau_options.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// pour SPIP 1.9.1
if(!defined('_DIR_PLUGIN_EXAU')) {
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_EXAU',(_DIR_PLUGINS.end($p)).'/');
}

// permettre l'export des statuts :
//define("EXAU_PERMET_STATUTS", "0minirezo,1comite,6forum");
define("EXAU_PERMET_STATUTS", "6forum");

?>