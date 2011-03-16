<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acs_presentation');
include_spip('inc/config');
include_spip('inc/meta');

function exec_acs() {
  global $connect_statut, $connect_toutes_rubriques, $options, $spip_lang_left, $spip_lang_right,$changer_config, $spip_display;

  if (isset($GLOBALS['meta']['ACS_ADMINS']) && (!acs_autorise()))
    acs_exit();

  // Modifications
  $changer_config = $_POST['changer_config'];
  if ($changer_config=='oui') {
    ecrire_meta("acsDerniereModif", time());
    ecrire_metas();
    lire_metas();
  }

  if (_request('onglet')) $onglet = _request('onglet');
  else  $onglet = 'pages';

  // Affichage
  // (spip-hack: le debut_page doit se trouver avant la création d'un objet composant pour disposer des bons include spip⁾)
  echo acs_commencer_page(_T('acs:assistant_configuration_squelettes'), "configuration", "acs");

  echo "<br />";
  echo acs_gros_titre(_T('acs:assistant_configuration_squelettes'));

  switch($onglet) {
    case 'pages':
      include_spip('inc/acs_pages');
      if (_request('pg'))
        $pg = _request('pg');
      else
        $pg = 'sommaire';
      $col1 = acs_pages_gauche($pg);
      $col2 = acs_pages($pg);
      $col3 = acs_pages_droite($pg);
      break;

    case 'vars':
      include_spip('inc/acs_vars');
      $col1 = acs_vars_gauche();
      $col2 = acs_vars();
      break;
      
    case 'adm':
      include_spip('inc/acs_adm');
      $col1 = acs_adm_gauche();
      $col2 = acs_adm();
      $col3 = acs_adm_droite();
      break;
      
    case 'composants':
      include_spip('inc/acs_composants');
      include_spip('inc/composant/classComposantPrive');
      include_spip('inc/acs_widgets');

      // Crée l'objet composant - Create current component object
      $cc = _request('composant') ? _request('composant') : 'fond';
      $$c = new AdminComposant($cc, _request('nic'), $debug = false);

      // Crée l'interface d'administration du composant
      $col1 = composants_gauche($$c);
      $col2 = composants($$c);
      $col3 = composants_droite($$c);
      break;
  }
  $si_premiere_fois = isset($GLOBALS['meta']['ACS_ADMINS']) ? '' : avertissement_config();
	echo debut_onglet();
	echo onglet(_T('acs:pages'), generer_url_ecrire('acs', 'onglet=pages'), $onglet, 'pages', _DIR_PLUGIN_ACS."/images/pages-24.gif");
	echo onglet(_T('acs:composants'), generer_url_ecrire('acs', 'onglet=composants'), $onglet, 'composants', _DIR_PLUGIN_ACS."/images/composant-24.gif");
	if (isset($GLOBALS['meta']['ACS_VOIR_ONGLET_VARS']) && ($GLOBALS['meta']['ACS_VOIR_ONGLET_VARS'] == 'on')) 
	  echo onglet(_T('acs:variables'), generer_url_ecrire('acs', 'onglet=vars'), $onglet, 'vars', _DIR_PLUGIN_ACS."/images/vars-24.gif");	
	echo onglet(_T('acs:adm'), generer_url_ecrire('acs', 'onglet=adm'), $onglet, 'adm', 'cadenas-24.gif');
	echo fin_onglet();
  echo acs_3colonnes($col1, $si_premiere_fois.$col2, $col3);
  echo fin_page();
}
?>
