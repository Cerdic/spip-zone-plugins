<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Lit la liste des composants disponibles - Get available components list
 * Utilise le cache ACS - Use ACS cache
 * 
 * Retourne un tableau avec les noms de dossiers des composants en index
 * Return Array('component1' => '', 'component2' => '<dir>', ...)
 */
function composants_liste(){
  static $cl=array();

  if (count($cl) > 0)
    return $cl; // Return result if done once

  require_once _DIR_ACS.'inc/acs_cache.php';
  $model = (isset($GLOBALS['meta']['acsModel']) ? $GLOBALS['meta']['acsModel'] : 'cat');  
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
      $dirc = _ACS_DIR_SITE_ROOT.$dir.'/composants';
      $cl = array_merge($cl, lit_liste_composants($dirc, $dir));
    }
  }
  ksort($cl);
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
      if (is_file($p)) {
      	$lc[$f]['on'] = $GLOBALS['meta']['acs'.ucfirst($f).'Use'];
       	if ($tag)
       		$lc[$f]['over'] = $tag;
      }
    }
    $nb++;
  }
  return $lc;
}

/**
 * Retourne les instances d'un composant
 */
function composant_instances($c) {
  static $ci = array();

  if (count($ci[$c]) > 0)
    return $ci[$c];
    
  $ci[$c] = array();
  $metas = $GLOBALS['meta'];
  $reg = '/acs'.ucfirst($c).'(\d+)Use/';
  foreach ($metas as $meta=>$val) {
    if (preg_match($reg, $meta, $matches))
      $ci[$c][] = $matches[1];
  }
  sort($ci[$c]);
  return $ci[$c];
}
?>