<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt


/**
 * Sauvegarde / restaure la config d'ACS
 */
function action_acs_config() {
  // renvoie "acs_config : Accès interdit" en cas de tentative d'accès direct
  $securiser_action = charger_fonction('securiser_action', 'inc');
  $securiser_action();

  acs_log('action_acs_config'.dbg($_POST));
  if (isset($_POST['changer_config']) && ($_POST['changer_config'] == 'oui')) {
    ecrire_meta('ACS_VOIR_PAGES_COMPOSANTS', $_POST['ACS_VOIR_PAGES_COMPOSANTS']);
    ecrire_meta('ACS_VOIR_PAGES_PREVIEW', $_POST['ACS_VOIR_PAGES_PREVIEW']);
    ecrire_meta('ACS_VOIR_ONGLET_VARS', $_POST['ACS_VOIR_ONGLET_VARS']);
    ecrire_meta('ACS_PREVIEW_BACKGROUND', $_POST['ACS_PREVIEW_BACKGROUND']);
    ecrire_meta('ACS_SPIP_ADMIN_FORM_STYLE', $_POST['ACS_SPIP_ADMIN_FORM_STYLE']);
    ecrire_meta('ACS_CACHE_SPIP_OFF', $_POST['ACS_CACHE_SPIP_OFF']);
    ecrire_meta("acsDerniereModif", time());
    ecrire_metas();
  }
}
?>