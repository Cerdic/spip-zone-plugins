<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Retourne les pages du squelette qui utilisent le composant $c
 * Fonction appellée en mode Ajax et mise en cache/acs
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Requis à partir de spip 2.1
if (!is_callable('ajax_retour'))
	include_spip('inc/actions');

include_spip('inc/acs_cache');
include_spip('inc/composant/composant_infos');

function exec_composant_get_infos() {
  $c = _request('c');
  $nic = _request('nic');
  $var_mode = _request('var_mode');

  $err = '<span class="alert" title="'._T('acs:err_cache').'">*</span>';

  $r = cache('composant_infos', 'c_'.$GLOBALS['meta']['acsSet'].'_'.$c.$nic.'_infos', array('c' => $c,'nic'=>$nic), ($var_mode == 'recalcul'));
  if(!is_array($r) || count($r) < 2)
    ajax_retour($err);
  if ($r[1] != 'err')
    $err = '';
  ajax_retour($r[0].$err);
}

?>