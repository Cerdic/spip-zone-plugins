<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Retourne une traduction du composant $c
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_composant_get_trad() {
  $c = _request('c');
  $trcmp = _request('trcmp');
  $module = _request('module');

  include_spip('lib/composant/composant_traduction');
  $r .=  composant_traduction($c, $trcmp, $module);

  ajax_retour($r);
}

?>