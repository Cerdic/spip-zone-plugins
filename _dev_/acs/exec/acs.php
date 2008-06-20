<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acs_presentation');
include_spip('inc/config');
include_spip('inc/meta');

function exec_acs() {
  global $connect_statut, $connect_toutes_rubriques, $options, $spip_lang_left, $spip_lang_right,$changer_config, $spip_display;

  if (isset($GLOBALS['meta']['ACS_ADMINS']) && (!acs_autorise($GLOBALS['auteur_session']['id_auteur'])))
    acs_exit();

  //pipeline('exec_init',array('args'=>array('exec'=>'acs'),'data'=>'')); // non utilisé

  define('_DIR_COMPOSANTS', find_in_path('composants'));

  // Modifications
  $changer_config = $_POST['changer_config'];
  if ($changer_config=='oui') {
    ecrire_meta("acsDerniereModif", time());
    ecrire_metas();
    lire_metas();
  }

  if (_request('onglet')) $onglet = _request('onglet');
  else  $onglet = 'pages';

  $cIconDef = _DIR_PLUGIN_ACS."/img_pack/composant-24.gif";
  // Affichage
  // (spip-hack: le debut_page doit se trouver avant la création d'un objet composant pour disposer des bons include spip⁾)
  echo acs_commencer_page(_T('acs:assistant_configuration_squelettes'), "configuration", "acs");

  echo "<br />";
  echo acs_gros_titre(_T('acs:assistant_configuration_squelettes'));

	echo debut_onglet();
	echo onglet(_T('acs:pages'), generer_url_ecrire('acs', 'onglet=pages'), $onglet, 'pages', _DIR_PLUGIN_ACS."/img_pack/pages-24.gif");
	echo onglet(_T('acs:composants'), generer_url_ecrire('acs', 'onglet=composants'), $onglet, 'composants', $cIconDef);
	echo onglet(_T('acs:adm'), generer_url_ecrire('acs', 'onglet=adm'), $onglet, 'adm', 'cadenas-24.gif');
	echo fin_onglet();

  switch($onglet) {
    case 'pages':
      include_spip('inc/acs_pages');
      $col1 = acs_pages_gauche();
      $col2 = acs_pages();
      $col3 = acs_pages_droite();
      break;

    case 'page':
      include_spip('inc/acs_page');
      $col1 = acs_page_gauche(_request('pg'));
      $col2 = acs_page(_request('pg'));
      $col3 = acs_page_droite(_request('pg'));
      break;

    case 'adm':
      include_spip('inc/acs_adm');
      $col1 = acs_adm_gauche();
      $col2 = acs_adm();
      break;

    case 'composants':
      include_spip('lib/composant/composant_select');
      include_spip('inc/acs_widgets');
      include _DIR_PLUGIN_ACS.'lib/composant/classComposantPrive.php';

      $configfile = find_in_path('composants/config.php');
      @include $configfile;
      if (!is_array($choixComposants))
        break;

      // Insère les scripts de choix - Insert javascripts for choices
      echo '<script type="text/javascript" src="'._DIR_PLUGIN_ACS.'js/picker.js"></script>';

      // Crée l'objet composant - Create current component object
      $cc = _request('composant') ? _request('composant') : 'fond';
      $$c = new AdminComposant($cc, $debug = false);

      // Crée l'interface d'administration du composant
      if(isset($$c->optionnel)) {
        $acsCU = $$c->fullname.'Use';
        $enable = true;
        // Désactive le composant s'il dépend de plugins non activés
        if (strpos($$c->optionnel, 'plugin') === 0) {
          $plugins_requis = explode(' ', substr($$c->optionnel, 7));
          foreach ($plugins_requis as $plug) {
            $plug = strtoupper(trim($plug));
            if (!acs_get_from_active_plugin($plug)) {
              $enable = false;
              break;
            }
          }
        }
        // Désactive le composant si "optionnel" est égal à une variable de configuration non égale à "oui" (si optionnel ne vaut pas oui ou true, il s'agit d'un nom de variable de configuration)
        elseif (($$c->optionnel != 'oui') && ($$c->optionnel != 'true') && ($GLOBALS['meta'][$$c->optionnel] != 'oui')) {
          ecrire_meta($acsCU,'non');
          $enable = false;
        }

        if ($_POST['changer_config']=='oui' && ($_POST[$acsCU] != $GLOBALS['meta'][$acsCU]))
          ecrire_meta($acsCU, ($_POST[$acsCU] ? $_POST[$acsCU] : 'non'));
          $o = '<div align="'.$GLOBALS['spip_lang_right'].'" style ="font-weight: normal"><label>'._T('acs:use').' '._T($cc).' : </label>'.$$c->editswitch($enable).'</div>';
      }
      else $o = "";

      $cIcon = $$c->icon;
      if (!is_readable($cIcon)) $cIcon = $cIconDef;

      $col1 = acs_box(_TC($cc), $$c->gauche(), $cIcon, false, '<img src="'._DIR_PLUGIN_ACS.'/img_pack/info.png" />');

      $col2 = acs_box(select_composant($choixComposants, $cc, $onglet),
        '<form name="acs" action="?exec=acs" method="post">'.
        "<input type='hidden' name='changer_config' value='oui' />".
        "<input type='hidden' name='onglet' value='$onglet' />".
        "<input type='hidden' name='composant' value='$cc' />".
        $o.
        $$c->edit().
        "</form>",
        $cIconDef
      );
      $col2 .= '<br /><a name="cTrad"></a><div id="cTrad"></div>'; // Container for translations - Ajax

      if (count($$c->widgets) > 0)       // Containers
        $col3 =  acs_box(_T('composants'), liste_widgets($$c->widgets), $cIcon, 'acs_box_composants');
      if ($_POST['changer_config']=='oui')
        ecrire_metas(); // écrit les metas changés, obl. après composants

      break;
  }
  $si_premiere_fois = isset($GLOBALS['meta']['ACS_ADMINS']) ? '' : avertissement_config();
  echo acs_3colonnes($col1, $si_premiere_fois.$col2, $col3);
  echo fin_page();
}

// Retourne la traduction d'un terme défini par un composant crée AVANT
// l'appel de cette fonction
function _TC($texte) {
  if (in_array($texte, array_keys($GLOBALS[$GLOBALS['idx_lang']])))
    return $GLOBALS[$GLOBALS['idx_lang']][$texte];
  else
    return $texte;
}

?>
