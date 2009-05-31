<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt

// Choix de couleur
function ctlColor($composant, $nic, $nom, $couleur, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  return '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr><td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'" class="label">'._TC($composant, $nom).'</label></td><td><input type="text" class="palette" id="'.$var.'" name="'.$var.'_'.$wid.'" size="8" maxlength="8" value="'.$couleur.'"></td></tr></table></div>';
}

// Choix d'image
function ctlImg($composant, $nic, $nom, $image, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  $path = $GLOBALS['ACS_CHEMIN'].'/'.$param['chemin'];
  mkdir_recursive(_DIR_RACINE.$path);
  $s = @getimagesize('../'.$path.'/'.$image);
  $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
  if ($param['label'] != 'non')
    $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'"  class="label">'._TC($composant, $nom).'</label></td>';
  $r .= '<td><input type="text" name="'.$var.'_'.$wid.'"'.(is_array($s) ? ' title="'.$s[0].'x'.$s[1].'"' : '').' value="'.$image.'" size="40" class="forml"></td>';
  $r .= '<td><a href="javascript:TFP.popup(document.forms[\'acs\'].elements[\''.$var.'_'.$wid.'\'], document.forms[\'acs\'].elements[\''.$var.'_'.$wid.'\'].value, \''.$path.'\', \''._DIR_RACINE.'\');" title="'._T('acs:choix_image').'"><img src="'._DIR_ACS.'images/folder_image.png" class="icon" alt="'._T('acs:choix_image').'" /></a></td></tr></table></div>';
  return $r;
}

// Choix d'une largeur de bordure
function ctlLargeurBord($composant, $nic, $nom, $largeur='0', $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
  if ($param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'" class="label">'._TC($composant, $nom).'</label></td>';
  $r .= '<td><select name="'.$var.'_'.$wid.'" title="'._T('acs:bordlargeur').'" class="forml" style="width: auto">'.
    '<option value=""'.($largeur=="" ? ' selected' : '').'></option>'.
    '<option value="0"'.($largeur=="0" ? ' selected' : '').'>0</option>'.
    '<option value="thin"'.($largeur=="thin" ? ' selected' : '').'>thin</option>'.
    '<option value="1px"'.($largeur=="1px" ? ' selected' : '').'>1px</option>'.
    '<option value="2px"'.($largeur=="2px" ? ' selected' : '').'>2px</option>'.
    '<option value="3px"'.($largeur=="3px" ? ' selected' : '').'>3px</option>'.
    '<option value="4px"'.($largeur=="4px" ? ' selected' : '').'>4px</option>'.
    '<option value="5px"'.($largeur=="5px" ? ' selected' : '').'>5px</option>'.
    '</select></td></tr></table></div>';
  return $r;
}

// Choix d'un style  de bordure
function ctlStyleBord($composant, $nic, $nom, $style='solid', $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
  if ($param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'" class="label">'._TC($composant, $nom).'</label></td>';
  $r .= '<td><select name="'.$var.'_'.$wid.'" title="'._T('acs:bordstyle').'" class="forml" style="width: auto">'.
    '<option value=""'.($style=="" ? ' selected' : '').' title="'._T('acs:parent').'"></option>'.
    '<option value="none"'.($style=="none" ? ' selected' : '').' title="'._T('acs:none').'">none</option>'.
    '<option value="solid"'.($style=="solid" ? ' selected' : '').' title="'._T('acs:solid').'">solid</option>'.
    '<option value="dashed"'.($style=="dashed" ? ' selected' : '').' title="'._T('acs:dashed').'">dashed</option>'.
    '<option value="dotted"'.($style=="dotted" ? ' selected' : '').' title="'._T('acs:dotted').'">dotted</option>'.
    '<option value="double"'.($style=="double" ? ' selected' : '').' title="'._T('acs:double').'">double</option>'.
    '<option value="groove"'.($style=="groove" ? ' selected' : '').' title="'._T('acs:groove').'">groove</option>'.
    '<option value="ridge"'.($style=="ridge" ? ' selected' : '').' title="'._T('acs:ridge').'">ridge</option>'.
    '<option value="inset"'.($style=="inset" ? ' selected' : '').' title="'._T('acs:inset').'">inset</option>'.
    '<option value="outset"'.($style=="outset" ? ' selected' : '').' title="'._T('acs:outset').'">outset</option>'.
    '</select></td></tr></table></div>';
  return $r;
}

// Choix d'une famille de fonte
function ctlFontFamily($composant, $nic, $nom, $style='sans-serif', $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
  if ($param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'" class="label">'._TC($composant, $nom).'</label></td>';
  $r .= '<td><select name="'.$var.'_'.$wid.'" title="'._T('acs:bordstyle').'" class="forml" style="width: auto">'.
    '<option value="serif"'.($style=="serif" ? ' selected' : '').' title="'._T('acs:serif').'">serif</option>'.
    '<option value="sans-serif"'.($style=="sans-serif" ? ' selected' : '').' title="'._T('acs:sans-serif').'">sans-serif</option>'.
    '<option value="cursive"'.($style=="cursive" ? ' selected' : '').' title="'._T('acs:cursive').'">cursive</option>'.
    '<option value="fantasy"'.($style=="fantasy" ? ' selected' : '').' title="'._T('acs:fantasy').'">fantasy</option>'.
    '<option value="monotype"'.($style=="monotype" ? ' selected' : '').' title="'._T('acs:monotype').'">monotype</option>'.
    '</select></td></tr></table></div>';
  return $r;
}

// Choix de valeur, avec + / - (todo)
function ctlNombre($composant, $nic, $nom, $nombre=0, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  return '<table><tr><td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'"  class="label">'._TC($composant, $nom).'</label></td><td><input type="text" name="'.$var.'_'.$wid.'" size="8" maxlength="6" class="forml" value="'.$nombre.'" style="text-align:'.$GLOBALS['spip_lang_right'].'" /></td></tr></table>';
}

// Saisie d'un texte
function ctlText($composant, $nic, $nom, $txt, $param = array('taille' => 30), $wid) {
  $var =  nomvar($composant, $nic, $nom);
  return '<table width="100%"><tr><td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'"  class="label">'._TC($composant, $nom).'</label></td><td><input type="text" name="'.$var.'_'.$wid.'" size="'.$param['taille'].'" maxlength="'.$param['taille'].'" class="forml" value="'.$txt.'" /></td></tr></table>';
}

// Saisie d'un texte long
function ctlTextarea($composant, $nic, $nom, $txt, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  return '<div align="'.$GLOBALS['spip_lang_left'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'">'._TC($composant, $nom).'</label><textarea name="'.$var.'_'.$wid.'" class="forml" rows="'.(isset($param['lines']) ? $param['lines']-1 : 2).'">'.$txt.'</textarea></div>';
}

// Choix oui / non
function ctlChoix($composant, $nic, $nom, $value, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  if (!is_array($param['option'])) return 'Pas d\'options pour '.$nom;
  $r = '<table><tr valign="bottom"><td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'" class="label">'._TC($composant, $nom).'</label></td><td>';
  foreach($param['option'] as $option) {
    switch($option) {
      case 'oui';
        $label = _T('item_oui');
        break;
      case 'non';
        $label = _T('item_non');
        break;
      default:
        $label = _TC($composant, $nom.ucfirst($option));
        // S'il n'existe pas de traduction propre au composant pour ce choix, on cherche une traduction ACS generique
        if ($label == strtolower(str_replace('_', ' ', $nom.$option)))
        	$label = _T('acs:'.strtolower($option));
    }
    $r .= acs_bouton_radio(
      $var.'_'.$wid,
      $option,
      '<label for "'.$var.'_'.$wid.'" title="'.$var.ucfirst($option).'" class="label">'.$label.'</label>',
      $value == $option
    );
  }
  $r .= '</td></tr></table>';
  return $r;
}

function ctlUse($composant, $nic, $nom, $txt, $param, $wid) {
	return ctlChoix($composant, $nic, $nom, (($GLOBALS['meta'][nomvar($composant, $nic, $nom)] == 'oui') ? 'oui' : 'non'), array('option' => array('oui', 'non')), $wid);
}

// Choix d'un composant
function ctlWidget($composant, $nic, $nom, $value, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  require_once(_DIR_PLUGIN_ACS.'lib/composant/composants_variables.php');
  $vars = composants_variables();

  $r = '<table><tr><td><label for "'.$var.'_'.$wid.'" title="'.$var.'"  class="label">'.$nom.'</label></td>'.
  '<td><div id="'.$var.'_'.$wid.'" class="ctlWidget">';
  $r .= '<select id="select_'.$var.'_'.$wid.'" name="'.$var.'_'.$wid.'" class="forml select_widget">';
  $r .= '<option value=""'.($value=='' ? ' selected' : '').'> </option>';
  foreach(array_keys(composants_liste()) as $c) {
    $cuse = 'acs'.ucfirst($c).'Use';
    if (isset($GLOBALS['meta'][$cuse]) && (($GLOBALS['meta'][$cuse] == 'oui') || ($GLOBALS['meta'][$cuse] == 'yes')))
      $r .= '<option value="'.$c.'"'.($value==$c ? ' selected' : '').'>'.ucfirst($c).'</option>';
    elseif (!isset($GLOBALS['meta'][$cuse]) && (!in_array(ucfirst($c).'Use', composants_variables()))) {
      if (isset($value)) {
        if ($value==$c)
          $r .= '<option value="'.$c.'" selected>'.ucfirst($c).'</option>';
        else
          $r .= '<option value="'.$c.'">'.ucfirst($c).'</option>';
      }
    }
  }
  $r .= '</select></div></td></tr></table>';
  return $r;
}

function ctlHidden($composant, $nic, $nom, $value, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  return '<input type="hidden" name="'.$var.'_'.$wid.'" value="'.$value.'" />';
}
/**
 * Retourne la traduction spécifique au composant,
 * une traduction par défaut, ou le texte 
 */
function _TC($composant, $texte) {
	// traduction ACS propre au composant
	$t = _T('acs:'.$composant.'_'.$texte);
	if ($t != str_replace('_', ' ', $composant.'_'.$texte))
		return $t;
	// traduction ACS generique 
	$t = _T('acs:'.strtolower($texte));
	if ($t != str_replace('_', ' ', $texte))
		return $t;
	// traduction SPIP generique 
	$t = _T(strtolower($texte));
	if ($t != str_replace('_', ' ', $texte))
		return $t;
	return $texte;
}

function nomvar($composant, $nic, $nom) {
	return 'acs'.ucfirst($composant).$nic.$nom;
}

// http://doc.spip.org/@bouton_radio
function acs_bouton_radio($nom, $valeur, $titre, $actif = false, $onClick="", $enable=true) {
  static $id_label = 0;

  if (strlen($onClick) > 0) $onClick = " onclick=\"$onClick\"";
  $texte = "<input type='radio' name='$nom' value='$valeur' id='radio_$id_label'$onClick";
  if ($actif) {
    $texte .= ' checked="checked"';
    $titre = '<b>'.$titre.'</b>';
  }
  $texte .= ($enable ? '' : ' disabled')." />&nbsp;<label for='radio_$id_label'>$titre</label>\n";
  $id_label++;
  return $texte;
}
?>