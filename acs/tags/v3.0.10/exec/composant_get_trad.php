<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Retourne une traduction du composant $c
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

// Requis à partir de spip 2.1
if (!is_callable('ajax_retour'))
	include_spip('inc/actions');


function exec_composant_get_trad() {
  $c = _request('c');
  $trcmp = _request('trcmp');
  $cadre = _request('cadre');

  include_spip('inc/composant/composant_traduction');
  $r .=  composant_traduction($c, $trcmp, $cadre);

  ajax_retour($r);
}

?>