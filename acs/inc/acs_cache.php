<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * - Cache le résultat de fonctions qui prennent beaucoup de temps
 * La lecture du cache est parfois des centaines de fois plus rapide.
 * Le recalcul se fera après tout changement valide dans ACS,
 * ou lorsque l'option $force_recalcul vaut true.
 *
 * - Cache result of time-expensive functions. Reading cache is sometimes hundreds
 * time faster. Cache is refreshed after all valid change in ACS configuration,
 * or when $force_recalcul option is true.
 */
function cache($fonction, $file, $args=null, $force_recalcul=false) {

  $tmpDir =  _DIR_RACINE._NOM_TEMPORAIRES_INACCESSIBLES;
  $cacheDir = $tmpDir.'cache/acs/';
  $cachefile = $cacheDir.$file;
  $date = $GLOBALS['meta']['acsDerniereModif'];

  if (is_readable($cachefile && !$force_recalcul)) {
    $cache = file_get_contents($cachefile);
    $r = unserialize($cache);
    if ($date == $r['date']) {
      return array($r['content'], 'read', $r['date']);
    }
  }

  if (!is_callable($fonction))
    return array(_T('err_not_callable').' : '.$fonction, 'err', $date);
  if (isset($args) && (!is_array($args)))
    return array(_T('err_args_not_in_array').' : '.$fonction, 'err', $date);

  $r = call_user_func_array($fonction, $args);
  $cachestring = serialize(array(
    'date' => $date,
    'content' => $r
  ));
  if (@file_put_contents($cachefile, $cachestring))
    return array($r, 'write', $date);
  // Si $cacheDir n'est pas accessible en écriture, on crée acs et on ré-éssaie
  if (is_writable($tmpDir.'cache/')) {
    @mkdir($tmpDir.'cache/acs');
    if (@file_put_contents($cachefile, $cachestring))
      return array($r, 'writedir', $date);
  }
  return array($r, 'err', $date);
}

?>