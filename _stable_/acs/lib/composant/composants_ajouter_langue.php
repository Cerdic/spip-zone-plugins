<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

require_once _DIR_ACS.'lib/composant/composants_actifs.php';

/**
 * Inclut les fichiers de langue des composants actifs
 * Include components lang files
 */
function composants_ajouter_langue() {
  if (!is_array(composants_actifs())) return false;

  $idx = $GLOBALS['idx_lang'];
  $idx_tmp = $idx.'_tmp';
  $GLOBALS['idx_lang'] = $idx_tmp;

  foreach (composants_actifs() as $c) {
    if ($c == '') continue;

    $langfile = find_in_path('composants/'."$c/lang/$c".'_'.$GLOBALS['spip_lang'].'.php');
    if (!$langfile)
      $langfile = find_in_path('composants/'."$c/lang/$c".'_fr.php');
    if (!$langfile)
      continue;
    @include($langfile);
    if (is_array($GLOBALS[$idx_tmp])) {
      $cla = array();
      foreach($GLOBALS[$idx_tmp] as $k => $v) {
        $cla[$c.'_'.$k] = $v;
      }
      $GLOBALS[$idx] = array_merge($GLOBALS[$idx], $cla);
    }
    unset($GLOBALS[$idx_tmp]);
  }
  $GLOBALS['idx_lang'] = $idx;
}
?>
