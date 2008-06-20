<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/acs_groups');

/**
 * Retourne la page d'admin "Pages"
 */

function acs_adm() {
  if (isset($_POST['changer_model']) && ($_POST['changer_model'] == 'oui')) {
    if (
        ($GLOBALS['meta']['acsModel'] != $_POST['acsModel']) ||
        ($GLOBALS['meta']['acsSqueletteOverACS'] != $_POST['acsSqueletteOverACS'])
      ) {
      ecrire_meta('acsModel', $_POST['acsModel']);
      ecrire_meta('acsSqueletteOverACS', $_POST['acsSqueletteOverACS']);
      $GLOBALS['dossier_squelettes'] = (isset($GLOBALS['meta']['acsSqueletteOverACS']) ? $GLOBALS['meta']['acsSqueletteOverACS'].':' : '')._DIR_PLUGIN_ACS.'models/'.$_POST['acsModel'];
      ecrire_metas();
    }
  }
  if (isset($_POST['changer_groupes']) && ($_POST['changer_groupes'] == 'oui')) {
    acs_groups_update($_POST['acsGroups']);
  }
  if (isset($_POST['changer_pages']) && ($_POST['changer_pages'] == 'oui'))
    acs_group_update_pages(acs_grid($_POST['group']), $_POST['pages']);

  $r = acs_box(_T('acs:model').' '._T('acs:acs'),
    acs_model()
    ,
    _DIR_PLUGIN_ACS.'img_pack/composant-24.gif'
  );
/*
echo "<br>_______________________________________________\$_POST<br>\n";
print_r($_POST);

echo "<br>_______________________________________________\$GLOBALS['meta']['acsGroups']<br>\n";
print_r(unserialize($GLOBALS['meta']['acsGroups']));

echo "<br>_______________________________________________\$GLOBALS['meta']['acsCadenasse']<br>\n";
print_r(unserialize($GLOBALS['meta']['acsCadenasse']));
*/
  $editer_acs_admins = charger_fonction('acs_editer_admins', 'inc');
  $groups = acs_groups();
  $blocs_cadenas = '';
  foreach (array_keys($groups) as $grid=>$gr) {
    $ids = implode(',', acs_members($gr));
    $blocs_cadenas .= $editer_acs_admins('acsadmins', $grid + 1, true, false, $ids, _T('acs:admins').' '.$gr, 'acs&onglet=adm','auteur-24.gif');
  }

  $r .= $editer_acs_admins('acsadmins', 0, true, false, 1, _T('acs:admins').' '._T('acs:acs'), 'acs&onglet=adm',_DIR_PLUGIN_ACS.'img_pack/cadenas-24.gif').
      '<br />'.
      acs_box(_T('acs:adm').' '._T('acs:acs'),
      '<form name="acs_config" action="?exec=acs" method="post">'.
        '<input type="hidden" name="onglet" value="adm"><input type="hidden" name="changer_groupes" value="oui">'.
        '<table style="width:100%" cellpadding="2px"><tr><td style="width:90%;" >'.
        ctlInput('acsGroups',
                _T('acs:groupes'),
                '<input type="text" name="acsGroups" value="'.implode(', ', array_keys(acs_groups())).'" class="formc"  style="width:100%" />'
                ).
        '</td><td style="text-align:'.$GLOBALS['spip_lang_right'].';"><input type="submit" name="'._T('bouton_valider').
        '" value="'._T('bouton_valider').'" class="fondo" /></td></tr></table>'.
      '</form>'.
      $blocs_cadenas
      ,
      _DIR_PLUGIN_ACS.'img_pack/cadenas_gris-24.gif'
  );
  return $r;
}

function acs_adm_gauche() {
  return acs_info_box(
    _T('acs:adm'),
    _T('acs:onglet_adm_description').'<br /><br />',
    _T('acs:onglet_adm_help'),
    _T('acs:onglet_adm_info').'<br /><br />',
    _DIR_PLUGIN_ACS."img_pack/cadenas-24.gif",
    false
  );
}

function acs_model() {
  $r = '<form name="acs_config" action="?exec=acs" method="post">'.
        '<input type="hidden" name="onglet" value="adm"><input type="hidden" name="changer_model" value="oui">';
  $r .= '<table width="100%"><tr><td>'.ctlInput('acsModel', _T('acs:model'), select_model());
  $r .= '</td><td>'.ctlInput('acsSqueletteOverACS', _T('acs:squelette'), '<input type="text" name="acsSqueletteOverACS" value="'.$GLOBALS['meta']['acsSqueletteOverACS'].'" class="formc" />').'</td></tr></table><br />';

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
function select_model() {
  $r = '<select name="acsModel" class="formc">';
  foreach(list_models() as $sq)
    $r .= '<option name="'.$sq.'" value="'.$sq.'"'.(($sq == $GLOBALS['meta']['acsModel']) ? ' selected': '').'>'.$sq.'</option>';
  $r .= '</select>';
  return $r;
}

/**
 * Lit la liste des modèles de squelettes
 */
function list_models(){
  $squelettes = array();
  if ($d = @opendir(_DIR_PLUGIN_ACS.'models')) {
    while (false !== ($file = @readdir($d))) {
      if ($file != "." && $file != ".." && is_dir(_DIR_PLUGIN_ACS.'models/'.$file)) {
        $squelettes[] = $file;
      }
    }
    closedir($d);
    sort($squelettes);
    return $squelettes;
  }
  else {
    return 'Impossible d\'ouvrir le dossier de modeles "'._DIR_PLUGIN_ACS.'models"';
  }
}

?>