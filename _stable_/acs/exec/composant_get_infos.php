<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Retourne les pages du squelette qui utilisent le composant $c
 * Fonction appellée en mode Ajax et mise en cache/acs
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acs_cache');
include_spip('lib/composant/composant_infos');

function exec_composant_get_infos() {
  $c = _request('c');
  $err = '<span class="alert" title="err_read_cache">*</span>';

  $r = cache('composant_infos', 'c_'.$GLOBALS['meta']['acsModel'].'_'.$c.'_infos');
  if(!is_array($r) || count($r) < 2)
    ajax_retour($err);
  if ($r[1] != 'err')
    $err = '';
  ajax_retour($r[0].$err);
}

?>