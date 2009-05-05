<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt

function acs_install($action){
  switch ($action)
  {
    case 'test':
      return isset($GLOBALS['meta']['acsInstalled']);
      break;

    case 'install':
      acs_set_default_values();
      break;

    case 'uninstall':
      acs_reset_vars();
      break;
  }
}


function acs_set_default_values() {
  $defaults = find_in_path('composants/def.php');
  if (is_readable($defaults))
    include $defaults;
  else 
    spip_log('ACS init failed : unable to read composants/def.php');  
  if (is_array($def)) {
    foreach($def as $var=>$value) {
      if (!isset($GLOBALS['meta'][$var]))
	      ecrire_meta($var, $value);
    }
    ecrire_meta('acsInstalled', 'oui');
    ecrire_metas();
    spip_log('ACS init done with default values from composants/def.php');
  }
}

function acs_reset_vars() {
  spip_query("delete FROM spip_meta where left(nom,3)='acs'");
  lire_metas();
  spip_log('ACS variables DELETED');
  
}
?>