<?php
if (isset($GLOBALS['multiplug_autorise'])
    AND is_dir(_DIR_RACINE.$GLOBALS['mutualisation_dir'].'/'.$GLOBALS['site'].'/'.'plugins/')
    AND in_array($GLOBALS['site'], $GLOBALS['multiplug_autorise']))
  define('_DIR_PLUGINS_FORK', _DIR_RACINE.$GLOBALS['mutualisation_dir'].'/'.$GLOBALS['site'].'/'.'plugins/');
  
//define('_DIR_PLUGINS_SUPPL', _DIR_RACINE.'sites/'.'mamutu1.mon-site.tld/'.'plugins/'.':'._DIR_RACINE.'sites/'.'mamutu1.mon-site.tld/'.'core/');

?>