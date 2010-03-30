<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Page acs_onload
 */
// appellé depuis acs_options.php dans TOUS les cas
// acs_options est le premier fichier du plugin chargé, avant même autoriser.php de la dist

// Dossier des paramètres et images utilisateur
// User images and parameters
// compatible mutualisation (_DIR_SITE defini)
if (_DIR_SITE != '_DIR_SITE') {
	$dir_site = _DIR_SITE;
	$dir_site_absolu = _DIR_RACINE ? substr(_DIR_SITE, 3) : _DIR_SITE;
}
else {
	$dir_site = _DIR_RACINE;
	$dir_site_absolu = '';
}
// Dossier du cache ACS (par defaut: tmp/cache/acs)
define('_ACS_TMP_DIR', $dir_site._NOM_TEMPORAIRES_INACCESSIBLES.'cache/acs/');

// Modèle ACS par defaut - Default ACS model
$GLOBALS['meta']['acsModel'] = isset($GLOBALS['meta']['acsModel']) ? $GLOBALS['meta']['acsModel'] : 'cat';

// Dossier des images du modèle ACS actif - Active ACS model images directory
$GLOBALS['ACS_CHEMIN'] = $dir_site_absolu._NOM_PERMANENTS_ACCESSIBLES.'_acs/'.$GLOBALS['meta']['acsModel'];

define('_DIR_ACS', _DIR_PLUGINS.acs_get_from_active_plugin('ACS', 'dir').'/'); // Chemin valable espace public aussi, pas comme _DIR_PLUGIN_ACS, qui est à proscrire

// Versions - Lues dans la variable meta que spip a écrit
define('ACS_VERSION', preg_replace('/([^\s]+).*/', '\1', acs_get_from_active_plugin('ACS', 'version')));
define('ACS_RELEASE', preg_replace('/.*\s\((.*)\)/', '\1', acs_get_from_active_plugin('ACS', 'version')));

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
    $GLOBALS['dossier_squelettes'] .= $dir_site_absolu.$dir.':';
  }
}
$GLOBALS['dossier_squelettes'] .= 'plugins/'.acs_get_from_active_plugin('ACS', 'dir').'/'.'models/'.$GLOBALS['meta']['acsModel'];

// dossier des composants :
define('_DIR_COMPOSANTS', find_in_path('composants'));

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

// Vérifie que l'utilisateur connecté est un admin ACS autorisé, ou le(s) webmestre(s) si ACS n'est pas encore configuré 
function acs_autorise() {
	// Si l'utilisateur n'est pas admin, pas la peine d'aller plus loin
	if ($GLOBALS['auteur_session']['statut'] != "0minirezo")
		return false;
	$id_admin = $GLOBALS['auteur_session']['id_auteur'];
  if (isset($GLOBALS['meta']['ACS_ADMINS']) && $GLOBALS['meta']['ACS_ADMINS'] != '')
    return in_array($id_admin, explode(',', $GLOBALS['meta']['ACS_ADMINS']) );
  elseif (defined(_ID_WEBMESTRES)) // Si le plugin Autorité est installé, les webmestres sont autorisés à configurer ACS
    return in_array($id_admin, explode(':', _ID_WEBMESTRES) );
  else  // A défaut, le créateur ET administrateur du site (auteur n°1) est toujours autorisé à configurer ACS
    return ($id_admin == 1);
  return false;
}

/**
 * Makes directory, returns TRUE if exists or made
 *
 * @param string $pathname The directory path.
 * @return boolean returns TRUE if exists or made or FALSE on failure.
 */
function mkdir_recursive($pathname) {
    @is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname));
    return @is_dir($pathname) || @mkdir($pathname);
}

// Utilise par la fonction balise_VAR()
function meta_recursive($meta) {
	if (isset($GLOBALS['meta'][$meta])) {
		$val = $GLOBALS['meta'][$meta];
		if (substr($val, 0 ,1) == '=') {
  		$meta = meta_recursive(substr($val, 1));
  	}
	}
	return $meta;
}
?>