<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acs_version');

// Initialisation des variables ACS, à l'installation et lors d'une restauration
function acs_load_vars($from) {
  if (is_readable($from))
    include $from;
  else 
    return 'unable to read '.$from;

  if (is_array($def)) {
  	// ACS_VERSION est la version d'acsInstalled lors de la sauvegarde : NE PAS LA RESTAURER !
  	// todo : gérer les version mismatches
  	$dversion = $def['ACS_VERSION'];
  	unset($def['ACS_VERSION']);
  	unset($def['ACS_RELEASE']);

    foreach($def as $var=>$value) {
    	if (is_array($value))
    		serialize($value);
    	// on n'écrase pas les valeurs existantes lors d'une installation / reinstallation du plugin
	    if (!isset($GLOBALS['meta'][$var]))
	    	ecrire_meta($var, $value);
    }
    ecrire_metas();
    lire_metas();
    if ($dversion == acs_version())
    	return 'ok';
    else
    	return 'version mismatch (def '.$dversion.' vs ACS '.acs_version().')';
  }
  else
  	return 'no vars';
}

function acs_reset_vars() {
  spip_query("delete FROM spip_meta where left(nom,3)='acs'");
  lire_metas();
  acs_log('ACS init/restore : variables DELETED');
}
?>