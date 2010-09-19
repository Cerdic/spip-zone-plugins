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
    @include $from;
  else 
    return 'unable to read '.$from;
  if (is_array($def)) {
  	$dversion = $def['acsVersion'];
  	$drelease = $def['acsRelease'];
  	unset($def['acsVersion']);
  	unset($def['acsRelease']);
  	
    foreach($def as $var=>$value) {
	    ecrire_meta($var, $value);
    }
    ecrire_metas();
    lire_metas();
    if ($dversion == ACS_VERSION)
    	return 'ok';
    else
    	return 'version mismatch ('.$dversion.')';
  }
  else
  	return 'no vars';
}
?>