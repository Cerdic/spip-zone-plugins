<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * @file acs_onload.php
 * appellé depuis acs_options.php dans TOUS les cas
 * acs_options est le premier fichier du plugin chargé, avant même autoriser.php de la dist
 */

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

// Racine des dossiers du site :
define('_ACS_DIR_SITE_ROOT', $dir_site);

// Dossier du cache ACS (par defaut: tmp/cache/acs)
define('_ACS_TMP_DIR', _ACS_DIR_SITE_ROOT._NOM_TEMPORAIRES_INACCESSIBLES.'cache/acs/');

// Desactivation du cache de SPIP
if ($GLOBALS['meta']['ACS_CACHE_SPIP_OFF'] == 'on')
  define('_NO_CACHE',-1); // Desactive totalement le cache de SPIP (aucune creation des pages en cache)

// Set ACS par defaut - Default ACS set
if (!isset($GLOBALS['meta']['acsSet']))
	$GLOBALS['meta']['acsSet'] = 'cat';

// Dossier des images du set ACS actif - Active ACS set images directory
$GLOBALS['ACS_CHEMIN'] = $dir_site_absolu._NOM_PERMANENTS_ACCESSIBLES.'_acs/'.$GLOBALS['meta']['acsSet'];

define('_DIR_ACS', _DIR_PLUGINS.acs_get_from_active_plugin('ACS', 'dir').'/'); // Chemin valable espace public aussi, pas comme _DIR_PLUGIN_ACS, qui est à proscrire

// Définition du dossier global des squelettes actifs d'ACS (avec override)
// Global active ACS skeletons directory definition (with override)
$dossiers_squelettes_avant_override = explode(':', $GLOBALS['dossier_squelettes']);
if (isset($GLOBALS['meta']['acsSqueletteOverACS']) && $GLOBALS['meta']['acsSqueletteOverACS']) {
  $tas = explode(':', $GLOBALS['meta']['acsSqueletteOverACS']);
  foreach($tas as $dir) {
    @include(_ACS_DIR_SITE_ROOT.$dir.'/mes_options.php');
    @include(_ACS_DIR_SITE_ROOT.$dir.'/mes_fonctions.php');
    if (in_array($dir_site_absolu.$dir, $dossiers_squelettes_avant_override))
      continue;
    if (isset($GLOBALS['dossier_squelettes']) && $GLOBALS['dossier_squelettes'])
      $GLOBALS['dossier_squelettes'] .= ':';
    $GLOBALS['dossier_squelettes'] .= $dir_site_absolu.$dir;
  }
}
if (isset($GLOBALS['dossier_squelettes']) && $GLOBALS['dossier_squelettes'])
  $GLOBALS['dossier_squelettes'] .= ':';
// On ajoute le chemin du modèle cat actif
$GLOBALS['dossier_squelettes'] .= 'plugins/'.acs_get_from_active_plugin('ACS', 'dir').'/'.'sets/'.$GLOBALS['meta']['acsSet'];

// dossier des composants :
define('_DIR_COMPOSANTS', find_in_path('composants'));

// dispatch public / privé
if ((_DIR_RESTREINT != '') && ($_POST['action'] != 'poster_forum_prive')) {
	// Sauts de ligne 
	$GLOBALS['spip_pipeline']['pre_propre'] .= '|post_autobr';  
}
else {
  require_once _DIR_ACS.'inc/acs_onload_ecrire.php';
}
include_spip('balise/acs_balises');

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
/*
// Vérifie que l'utilisateur connecté est un admin ACS autorisé, ou le(s) webmestre(s) si ACS n'est pas encore configuré
// ou qu'il dispose des droits requis (_acs, _adm, _aut, ou _ide)
function acs_autorise($requested_statut='_acs') {
  // Si l'utilisateur n'est pas identifie, pas la peine d'aller plus loin
  if (!isset($GLOBALS['auteur_session']['statut']))
    return false;
  
  switch($requested_statut) {
    case '_adm':
      $rs = Array('0minirezo');
      break;
    case '_aut':
      $rs = Array('0minirezo', '1redacteur');
      break;
    case '_ide':
      $rs = Array('0minirezo', '1redacteur', '6visiteur');
      break;
  }
  // Si un droit plus faible que "_acs" est demandé, on retourne le resultat
  if (is_array($rs))
    return in_array($GLOBALS['auteur_session']['statut'], $rs);
  
	// Si l'utilisateur n'est pas admin, pas la peine d'aller plus loin
	if ($GLOBALS['auteur_session']['statut'] != "0minirezo")
		return false;

	// Cet admin est-il aussi admin ACS ?
	$id_admin = $GLOBALS['auteur_session']['id_auteur'];
  if (isset($GLOBALS['meta']['ACS_ADMINS']) && $GLOBALS['meta']['ACS_ADMINS'] != '') {
    return in_array($id_admin, explode(',', $GLOBALS['meta']['ACS_ADMINS']) );
  }
  elseif (defined(_ID_WEBMESTRES)) { // Si le plugin Autorité est installé, les webmestres sont autorisés à configurer ACS
    return in_array($id_admin, explode(':', _ID_WEBMESTRES) );
  }
  else  // A défaut, le créateur ET administrateur du site (auteur n°1) est toujours autorisé à configurer ACS
    return ($id_admin == 1);
  return false;
}
*/
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

/**
 * Lit une variable meta à la façon de l'API cfg, suivant un chemin,
 * avec en plus la récursivité ACS (une variable ACS peut référencer une autre variable ACS)
 * Utilisé par la fonction balise_VAR()
 * @param src : tableau source
 * @param meta : nom de la variable meta à lire
 * @return : valeur de la variable (récursive)
 */
function meta_recursive($src, $meta) {
	$chemin = strtok($meta, '/');
	$val = $src[$chemin];
	// Si la valeur commence par "=", c'est une référence récursive à une autre variable ACS
	if (substr($val, 0 ,1) == '=')
		$val = meta_recursive($src, substr($val, 1).substr($meta, strlen($chemin)));
	// On appelle récursivement la fonction tant que $val est un array ou un array serialise
	if (is_array($val)) {
		$val = meta_recursive($val, substr($meta, strlen($chemin)+1));
	}
	elseif (is_array(unserialize($val))) {
		$val = meta_recursive(unserialize($val), substr($meta, strlen($chemin)+1));
	}
	return $val;
}
/**
 * Journalise les actions du plugin ACS si _ACS_LOG est supérieur à 0 dans acs_options.php
 * @param txt: texte à journaliser
 */
function acs_log($txt, $gravite = _LOG_HS) {
	if (_ACS_LOG >_LOG_HS) {
		spip_log($txt, 'acs', _LOG_FILTRE_GRAVITE);
	}
}
/**
 * Retourne un objet ou un tableau sous forme de tableau affichable en html
 * @param r : objet ou tableau
 * @param html : retourne du html si vrai
 * @return : objet ou tableau en mode texte ou html lisible
 */
function dbg($r, $html=false) {
   if (is_object($r) or is_array($r)) {
        ob_start();
        print_r($r);
        $r = ob_get_contents();
        ob_end_clean();
        if ($html)
        	$r = htmlentities($r);
        $srch = array('/Array[\n\r]/', '/\s*[\(\)]+/', '/[\n\r]+/', '/ (?= )/s');
        $repl = array(''             , ''            , "\n"       , ($html ? '&nbsp;' : ' '));
        $r = preg_replace($srch, $repl, $r);
        if ($html)
        	$r = nl2br($r);
    }
    return $r;
}
?>