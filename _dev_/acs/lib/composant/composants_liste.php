<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Lit la liste des composants disponibles - Get available components list
 * Utilise le cache ACS - Use ACS cache
 */
function composants_liste(){
  static $cl=array();

  if (count($cl) > 0)
    return $cl; // Return result if done once

  if (!isset($GLOBALS['meta']['acsModel']))
    return $cl; // Return empty array if ACS not initialized

  require_once _DIR_ACS.'inc/acs_cache.php';
  $cl = cache('lecture_composants_liste', 'a_'.$GLOBALS['meta']['acsModel'].'_cl');
  $cl = $cl[0];
  return $cl;
}

function lecture_composants_liste() {
  // Liste des composants du modèle ACS actif
  $dirc = _DIR_PLUGIN_ACS.'models/'.$GLOBALS['meta']['acsModel'].'/composants';
  $cl = lit_liste_composants($dirc);

  // Si override, il faut ajouter la liste des composants du ou des dossiers d'override
  if (isset($GLOBALS['meta']['acsSqueletteOverACS']) && $GLOBALS['meta']['acsSqueletteOverACS']) {
    $tas = explode(':', $GLOBALS['meta']['acsSqueletteOverACS']);
    foreach($tas as $dir) {
      $dirc = _DIR_RACINE.$dir.'/composants';
      $cl = array_merge($cl, lit_liste_composants($dirc, '#over#'));
    }
  }
  return $cl;
}

// Retourne la liste des composants du dossier $dirc
function lit_liste_composants($dirc, $tag=''){
  $lc = array();

  if (!(@is_dir($dirc) AND @is_readable($dirc) AND $d = @opendir($dirc)))
    return $lc;

  while (($f = readdir($d)) !== false && ($nb<1000)) {
    if ($f[0] != '.' # ignorer . .. .svn etc
    AND $f != 'CVS'
    AND $f != 'remove.txt'
    AND @is_readable($p = $dirc."/$f/ecrire/composant.xml")) {
      if (is_file($p)) $lc[$f] = $tag;
    }
    $nb++;
  }
  return $lc;
}
?>