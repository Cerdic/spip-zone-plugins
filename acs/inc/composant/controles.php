<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

// Requis pour fonction typo() utilisee dans ctlKey() :
include_spip('inc/texte');

// Choix de couleur - Color choice
function ctlColor($composant, $nic, $nom, $couleur, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  if (substr($couleur, 0, 4) == "=acs")
  	$color = meta_recursive($GLOBALS['meta'], substr($couleur, 1));
  	if (!$color)
  		$color = meta_recursive($GLOBALS['meta'], substr($couleur, 1).'/Color'); // Cas des variables de type "Bord", par exemple
  else
 		$color = meta_recursive($GLOBALS['meta'], $var);
  return '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr><td align="'.$GLOBALS['spip_lang_right'].'">&nbsp;<label for "'.$var.'_'.$wid.'" title="'.$var.'" class="label">'._TC($composant, $nom).'</label>&nbsp;</td><td><input type="text" class="palette" id="'.$var.'" name="'.$var.'_'.$wid.'" size="16" value="'.$couleur.'" style="background: '.$color.'"></td></tr></table></div>';
}

// Choix d'image - Image choice
function ctlImg($composant, $nic, $nom, $image, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  $path = $GLOBALS['ACS_CHEMIN'].'/'.$param['chemin'];
  mkdir_recursive(_ACS_DIR_SITE_ROOT.$path);
  $s = @getimagesize('../'.$path.'/'.$image);
  $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
  if ($param['label'] != 'non')
    $r .= '<td align="'.$GLOBALS['spip_lang_right'].'">&nbsp;<label for "'.$var.'_'.$wid.'" title="'.$var.'"  class="label">'._TC($composant, $nom).'</label>&nbsp;</td>';
  $r .= '<td><input type="text" id="'.$var.'_'.$wid.'" name="'.$var.'_'.$wid.'"'.(is_array($s) ? ' title="'.$s[0].'x'.$s[1].'"' : '').' value="'.$image.'" size="40" class="forml" /></td>';
  $r .= '<td>&nbsp;</td><td><a href="javascript:TFP.popup(document.forms[\'acs\'].elements[\''.$var.'_'.$wid.'\'], document.forms[\'acs\'].elements[\''.$var.'_'.$wid.'\'].value, \''.$path.'\', \''._ACS_DIR_SITE_ROOT.'\');" title="'._T('acs:choix_image').'"><img src="'._DIR_ACS.'images/folder_image.png" class="icon" alt="'._T('acs:choix_image').'" /></a></td></tr></table></div>';
  return $r;
}

// Choix de bordure - Border choice
function ctlBord($composant, $nic, $nom, $bord, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  $b = $GLOBALS['meta'][$var];
  $bord = unserialize($b);
  if (is_array($bord)) {
   	$largeur = $bord['Width'];
   	$style = $bord['Style'];
  	$couleur = $bord['Color'];
  }
  elseif (substr($b,0,1) == '=') {
   	$largeur = '=';
   	$style = '=';
  	$couleur = $b;
  }
	$r = '<table><tr><td>'.ctlColor($composant, $nic, $nom.'Color', $couleur, $param, $wid).'</td>';
	$param['label'] = 'non';
	$r .= '<td>'.ctlLargeurBord($composant, $nic, $nom.'Width', $largeur, $param, $wid).
				'<td>'.ctlStyleBord($composant, $nic, $nom.'Style', $style, $param, $wid).'</td>'.
		'</tr></table>';
	return $r;
}

// Choix d'une largeur de bordure - Border width choice
function ctlLargeurBord($composant, $nic, $nom, $largeur='0', $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
  if ($param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'" class="label">'._TC($composant, $nom).'</label></td>';
  if (!in_array($largeur, array('', '0', 'thin', '1px', '2px', '3px', '4px', '5px')))
  	$option = '<option value="'.$largeur.'"  title="'.$largeur.' ('.$GLOBALS['meta'][substr($largeur, 1)].')" selected>=</option>';
  $r .= '<td><select name="'.$var.'_'.$wid.'" title="'._T('acs:bordlargeur').' '.$var.'" class="forml" style="width: auto">'.
    '<option value=""'.($largeur=="" ? ' selected' : '').' title="'._T('acs:parent').'"></option>'.
  	$option.
    '<option value="0"'.($largeur=="0" ? ' selected' : '').' title="0">0</option>'.
    '<option value="1px"'.($largeur=="1px" ? ' selected' : '').' title="1px">1px</option>'.
    '<option value="2px"'.($largeur=="2px" ? ' selected' : '').' title="2px">2px</option>'.
    '<option value="3px"'.($largeur=="3px" ? ' selected' : '').' title="3px">3px</option>'.
    '<option value="4px"'.($largeur=="4px" ? ' selected' : '').' title="4px">4px</option>'.
    '<option value="5px"'.($largeur=="5px" ? ' selected' : '').' title="5px">5px</option>'.
    '<option value="8px"'.($largeur=="8px" ? ' selected' : '').' title="5px">8px</option>'.
    '<option value="10px"'.($largeur=="10px" ? ' selected' : '').' title="5px">10px</option>'.
    '<option value="15px"'.($largeur=="15px" ? ' selected' : '').' title="5px">15px</option>'.
    '</select></td></tr></table></div>';
  return $r;
}

// Choix d'un style  de bordure - Border style choice
function ctlStyleBord($composant, $nic, $nom, $style='solid', $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
  if ($param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'" class="label">'._TC($composant, $nom).'</label></td>';
  if (!in_array($style, array('', 'none', 'solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset')))
  	$option = '<option value="'.$style.'"  title="'.$style.' ('.$GLOBALS['meta'][substr($style, 1)].')" selected>=</option>';
  $r .= '<td><select name="'.$var.'_'.$wid.'" title="'._T('acs:bordstyle').' '.$var.'" class="forml" style="width: auto">'.
    '<option value=""'.($style=="" ? ' selected' : '').' title="'._T('acs:parent').'"></option>'.
  	$option.
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

// Choix d'une famille de fonte - Font family choice
function ctlFontFamily($composant, $nic, $nom, $style='sans-serif', $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
  if ($param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'" class="label">'._TC($composant, $nom).'</label>&nbsp;</td>';
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
	return '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr><td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'"  class="label">'._TC($composant, $nom).'</label>&nbsp;</td><td><input type="text" name="'.$var.'_'.$wid.'" size="8" class="forml" value="'.$nombre.'" style="text-align:'.$GLOBALS['spip_lang_right'].'" /></td></tr></table></div>';
}

// Saisie d'un texte
function ctlText($composant, $nic, $nom, $txt, $param = array('taille' => 30), $wid) {
  $var =  nomvar($composant, $nic, $nom);
  $r = '<table width="100%"><tr>';
  if ($param['label'] != 'non')
  	$r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'"  class="label">'._TC($composant, $nom).'</label>&nbsp;</td>';
  $r .= '<td><input type="text" name="'.$var.'_'.$wid.'" size="'.$param['taille'].'" maxlength="'.$param['taille'].'" class="forml" value="'.$txt.'" /></td></tr></table>';
  return $r;
}

// Saisie d'un texte long
function ctlTextarea($composant, $nic, $nom, $txt, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  return '<div align="'.$GLOBALS['spip_lang_left'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'">'._TC($composant, $nom).'</label><textarea name="'.$var.'_'.$wid.'" class="forml" rows="'.(isset($param['lines']) ? $param['lines']-1 : 2).'">'.$txt.'</textarea></div>';
}

// Choix oui / non
function ctlChoix($composant, $nic, $nom, $value, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  if (!is_array($param['option']))
  	return 'Pas d\'options pour '.$nom;
  $r = '<table><tr valign="middle"><td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$var.'_'.$wid.'" title="'.$var.'" class="label">'._TC($composant, $nom).'</label></td>';
  foreach($param['option'] as $option) {
    switch($option) {
      case 'oui':
      case 'yes';
        $label = _T('item_oui');
        break;
      case 'non':
      case 'no':
        $label = _T('item_non');
        break;
      default:
        $label = _TC($composant, $nom.ucfirst($option));
        // S'il n'existe pas de traduction propre au composant, on cherche une traduction ACS generique pour cette option
        if ($label == strtolower(str_replace('_', ' ', $nom.$option)))
        	$label = _T('acs:'.strtolower($option));
    }
    $r .= '<td>&nbsp;'.acs_bouton_radio(
      $var.'_'.$wid,
      $option,
      $label,
      $value == $option
    ).'&nbsp;<td>';
  }
  $r .= '</tr></table>';
  return $r;
}

function ctlUse($composant, $nic, $nom, $txt, $param, $wid) {
	return ctlChoix($composant, $nic, $nom, (($GLOBALS['meta'][nomvar($composant, $nic, $nom)] == 'oui') ? 'oui' : 'non'), array('option' => array('oui', 'non')), $wid);
}

// Choix d'un mot-clef
function ctlKey($composant, $nic, $nom, $value, $param, $wid) {
	global $spip_lang, $spip_lang_left, $spip_lang_right;
	
	$var =  nomvar($composant, $nic, $nom);
	$value = unserialize($value);
	$vid_group = $value['Group'];
	$vmot = $value['Key'];
	$r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
  if ($param['label'] != 'non')
    $r .= '<td><label for "'.$var.'Key_'.$wid.'" title="'.$var.'"  class="label">'._TC($composant, $nom).'</label>&nbsp;</td>';
	$r .= '<td><select id="select_'.$var.'Group_'.$wid.'" name="'.$var.'Group_'.$wid.'" class="forml" onchange="submit()">';
	$groups_query = sql_select("*, ".sql_multi ("titre", "$spip_lang"), "spip_groupes_mots", "", "", "multi");	
	while ($row_groupes = sql_fetch($groups_query)) {
		$id_groupe = $row_groupes['id_groupe'];
		$titre_groupe = typo($row_groupes['titre']);
		$r .= '<option value="'.$id_groupe.'"'.($id_groupe == $vid_group ? ' selected' : '').'>'.$titre_groupe.'</option>';
	}
	$r .= '</select></td>';
	$r .= '<td><select id="select_'.$var.'Key_'.$wid.'" name="'.$var.'Key_'.$wid.'" class="forml">';
	if ($vid_group) {
  	$keys_query = sql_select("*, ".sql_multi ("titre", "$spip_lang"), "spip_mots", "id_groupe=".$vid_group, "", "multi");  	
  	while ($row_keys = sql_fetch($keys_query)) {
  		$titre_mot = typo($row_keys['titre']);
  		$r .= '<option value="'.$titre_mot.'"'.($titre_mot == $vmot ? ' selected' : '').'>'.$titre_mot.'</option>';
  	}  	
	}
	$r .= '</select></td></tr></table></div>';
	return $r;
}

// Choix d'un composant
function ctlWidget($composant, $nic, $nom, $value, $param, $wid) {
  $var =  nomvar($composant, $nic, $nom);
  require_once(_DIR_PLUGIN_ACS.'inc/composant/composants_variables.php');
  $vars = composants_variables();

  $r = '<table><tr>';
  if ($param['label'] != 'non')
    $r .= '<td><label for "'.$var.'_'.$wid.'" title="'.$var.'"  class="label">'._TC($composant, $nom).'</label>&nbsp;</td>';
  $r .= '<td><div id="'.$var.'_'.$wid.'" class="ctlWidget">';
  $r .= '<select id="select_'.$var.'_'.$wid.'" name="'.$var.'_'.$wid.'" class="forml select_widget">';
  $r .= '<option value=""'.($value=='' ? ' selected' : '').'> </option>';
  
  //foreach($cl as $c=>$widget)
  foreach (composants_liste() as $class => $c) {
  	foreach($c['instances'] as $lnic => $cp) {
    	if ($lnic == $nic) continue;
    	if ($cp['on'] == 'oui')
				$r .= '<option value="'.$class.($lnic ? '-'.$lnic : '').'"'.($value==$class.($lnic ? '-'.$lnic : '') ? ' selected' : '').'>'.ucfirst($class).($lnic ? ' '.$lnic : '').'</option>';
  	}
  }
  $r .= '</select></div></td>';
  $r .= '</tr></table>';
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
		return str_replace(' ', '&nbsp;', $t);
	// traduction ACS generique 
	$t = _T('acs:'.strtolower($texte));
	if ($t != str_replace('_', ' ', $texte))
		return str_replace(' ', '&nbsp;', $t);
	// traduction SPIP generique 
	$t = _T(strtolower($texte));
	if ($t != str_replace('_', ' ', $texte))
		return str_replace(' ', '&nbsp;', $t);
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
  $texte .= ($enable ? '' : ' disabled')." />&nbsp;<label for='radio_$id_label'>$titre</label>";
  $id_label++;
  return $texte;
}
?>