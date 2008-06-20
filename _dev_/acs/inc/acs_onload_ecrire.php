<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * acs_onload_ecrire est appellé au chargement de l'espace ecrire.
 * les fonctions définies ici sont disponibles dans tout l'espace ecrire
 */

// Versions - Lues dans la variable meta que spip a écrit
define('ACS_VERSION', preg_replace('/([^\s]+).*/', '\1', acs_get_from_active_plugin('ACS', 'version')));
define('ACS_RELEASE', preg_replace('/.*\s\((.*)\)/', '\1', acs_get_from_active_plugin('ACS', 'version')));
define('ACS_SPIP_CODE_MIN', 1.9207); // Le $spip_version_code de ecrire/inc_versions.php
define('ACS_SPIP_CODE_MAX', 1.9208); // Le $spip_version_code de ecrire/inc_versions.php

// Pour convertir les $spip_version_code en texte
// et/ou pour afficher la liste des versions de spip testées compatibles ACS
$GLOBALS['acs_table_versions_spip'] = array(
  '1.9207' => '1.9.2c',
  '1.9208' => '1.9.2d'
);

// Contrôle l'accès aux pages de configuration, dont celles déclarées dans $GLOBALS['ACS_ACCES'] (acs_options)
// et celles déclarées dans $GLOBALS['meta']['acsCadenasse'] (inc/acs_adm)
if (_request('exec'))
  acs_acces(_request('exec'));

function acs_acces($page) {
  // les fichiers exec de configuration de spip sont administrés par les mêmes admins qu'ACS
  $enfer = array('acs', 'configuration', 'config_lang', 'admin_tech', 'admin_vider', 'admin_plugin');

  // Les pages définies dans options, administrés par les mêmes admins qu'ACS
  if (isset($GLOBALS['ACS_ACCES']) && is_array($GLOBALS['ACS_ACCES']) && count($GLOBALS['ACS_ACCES']))
    $enfer = array_merge($GLOBALS['ACS_ACCES'], $enfer);

  $GLOBALS['ACS_ENFER'] = $enfer; // On garde cette définition pour affichage

  if (isset($GLOBALS['meta']['ACS_ADMINS'])) { // Pas d'action avant initialisation !
    // Les pages à accès contrôlé par ACS, avec choix des admins
    if (isset($GLOBALS['meta']['acsCadenasse']) && $GLOBALS['meta']['acsCadenasse']) {
      $acsCadenasse = unserialize($GLOBALS['meta']['acsCadenasse']);
      if (is_array($acsCadenasse) && isset($acsCadenasse[$page])) {
        if (!in_array($GLOBALS['auteur_session']['id_auteur'], array_keys($acsCadenasse[$page])))
          acs_exit();
      }
    }
    if (in_array($page, $enfer) && (!acs_autorise($GLOBALS['auteur_session']['id_auteur'])))
      acs_exit();
  }
}

// Vérifie que l'admin connecté est autorisé à accéder à ACS
function acs_autorise($id_admin) {
  if (isset($id_admin) && $id_admin)
    return in_array($id_admin, explode(',', $GLOBALS['meta']['ACS_ADMINS']) );
  else  // A défaut, le créateur ET administrateur du site (auteur n°1) est autorisé à configurer ACS
    return ($id_admin == 1);
  return false;
}

// (ressemble vaguement à exec_configuration_dist() de spip 1.9.2 - http://doc.spip.org/@exec_configuration_dist)
function acs_exit() {
  global $spip_version_code;

  if (!isset($GLOBALS['auteur_session']['statut'])) {
    include_spip('inc/headers');
    redirige_par_entete(generer_url_public('login'));
  }

  include_spip('inc/acs_presentation');
  include_spip('inc/config');

  $GLOBALS['couleur_foncee'] = '#ffaf00';
  $GLOBALS['couleur_claire'] = '#ff0000';
  if($spip_version_code >= 1.93)
    echo '<html><body>'; // commencer_page_dist() buggé avec version 1.9.3svn du 11/04/2008
  else
    echo acs_commencer_page(_T('avis_non_acces_page'), "administration", "interdit");

  echo '<br /><div style="width: 50%">';
  echo '<h2 class="alert">'._T('avis_non_acces_page').'</h2>';
  if (in_array(_request('exec'), $GLOBALS['ACS_ENFER']))
    echo avertissement_config();
  echo '</div>';
  echo fin_gauche(), fin_page();
  exit;
}



// Lit un tableau sérialisé dans une variable meta
// Read serialized array in meta variable
function meta2array($meta) {
  $r = unserialize($GLOBALS['meta'][$meta]);
  if (is_array($r))
    return $r;
  return $GLOBALS['meta'][$meta];
}

?>