<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * acs_onload_ecrire est appellé au chargement de l'espace ecrire.
 * les fonctions définies ici sont disponibles dans tout l'espace ecrire
 */

// Contrôle l'accès aux pages de configuration, dont celles déclarées dans $GLOBALS['ACS_ACCES'] (acs_options)
// et celles déclarées dans $GLOBALS['meta']['acsCadenasse'] (inc/acs_adm)
if (_request('exec'))
  acs_acces(_request('exec'));

  /**
   * Contrôle l'accès à la page $page
   * Les pages à contrôler sont déclarées dans $GLOBALS['ACS_ACCES'] (acs_options)
   * et dans $GLOBALS['meta']['acsCadenasse'] (inc/acs_adm)
   *
   * @param string $page
   */
function acs_acces($page) {
  // les fichiers exec de configuration de spip sont administrés par les mêmes admins qu'ACS
  $enfer = array('acs',
  	'acs_editer_admins',
  	'acs_selectionner_admin',
  	'acs_rechercher_admin',
    'acs_page_get_infos',
  	'acs_page_source',
    'composant_get_infos',
    'composant_get_trad',
    'configuration',
    'config_lang',
    'admin_tech',
    'admin_vider',
    'admin_plugin');
  
  // Les pages définies dans options, administrés par les mêmes admins qu'ACS
  if (isset($GLOBALS['ACS_ACCES']) && is_array($GLOBALS['ACS_ACCES']) && count($GLOBALS['ACS_ACCES']))
    $enfer = array_merge($GLOBALS['ACS_ACCES'], $enfer);
spip_log('acs_acces '.implode(' ', $enfer));
  $GLOBALS['ACS_ENFER'] = $enfer; // On garde cette définition pour affichage

  if (isset($GLOBALS['meta']['ACS_ADMINS'])) { // Pas d'action avant initialisation d'ACS !
    // Les pages à accès contrôlé par ACS, avec choix des admins
    if (isset($GLOBALS['meta']['acsCadenasse']) && $GLOBALS['meta']['acsCadenasse']) {
      $acsCadenasse = unserialize($GLOBALS['meta']['acsCadenasse']);
      if (is_array($acsCadenasse) && isset($acsCadenasse[$page])) {
        if (!in_array($GLOBALS['auteur_session']['id_auteur'], array_keys($acsCadenasse[$page])))
          acs_exit();
      }
    }
    if (in_array($page, $enfer) && (!acs_autorise()))
      acs_exit();
  }
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