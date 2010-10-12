<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/acs_groups');
include_spip('inc/acs_version');

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
  if (isset($_POST['changer_config']) && ($_POST['changer_config'] == 'oui')) {
    ecrire_meta('ACS_VOIR_PAGES_COMPOSANTS', $_POST['ACS_VOIR_PAGES_COMPOSANTS']);
    ecrire_meta('ACS_VOIR_PAGES_PREVIEW', $_POST['ACS_VOIR_PAGES_PREVIEW']);
    ecrire_meta('ACS_VOIR_ONGLET_VARS', $_POST['ACS_VOIR_ONGLET_VARS']);
    ecrire_meta('ACS_PREVIEW_BACKGROUND', $_POST['ACS_PREVIEW_BACKGROUND']);
    ecrire_metas();
  }
  
  $r = acs_box(_T('acs:model').' '._T('acs:acs'),
    acs_model()
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

  $r .= $editer_acs_admins('acsadmins', 0, true, false, 1, _T('acs:admins').' '._T('acs:acs'), 'acs&onglet=adm',_DIR_PLUGIN_ACS.'images/cadenas-24.gif').
      '<br />'.
      acs_box(_T('acs:adm').' '._T('acs:acs'),
      '<form name="acs_config" action="?exec=acs" method="post">'.
        '<input type="hidden" name="onglet" value="adm"><input type="hidden" name="changer_groupes" value="oui">'.
        '<table style="width:100%" cellpadding="2px"><tr><td style="width:90%;" >'.
        ctlInput('acsGroups',
                _T('acs:groupes'),
                '<input type="text" name="acsGroups" value="'.implode(', ', array_keys(acs_groups())).'" class="forml"  style="width:100%" />'
                ).
        '</td><td style="text-align:'.$GLOBALS['spip_lang_right'].';"><input type="submit" name="'._T('bouton_valider').
        '" value="'._T('bouton_valider').'" class="fondo" /></td></tr></table>'.
      '</form>'.
      $blocs_cadenas
      ,
      _DIR_PLUGIN_ACS.'images/cadenas_gris-24.gif'
  );
  return $r;
}

function acs_adm_gauche() {
  return acs_info_box(
    _T('acs:adm'),
    _T('acs:onglet_adm_description').'<br /><br />',
    _T('acs:onglet_adm_help'),
    _T('acs:onglet_adm_info').'<br /><br />',
    _DIR_PLUGIN_ACS."images/cadenas-24.gif",
    false
  );
}

function acs_adm_droite() {
  return acs_box(
_T('acs:acs'),
'<form name="acs_config" action="?exec=acs&onglet=adm" method="post">
<input type="hidden" name="changer_config" value="oui">
<input name="ACS_VOIR_ONGLET_VARS" type="checkbox"'.
($GLOBALS['meta']['ACS_VOIR_ONGLET_VARS'] ? ' checked' : '').' />'.
_T('acs:voir_onglet_vars').
'<br /><input name="ACS_VOIR_PAGES_COMPOSANTS" type="checkbox"'.
($GLOBALS['meta']['ACS_VOIR_PAGES_COMPOSANTS'] ? ' checked' : '').' />'.
_T('acs:voir_pages_composants').
'<br />
<input name="ACS_VOIR_PAGES_PREVIEW" type="checkbox"'.
($GLOBALS['meta']['ACS_VOIR_PAGES_PREVIEW'] ? ' checked' : '').' />'
._T('acs:voir_pages_preview_composants').'
<br />
<table><tr><td><input name="ACS_PREVIEW_BACKGROUND" type="text" class="palette forml" style="width:60px;" value="'.$GLOBALS['meta']['ACS_PREVIEW_BACKGROUND'].'" /></td><td>'._T('acs:preview_background').'</td></tr></table>
<br />
<br />
<input type="submit" name="'._T('bouton_valider').'" value="'._T('bouton_valider').'" class="fondo" /></form><br />'.
_T('acs:acsDerniereModif').' '.date("Y-m-d H:i:s", lire_meta("acsDerniereModif")).
'<hr /><br />'.
_T('version').' <a style="color: black">ACS '.acs_version().'</a> '.(acs_release() ? '('.acs_release().')' : '').
'<br /><br />'.
_T('acs:documentation').': <a href="http://acs.geomaticien.org" target="_new"><img src="'._DIR_PLUGIN_ACS.'images/acs_32x32_help.gif" alt="?" style="vertical-align: middle"/></a>', _DIR_PLUGIN_ACS."images/acs_32x32.gif");
}

function acs_model() {
  $r = '<form name="acs_model" action="?exec=acs" method="post">'.
        '<input type="hidden" name="onglet" value="adm"><input type="hidden" name="changer_model" value="oui">';
  $r .= '<table width="100%"><tr><td>'.ctlInput('acsModel', _T('acs:model'), select_model());
  $r .= '</td><td>'.ctlInput('acsSqueletteOverACS', _T('acs:squelette'), '<input type="text" name="acsSqueletteOverACS" value="'.$GLOBALS['meta']['acsSqueletteOverACS'].'" class="forml" />').'</td></tr></table><br />';

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
  $r = '<select name="acsModel" class="forml">';
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
      if ($file != "." && $file != ".." && substr($file, 0, 1) != '.' && @is_dir(_DIR_PLUGIN_ACS.'models/'.$file)) {
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