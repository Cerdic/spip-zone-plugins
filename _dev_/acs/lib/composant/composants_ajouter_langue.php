<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt
    require_once _DIR_ACS.'lib/composant/composants_actifs.php';
    require_once _DIR_ACS.'lib/composant/composants_liste.php';
/**
 * Inclut les fichiers de langue des composants actifs
 * Include components lang files
 *
 * Ordre de recherche:
 * 1. langue courante depuis dossier(s) squelettes_over_acs
 * 2. langue courante depuis dossier modèle acs actif
 * 3. langue par défaut depuis dossier(s) squelettes_over_acs
 * 4. langue par défaut depuis dossier modèle acs actif
 */
function composants_ajouter_langue($module='') {
  if ($module=='') {
    require_once _DIR_ACS.'lib/composant/composants_actifs.php';
    $composants = composants_actifs();
  }
  else {
    require_once _DIR_ACS.'lib/composant/composants_liste.php';
    $composants = array_keys(composants_liste());
  }
  if (!is_array($composants)) return false;

  $idx = $GLOBALS['idx_lang'];
  $idx_tmp = $idx.'_tmp';
  $GLOBALS['idx_lang'] = $idx_tmp;

  foreach ($composants as $c) {
    if ($c == '') continue;

    $langfile = find_in_path("composants/$c/".($module ? $module.'/' : '')."lang/$c".'_'.($module ? $module.'_' : '').$GLOBALS['spip_lang'].'.php');
    if (!$langfile)
      $langfile = find_in_path("composants/$c/".($module ? $module.'/' : '')."lang/$c".'_'.($module ? $module.'_' : '').'fr.php');
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
