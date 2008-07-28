<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Page acs_onload
 */
// appellé depuis acs_options.php dans TOUS les cas
// acs_options est le premier fichier du plugin chargé, avant même autoriser.php de la dist

$GLOBALS['meta']['acsModel'] = (isset($GLOBALS['meta']['acsModel']) ? $GLOBALS['meta']['acsModel'] : 'cat');
$GLOBALS['ACS_CHEMIN'] = $GLOBALS['ACS_CHEMIN'].'/'.$GLOBALS['meta']['acsModel'];

define('_DIR_ACS', _DIR_PLUGINS.acs_get_from_active_plugin('ACS', 'dir').'/'); // Chemin valable espace public aussi, pas comme _DIR_PLUGIN_ACS, qui est à proscrire

// Versions - Lues dans la variable meta que spip a écrit
define('ACS_VERSION', preg_replace('/([^\s]+).*/', '\1', acs_get_from_active_plugin('ACS', 'version')));
define('ACS_RELEASE', preg_replace('/.*\s\((.*)\)/', '\1', acs_get_from_active_plugin('ACS', 'version')));
define('ACS_SPIP_CODE_MIN', 1.9207); // Le $spip_version_code de ecrire/inc_versions.php
define('ACS_SPIP_CODE_MAX', 1.9208); // Le $spip_version_code de ecrire/inc_versions.php

// Définition du dossier global des squelettes actifs d'ACS (avec override)
// Global active ACS skeletons directory definition (with override)
if (isset($GLOBALS['meta']['acsSqueletteOverACS']) && $GLOBALS['meta']['acsSqueletteOverACS']) {
  $tas = explode(':', $GLOBALS['meta']['acsSqueletteOverACS']);
  foreach($tas as $dir) {
    $gbs = $GLOBALS['dossier_squelettes'];
    @include(_DIR_RACINE.$dir.'/mes_options.php');
    @include(_DIR_RACINE.$dir.'/mes_fonctions.php');
    if ($GLOBALS['dossier_squelettes'] != $gbs) {
      $GLOBALS['dossier_squelettes'] .= ':';
    }
    $GLOBALS['dossier_squelettes'] .= $dir.':';
  }
}
$GLOBALS['dossier_squelettes'] .= 'plugins/'.acs_get_from_active_plugin('ACS', 'dir').'/'.'models/'.$GLOBALS['meta']['acsModel'];

// dispatch public / privé
if ((_DIR_RESTREINT != '') && ($_POST['action'] != 'poster_forum_prive')) {
  include_spip('balise/acs_balises');
}
else {
  require_once _DIR_ACS.'inc/acs_onload_ecrire.php';
}

// Retourne une variable d'un plugin actif
function acs_get_from_active_plugin($plugin, $variable = false) {
  $meta_plugin = unserialize($GLOBALS['meta']['plugin']);
  $plugin = $meta_plugin[$plugin];

  if (!$variable)
    return isset($plugin['dir']);

  if (isset($plugin[$variable]))
    return $plugin[$variable];

  return false;
}
?>