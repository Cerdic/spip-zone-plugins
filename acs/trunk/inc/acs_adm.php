<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/acs_groups');
include_spip('inc/acs_version');

/**
 * Retourne la page d'admin "Pages"
 */

function acs_adm() {
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

function acs_set() {
  $r = '<form name="acs_set" action="?exec=acs" method="post">'.
        '<input type="hidden" name="onglet" value="adm"><input type="hidden" name="changer_set" value="oui">';
  $r .= '<table width="100%"><tr><td>'.ctlInput('acsSet', _T('acs:set'), select_set());
  $r .= '</td><td>'.ctlInput('acsSqueletteOverACS', _T('ecrire:icone_squelette'), '<input type="text" name="acsSqueletteOverACS" value="'.$GLOBALS['meta']['acsSqueletteOverACS'].'" class="forml" />').'</td></tr></table>';

  $r .= '<div style="text-align:'.$GLOBALS['spip_lang_right'].';"><input type="submit" name="'._T('bouton_valider').
  '" value="'._T('bouton_valider').'" class="fondo" /></div></form>';
  return $r;
}

function ctlInput($nom, $txt, $content) {
  return '<table width="100%"><tr><td style="width: 10%; text-align:'.$GLOBALS['spip_lang_right'].'"><label for "'.$nom.'" title="'.$nom.'"  class="label">'.$txt.'</label></td><td>'.$content.'</td></tr></table>';
}



?>