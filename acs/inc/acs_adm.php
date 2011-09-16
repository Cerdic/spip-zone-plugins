<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/acs_groups');
include_spip('inc/acs_version');

/**
 * Retourne la page d'admin "Pages"
 */

function acs_adm() {
  if (isset($_POST['changer_set']) && ($_POST['changer_set'] == 'oui')) {
    if (
        ($GLOBALS['meta']['acsSet'] != $_POST['acsSet']) ||
        ($GLOBALS['meta']['acsSqueletteOverACS'] != $_POST['acsSqueletteOverACS'])
      ) {
      ecrire_meta('acsSet', $_POST['acsSet']);
      ecrire_meta('acsSqueletteOverACS', $_POST['acsSqueletteOverACS']);
      $GLOBALS['dossier_squelettes'] = (isset($GLOBALS['meta']['acsSqueletteOverACS']) ? $GLOBALS['meta']['acsSqueletteOverACS'].':' : '')._DIR_PLUGIN_ACS.'sets/'.$_POST['acsSet'];
      ecrire_metas();
    }
  }  
  if (isset($_POST['changer_groupes']) && ($_POST['changer_groupes'] == 'oui')) {
    acs_groups_update($_POST['acsGroups']);
  }
  if (isset($_POST['changer_pages']) && ($_POST['changer_pages'] == 'oui'))
    acs_group_update_pages(acs_grid($_POST['group']), $_POST['pages']);

  $r = acs_box(_T('acs:set').' '._T('acs:acs').acs_help_call('set'),
    acs_help_div('set', _T('acs:set_help').'<br /><br />').acs_set()
    ,
    _DIR_PLUGIN_ACS.'images/composant-24.gif'
  );
/*
echo "<br>_______________________________________________\$_POST<br>\n";
print_r($_POST);

echo "<br>_______________________________________________\$GLOBALS['meta']['ACS_GROUPS']<br>\n";
print_r(unserialize($GLOBALS['meta']['ACS_GROUPS']));

echo "<br>_______________________________________________\$GLOBALS['meta']['ACS_CADENASSE']<br>\n";
print_r(unserialize($GLOBALS['meta']['ACS_CADENASSE']));
*/


  // Sauvegarde/restauration
  $acs_sr = charger_fonction('acs_sr', 'inc');
  $res = ajax_action_greffe("acs_sr", 0, $acs_sr());
  $r.= '<br />'.acs_box(_T('acs:save').' / '._T('acs:restore'), $res, _DIR_PLUGIN_ACS.'images/sr.png');  
  
  // Bloc des admins
  $editer_acs_admins = charger_fonction('acs_editer_admins', 'inc');
  $groups = acs_groups();
  $blocs_cadenas = '';
  foreach (array_keys($groups) as $grid=>$gr) {
    $ids = implode(',', acs_members($gr));
    $blocs_cadenas .= $editer_acs_admins('acsadmins', $grid + 1, true, false, $ids, _T('acs:admins').' '.$gr, 'acs&onglet=adm','auteur-24.gif');
  }

  $r .= '<br />'.
      acs_box(_T('acs:adm').acs_help_call('help_acs_admins'),
      acs_help_div('help_acs_admins', _T('acs:admins_help')).
      $editer_acs_admins('acsadmins', 0, true, false, 1, _T('acs:admins').' '._T('acs:acs'), 'acs&onglet=adm',_DIR_PLUGIN_ACS.'images/cadenas-24.gif').
      '<br /><hr /><br /><form name="acs_config" action="?exec=acs" method="post">'.
        '<input type="hidden" name="onglet" value="adm"><input type="hidden" name="changer_groupes" value="oui">'.
        '<table style="width:100%" cellpadding="2px"><tr><td style="width:90%;" >'.
        ctlInput('acsGroups',
                _T('acs:groupes'),
                '<input type="text" name="acsGroups" value="'.implode(', ', array_keys(acs_groups())).'" class="forml"  style="width:100%" />'
                ).
        '</td><td style="text-align:'.$GLOBALS['spip_lang_right'].';"><input type="submit" name="'._T('bouton_valider').
        '" value="'._T('bouton_valider').'" class="fondo" /></td></tr></table>'.
      '</form>'.
      $blocs_cadenas,
      _DIR_PLUGIN_ACS.'images/cadenas-24.gif'
  );
  return $r;
}

function acs_adm_gauche() {
  return acs_info_box(
    _T('acs:adm'),
    _T('acs:onglet_adm_description').'<br /><br />',
    false,
    _T('acs:onglet_adm_info'),
    _DIR_PLUGIN_ACS."images/cadenas-24.gif",
    false
  );
}

function acs_adm_droite() {
  $acs_config = charger_fonction('acs_config', 'inc');
  $r = acs_box(_T('acs:acs'), ajax_action_greffe("acs_config", 0, $acs_config()), _DIR_PLUGIN_ACS."images/acs_32x32.gif");
  return $r;
}

function acs_set() {
  $r = '<form name="acs_set" action="?exec=acs" method="post">'.
        '<input type="hidden" name="onglet" value="adm"><input type="hidden" name="changer_set" value="oui">';
  $r .= '<table width="100%"><tr><td>'.ctlInput('acsSet', _T('acs:set'), select_set());
  $r .= '</td><td>'.ctlInput('acsSqueletteOverACS', _T('acs:squelette'), '<input type="text" name="acsSqueletteOverACS" value="'.$GLOBALS['meta']['acsSqueletteOverACS'].'" class="forml" />').'</td></tr></table>';

  $r .= '<div style="text-align:'.$GLOBALS['spip_lang_right'].';"><input type="submit" name="'._T('bouton_valider').
  '" value="'._T('bouton_valider').'" class="fondo" /></div></form>';
  return $r;
}

function ctlInput($nom, $txt, $content) {
  return '<table width="100%"><tr><td style="width: 10%; text-align:'.$GLOBALS['spip_lang_right'].'"><label for "'.$nom.'" title="'.$nom.'"  class="label">'.$txt.'</label></td><td>'.$content.'</td></tr></table>';
}

/**
 * Retourne un sélecteur de squelette,
 */
function select_set() {
  $r = '<select name="acsSet" class="forml">';
  foreach(list_sets() as $sq)
    $r .= '<option name="'.$sq.'" value="'.$sq.'"'.(($sq == $GLOBALS['meta']['acsSet']) ? ' selected': '').'>'.$sq.'</option>';
  $r .= '</select>';
  return $r;
}

/**
 * Lit la liste des jeux de composants
 */
function list_sets(){
  $squelettes = array();
  if ($d = @opendir(_DIR_PLUGIN_ACS.'sets')) {
    while (false !== ($file = @readdir($d))) {
      if ($file != "." && $file != ".." && substr($file, 0, 1) != '.' && @is_dir(_DIR_PLUGIN_ACS.'sets/'.$file)) {
        $squelettes[] = $file;
      }
    }
    closedir($d);
    sort($squelettes);
    return $squelettes;
  }
  else {
    return 'Impossible d\'ouvrir le jeu de composants "'._DIR_PLUGIN_ACS.'sets"';
  }
}

?>