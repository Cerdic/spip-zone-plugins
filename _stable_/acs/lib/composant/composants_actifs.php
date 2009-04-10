<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Lit la liste des composants actifs - Get active components list
 * Utilise le cache ACS - Use ACS cache
 */
function composants_actifs() {
  static $composants_actifs = array();

  if (!(count($composants_actifs) > 0)) {
    require_once _DIR_ACS.'inc/acs_cache.php';
    $composants_actifs = cache('lecture_composants_actifs', 'a_'.$GLOBALS['meta']['acsModel'].'_ca');
    $composants_actifs = $composants_actifs[0];
  }
  return $composants_actifs;
}

// Un composant actif est un composant qui possède une variable meta acsMonComposantUse égale à oui
function lecture_composants_actifs() {
  $ca = array();
  $unused = array();
  require_once _DIR_ACS.'lib/composant/composants_variables.php';

  foreach(composants_variables() as $c) {
    if (($c['type'] == 'use') && ($GLOBALS['meta']['acs'.ucfirst($c['composant']).'Use'] != 'oui'))
      $unused[$c['composant']] = true;
    $ca[$c['composant']] = 1;
  }
  $ca = array_diff_key($ca, $unused);
  return array_keys($ca);
}
?>
