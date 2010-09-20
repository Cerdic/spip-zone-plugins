<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

// Initialisation des variables ACS, à l'installation et lors d'une restauration
function inc_acs_load_vars($from) {
  if (is_readable($from))
    include $from;
  else 
    return 'unable to read '.$from;
    dbg($from);
  if (is_array($def)) {
  	// acsVersion est la version d'acsInstalled lors de la sauvegarde : NE PAS LA RESTAURER !
  	// todo : gérer les version mismatches
  	$dversion = $def['acsVersion'];
  	unset($def['acsVersion']);

    foreach($def as $var=>$value) {
    	if (is_array($value))
    		serialize($value);
	    ecrire_meta($var, $value);
    }
    ecrire_metas();
    lire_metas();
    if ($dversion == ACS_VERSION)
    	return 'ok';
    else
    	return 'version mismatch (def '.$dversion.' vs ACS '.ACS_VERSION.')';
  }
  else
  	return 'no vars';
}
?>