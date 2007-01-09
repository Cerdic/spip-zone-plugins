<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_OPENPUBLISHING',(_DIR_PLUGINS.end($p)));

include_spip('base/op_base');

// variables de personalisation
// veuilliez adapter ces variables à la configuration de votre site
$GLOBALS['op_agenda'] = '2';
$GLOBALS['op_renvoie'] = '/spip.php?page=indy-attente&var_mode=calcul'; 


?>