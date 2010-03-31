<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

define('_SPIP_DIST_DIR', _DIR_RACINE.'dist');

/**
 * Retourne un tableau des pages squelettes du site (Utilise le cache ACS)
 *
 * Spip va chercher ses squelettes dans l'ordre suivant :
 * over > modèle ACS actif > plugins actifs > spip (dist)
 */
function pages_liste() {
  static $liste;

  if (!count($liste)) {
    include_spip('inc/acs_cache');
    $model = (isset($GLOBALS['meta']['acsModel']) ? $GLOBALS['meta']['acsModel'] : 'cat');
    $liste =  cache('pages_du_site', 'a_'.$model.'_pages_liste');
  }
  return $liste[0];
}


function pages_du_site() {
  $pages = array();

  // Ordre d'override : over 1 > ...> over n > modèle ACS actif > plugins actifs > spip (dist)

  $tas = explode(':', $GLOBALS['meta']['acsSqueletteOverACS']);
  $dir_site = (_DIR_SITE != '_DIR_SITE') ? _DIR_SITE : _DIR_RACINE;
  foreach($tas as $dir) {
    $squelettes['over'.$numover] = $dir_site.$dir;
    $numover += 1;
  }
  // Squelettes du modele ACS actif:
  $squelettes['acs'] = _DIR_PLUGIN_ACS.'models/'.(isset($GLOBALS['meta']['acsModel']) ? $GLOBALS['meta']['acsModel'] : 'cat');
  // On ajoute les squelettes de plugins actifs - Add skeletons from active plugins
  $plugins = unserialize($GLOBALS['meta']['plugin']);
  foreach ($plugins as $NAME=>$plugin) {
    $squelettes['plugin_'.$NAME] = _DIR_PLUGINS.$plugin['dir'];
  }
  $squelettes['spip'] = _SPIP_DIST_DIR;

  foreach($squelettes as $source => $dir) {
    foreach(pages_du_squelette($dir) as $dossier => $pdd) {
      if (!isset($pages[$dossier])) $pages[$dossier] = array();
      foreach($pdd as $page=>$param) {
        if (!isset($pages[$dossier][$page]))
          $pages[$dossier][$page] = array('source' => $source);
      }
    }
  }
  return $pages;
}

function pages_du_squelette($dir) {
  $pages = array();

  $dossiers = array('', 'modeles', 'formulaires');
  
  if ($GLOBALS['meta']['acsVoirPagesComposants']) {
    $dossiers_composants = array_keys(composants_liste());
    foreach($dossiers_composants as $k => $v)
      $dossiers_composants[$k] = 'composants/'.$v;
    sort($dossiers_composants);
    $dossiers = array_merge($dossiers, $dossiers_composants);
  }
  foreach($dossiers as $dossier) {
    $pages[$dossier] = array();
    $pdd = pages_du_dossier($dir, $dossier);
    foreach($pdd as $page=>$param) {
      $pages[$dossier][$page] = true;
    }
  }
  return $pages;
}

function pages_du_dossier($dir, $dossier) {
  $pages = array();
  $dir .= ($dossier ? '/'.$dossier : '');

  if (@is_dir($dir) AND @is_readable($dir) AND $d = @opendir($dir)) {
    while (($f = readdir($d)) !== false && ($nbfiles<1000)) {
      if ($f[0] != '.' # ignorer . .. .svn etc
      AND $f != 'CVS'
      AND $f != 'remove.txt'
      AND @is_readable($p = "$dir/$f")) {
        if (is_file($p)) {
          if (preg_match(";.*[.]html$;iS", $f)) {
            $pagename = substr($f, 0, -5);
            if (($pagename == 'wrap') ||
            		($pagename == 'acs.js') ||
            		($pagename == 'acs_style_prive.css') ||
            		((substr($pagename, -8) == '_preview') && (!$GLOBALS['meta']['acsVoirPagesPreviewComposants'])) )
            	continue;
            $pages[$pagename] = true;
          }
        }
      }
      $nbfiles++;
    }
  }
  ksort($pages);
  return $pages;
}

?>