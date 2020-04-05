<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2012
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

// Pipeline SPIP autoriser

/**
 * Fonction appelée par le pipeline SPIP "autoriser"
 */
  function autoriser_acs_dist($faire, $type, $id, $qui, $opt) {
		// si on est admin SPIP ET admin ACS
		if ( $GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"] && (acs_autorise() || (!isset($GLOBALS['meta']['ACS_ADMINS']))) )
    	return true;
    return false;
  }
?>