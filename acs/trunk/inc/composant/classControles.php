<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acs_presentation');

/**
 * \~french
 * Classe Controle : affiche une interface de saisie pour une variable ACS
 * @param $composant : type de composant
 * @param $nic : numero d'instance de composant
 * @param $nom : nom de variable
 * @param $value : valeur de la variable
 * @param $param : parametres definis par le fichier composant.xml du composant
 * @param $wid : widget id
 * 
 * \~english
 * Controle class : display a graphic interface to edit an ACS variable
 */
abstract class Controle {
  protected $composant;
  protected $nic; 
  protected $nom;
  protected $value;
  protected $param;
  protected $wid;
  protected $var;
  protected $help;
  
  public function __construct($composant, $nic, $nom, $value, $param, $wid) {
    $this->composant = $composant;
    $this->nic = $nic;
    $this->nom = $nom;
    $this->value = $value;
    $this->param = $param;
    $this->wid = $wid;

    $this->var = 'acs'.ucfirst($composant).$nic.$nom;

    $help_src = $nom.'Help';
    $help = _TC($composant, $help_src);
    $this->help = ($help != $help_src) ? $help : false;
  }
  
  abstract public function draw();
}

/**
 * \~french
 * Classe ctlColor : Choix de couleur
 *
 * \~english
 * ctlColor class : Color choice
 */
class ctlColor extends Controle {
  public function draw() {
    if (substr($this->value, 0, 4) == "=acs")
      $color = meta_recursive($GLOBALS['meta'], substr($this->value, 1));
    if (!$color)
      $color = meta_recursive($GLOBALS['meta'], substr($this->value, 1).'/Color'); // Cas des variables de type "Bord", par exemple
    else
      $color = meta_recursive($GLOBALS['meta'], $this->var);
    $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr><td align="'.$GLOBALS['spip_lang_right'].'">&nbsp;<label for "'.$this->var.'_'.$this->wid.'" title="'.$this->var.'" class="label">'._TC($this->composant, $this->nom).'</label>&nbsp;</td><td><input type="text" class="palette" id="'.$this->var.'" name="'.$this->var.'_'.$this->wid.'" size="16" value="'.$this->value.'" style="background: '.$color.'"></td>'.($this->help ? '<td>&nbsp;</td><td>'.acs_help_call($this->var.'Help').'</td>' : '').'</tr></table></div>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlImg : Choix d'image
 *
 * \~english
 * ctlImg class : image choice
 */
class ctlImg extends Controle {
  public function draw() {
    $path = $GLOBALS['ACS_CHEMIN'].'/'.$this->param['chemin'];
    if (!mkdir_recursive(_DIR_RACINE.$path))
      $err = "*";
    $s = @getimagesize('../'.$path.'/'.$image);
    $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
    if ($this->param['label'] != 'non')
      $r .= '<td align="'.$GLOBALS['spip_lang_right'].'">&nbsp;<label for "'.$this->var.'_'.$this->wid.'" title="'.$this->var.'"  class="label">'._TC($this->composant, $this->nom).'</label>&nbsp;</td>';
    $r .= '<td><input type="text" id="'.$this->var.'_'.$this->wid.'" name="'.$this->var.'_'.$this->wid.'"'.(is_array($s) ? ' title="'.$s[0].'x'.$s[1].'"' : '').' value="'.$this->value.'" size="40" class="forml" /></td>';
    $r .= '<td>&nbsp;</td><td><a href="javascript:TFP.popup(document.forms[\'acs\'].elements[\''.$this->var.'_'.$this->wid.'\'], document.forms[\'acs\'].elements[\''.$this->var.'_'.$this->wid.'\'].value, \''.$path.'\', \''._DIR_RACINE.'\');" title="'._T('acs:choix_image').' '.$this->var.'"><img src="'._DIR_ACS.'images/folder_image.png" class="icon" alt="'._T('acs:choix_image').' '.$this->var.'" /></a>'.$err.'</td>'.($this->help ? '<td>&nbsp;</td><td>'.acs_help_call($this->var.'Help').'</td>' : '').'</tr></table></div>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlBord : Choix de bordure
 *
 * \~english
 * ctlBord class : Border choice
 */
class ctlBord extends Controle {
  public function draw() {
    $b = $GLOBALS['meta'][$this->var];
    $bord = unserialize($this->value);
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
    $ctlColor = new ctlColor($this->composant, $this->nic, $this->nom.'Color', $couleur, $this->param, $this->wid);
    $this->param['label'] = 'non';
    $ctlLargeurBord = new ctlLargeurBord($this->composant, $this->nic, $this->nom.'Width', $largeur, $this->param, $this->wid);
    $ctlStyleBord = new ctlStyleBord($this->composant, $this->nic, $this->nom.'Style', $style, $this->param, $this->wid);
    $r = '<table><tr>
            <td>'.$ctlColor->draw().'</td>
            <td>'.$ctlLargeurBord->draw().'</td>
            <td>'.$ctlStyleBord->draw().'</td>'.
            ($this->help ? '<td>&nbsp;</td><td>'.acs_help_call($this->var.'Help').'</td>' : '').
         '</tr></table>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlLargeurBord : Choix d'une largeur de bordure
 *
 * \~english
 * ctlLargeurBord class : Border width choice
 */
class ctlLargeurBord extends Controle {
  public function draw() {
    $largeur = $this->value;
    $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
    if ($this->param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$this->var.'_'.$this->wid.'" title="'.$this->var.'" class="label">'._TC($this->composant, $this->nom).'</label></td>';
    if (!in_array($largeur, array('', '0', 'thin', '1px', '2px', '3px', '4px', '5px')))
      $option = '<option value="'.$largeur.'"  title="'.$largeur.' ('.$GLOBALS['meta'][substr($largeur, 1)].')" selected>=</option>';
    $r .= '<td><select id="'.$this->var.'" name="'.$this->var.'_'.$this->wid.'" title="'._T('acs:bordlargeur').' '.$this->var.'" class="forml" style="width: auto">'.
      '<option value=""'.($largeur=="" ? ' selected' : '').' title="'._T('acs:parent').'"></option>'.
      $option.
      '<option value="0"'.($largeur=="0" ? ' selected' : '').' title="0">0</option>'.
      '<option value="1px"'.($largeur=="1px" ? ' selected' : '').' title="1px">1px</option>'.
      '<option value="2px"'.($largeur=="2px" ? ' selected' : '').' title="2px">2px</option>'.
      '<option value="3px"'.($largeur=="3px" ? ' selected' : '').' title="3px">3px</option>'.
      '<option value="4px"'.($largeur=="4px" ? ' selected' : '').' title="4px">4px</option>'.
      '<option value="5px"'.($largeur=="5px" ? ' selected' : '').' title="5px">5px</option>'.
      '<option value="8px"'.($largeur=="8px" ? ' selected' : '').' title="8px">8px</option>'.
      '<option value="10px"'.($largeur=="10px" ? ' selected' : '').' title="10px">10px</option>'.
      '<option value="15px"'.($largeur=="15px" ? ' selected' : '').' title="15px">15px</option>'.
      '</select></td>'.($this->help ? '<td>&nbsp;</td><td>'.acs_help_call($this->var.'Help').'</td>' : '').'</tr></table></div>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlStyleBord : Choix d'un style  de bordure 
 *
 * \~english
 * ctlStyleBord class : Border style choice
 */
class ctlStyleBord extends Controle {
  public function draw() {
    $style = $this->value;
    $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
    if ($this->param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$this->var.'_'.$this->wid.'" title="'.$this->var.'" class="label">'._TC($this->composant, $this->nom).'</label></td>';
    if (!in_array($style, array('', 'none', 'solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset')))
      $option = '<option value="'.$style.'"  title="'.$style.' ('.$GLOBALS['meta'][substr($style, 1)].')" selected>=</option>';
    $r .= '<td><select id="'.$this->var.'" name="'.$this->var.'_'.$this->wid.'" title="'._T('acs:bordstyle').' '.$this->var.'" class="forml" style="width: auto">'.
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
      '</select></td>'.($this->help ? '<td>&nbsp;</td><td>'.acs_help_call($this->var.'Help').'</td>' : '').'</tr></table></div>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlFontFamily : Choix d'une famille de fonte
 *
 * \~english
 * ctlFontFamily class : Font family choice
 */
class ctlFontFamily extends Controle {
  public function draw() {
    $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
    if ($this->param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$this->var.'_'.$this->wid.'" title="'.$this->var.'" class="label">'._TC($this->composant, $this->nom).'</label>&nbsp;</td>';
    $r .= '<td><select name="'.$this->var.'_'.$this->wid.'" title="'._T('acs:bordstyle').'" class="forml" style="width: auto">'.
      '<option value="serif"'.($style=="serif" ? ' selected' : '').' title="'._T('acs:serif').'">serif</option>'.
      '<option value="sans-serif"'.($style=="sans-serif" ? ' selected' : '').' title="'._T('acs:sans-serif').'">sans-serif</option>'.
      '<option value="cursive"'.($style=="cursive" ? ' selected' : '').' title="'._T('acs:cursive').'">cursive</option>'.
      '<option value="fantasy"'.($style=="fantasy" ? ' selected' : '').' title="'._T('acs:fantasy').'">fantasy</option>'.
      '<option value="monotype"'.($style=="monotype" ? ' selected' : '').' title="'._T('acs:monotype').'">monotype</option>'.
      '</select></td>'.($this->help ? '<td>&nbsp;</td><td>'.acs_help_call($this->var.'Help').'</td>' : '').'</tr></table></div>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlText : Saisie d'un texte
 *
 * \~english
 * ctlText class : short text 
 */
class ctlText extends Controle {
  public function draw() {
    $r = '<table width="100%"><tr>';
    if ($this->param['label'] != 'non')
      $r .= '<td align="'.$GLOBALS['spip_lang_right'].'">&nbsp;<label for "'.$this->var.'_'.$this->wid.'" title="'.$this->var.'"  class="label">'._TC($this->composant, $this->nom).'</label>&nbsp;</td>';
    $r .= '<td><input id="'.$this->var.'" type="text" name="'.$this->var.'_'.$this->wid.'" size="'.$this->param['taille'].'" maxlength="'.$this->param['taille'].'" class="forml" value="'.htmlspecialchars($this->value).'" /></td>'.($this->help ? '<td>&nbsp;</td><td>'.acs_help_call($this->var.'Help').'</td>' : '').'</tr></table>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlTextarea : Saisie d'un texte long 
 *
 * \~english
 * ctlTextarea class : long text
 */
class ctlTextarea extends Controle {
  public function draw() {
    $r = '<div align="'.$GLOBALS['spip_lang_left'].'"><label for "'.$this->var.'_'.$this->wid.'" title="'.$this->var.'">'._TC($this->composant, $this->nom).'</label><textarea name="'.$this->var.'_'.$this->wid.'" class="forml" rows="'.(isset($this->param['lines']) ? $this->param['lines']-1 : 2).'">'.$this->value.'</textarea></div>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlChoix : Choix 
 *
 * \~english
 * ctlChoix class :  choice
 */
class ctlChoix extends Controle {
  public function draw() {
    if (!is_array($this->param['option']))
      return 'Pas d\'options pour '.$this->nom;
    $r = '<table><tr valign="middle">';
    if ($this->param['label'] != 'non')    
      $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$this->var.'_'.$this->wid.'" title="'.$this->var.'" class="label">'._TC($this->composant, $this->nom).'</label></td>';
    foreach($this->param['option'] as $option) {
      switch($option) {
        case 'oui':
        case 'yes';
          $label = _T('acs:oui');
          break;
        case 'non':
        case 'no':
          $label = _T('acs:non');
          break;
        default:
          $label = _TC($this->composant, $this->nom.$option);
          // S'il n'existe pas de traduction propre au composant, on cherche une traduction ACS generique pour cette option
          if ( ($label == str_replace('_', ' ', $this->nom.$option)) || (substr($label, 0, 12) == '<blink style'))
            $label = _T('acs:'.strtolower($option));
      }
      $r .= '<td>&nbsp;'.acs_bouton_radio(
        $this->var.'_'.$this->wid,
        $option,
        $label,
        $this->value == $option
      ).'&nbsp;<td>';
    }
    $r .= ($this->help ? '<td>&nbsp;</td><td>'.acs_help_call($this->var.'Help').'</td>' : '').'</tr></table>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlUse : Choix utiliser oui/non
 *
 * \~english
 * ctlUse class :  choice use or not (oui/non)
 */
class ctlUse extends Controle {
  public function draw() {
    $ctl = new ctlChoix($this->composant, $this->nic, $this->nom, (($GLOBALS['meta'][nomvar($this->composant, $this->nic, $this->nom)] == 'oui') ? 'oui' : 'non'), array('option' => array('oui', 'non')), $this->wid);
    $r = $ctl->draw();
    if ($this->help)  
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlKey : Choix d'un mot-clef
 *
 * \~english
 * ctlKey class : keyword choice
 */
class ctlKey extends Controle {
  public function draw() {

    // Requis pour fonction typo()
    include_spip('inc/texte');
  
    $this->value = unserialize($this->value);
    $vid_group = $this->value['Group'];
    $vmot = $this->value['Key'];
    $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
    if ($this->param['label'] != 'non')
      $r .= '<td><label for "'.$this->var.'Key_'.$this->wid.'" title="'.$this->var.'"  class="label">'._TC($this->composant, $this->nom).'</label>&nbsp;</td>';
    $r .= '<td><select id="select_'.$this->var.'Group_'.$this->wid.'" name="'.$this->var.'Group_'.$this->wid.'" class="forml" onchange="submit()">';
    $r .= '<option value=""'.($vid_group =='' ? ' selected' : '').'></option>'; 
    $groups_query = sql_select("*, ".sql_multi ("titre", "$spip_lang"), "spip_groupes_mots", "", "", "multi");  
    while ($row_groupes = sql_fetch($groups_query)) {
      $id_groupe = $row_groupes['id_groupe'];
      $titre_groupe = typo($row_groupes['titre']);
      $r .= '<option value="'.$id_groupe.'"'.($id_groupe == $vid_group ? ' selected' : '').'>'.$titre_groupe.'</option>';
    }
    $r .= '</select></td>';
    $r .= '<td><select id="select_'.$this->var.'Key_'.$this->wid.'" name="'.$this->var.'Key_'.$this->wid.'" class="forml">';
    if ($vid_group) {
      $keys_query = sql_select("*, ".sql_multi ("titre", "$spip_lang"), "spip_mots", "id_groupe=".$vid_group, "", "multi");   
      while ($row_keys = sql_fetch($keys_query)) {
        $titre_mot = typo($row_keys['titre']);
        $r .= '<option value="'.$titre_mot.'"'.($titre_mot == $vmot ? ' selected' : '').'>'.$titre_mot.'</option>';
      }   
    }
    $r .= '</select></td>'.($this->help ? '<td>&nbsp;</td><td>'.acs_help_call($this->var.'Help').'</td>' : '').'</tr></table></div>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlKeyGroup : Choix d'un groupe de mots-clefs
 *
 * \~english
 * ctlKeyGroup class : keywords group choice
 */
class ctlKeyGroup extends Controle {
  public function draw() {
    
    // Requis pour fonction typo()
    include_spip('inc/texte');
  
    $vid_group = $this->value;
    $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
    if ($this->param['label'] != 'non')
      $r .= '<td><label for "'.$this->var.'_'.$this->wid.'" title="'.$this->var.'"  class="label">'._TC($this->composant, $this->nom).'</label>&nbsp;</td>';
    $r .= '<td><select id="select_'.$this->var.'_'.$this->wid.'" name="'.$this->var.'_'.$this->wid.'" class="forml" title="'.$this->var.($vid_group ? ' = '.$vid_group : '').'">';
    $r .= '<option value=""'.($vid_group =='' ? ' selected' : '').'></option>'; 
    $groups_query = sql_select("*, ".sql_multi ("titre", "$spip_lang"), "spip_groupes_mots", "", "", "multi");  
    while ($row_groupes = sql_fetch($groups_query)) {
      $id_groupe = $row_groupes['id_groupe'];
      $titre_groupe = typo($row_groupes['titre']);
      $r .= '<option value="'.$id_groupe.'"'.($id_groupe == $vid_group ? ' selected' : '').'>'.$titre_groupe.'</option>';
    }
    $r .= '</select></td>'.($this->help ? '<td>&nbsp;</td><td>'.acs_help_call($this->var.'Help').'</td>' : '').'</tr></table></div>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlWidget : Choix d'un composant
 *
 * \~english
 * ctlWidget class :  choice of a component
 */
class ctlWidget extends Controle {
  public function draw() {
    require_once(_DIR_PLUGIN_ACS.'inc/composant/composants_liste.php');
  
    $r = '<table><tr>';
    if ($this->param['label'] != 'non')
      $r .= '<td><label for "'.$this->var.'_'.$this->wid.'" title="'.$this->var.'"  class="label">'._TC($this->composant, $this->nom).'</label>&nbsp;</td>';
    $r .= '<td><div id="'.$this->var.'_'.$this->wid.'" class="ctlWidget">';
    $r .= '<select id="select_'.$this->var.'_'.$this->wid.'" name="'.$this->var.'_'.$this->wid.'" class="forml select_widget">';
    $r .= '<option value=""'.($this->value=='' ? ' selected' : '').'> </option>';
    
    foreach (composants_liste() as $class => $c) {
      foreach($c['instances'] as $lnic => $cp) {
        if ($lnic == $this->nic) continue;
        if ($cp['on'] == 'oui')
          $r .= '<option value="'.$class.($lnic ? '-'.$lnic : '').'"'.($this->value==$class.($lnic ? '-'.$lnic : '') ? ' selected' : '').'>'.ucfirst($class).($lnic ? ' '.$lnic : '').'</option>';
      }
    }
    $r .= '</select></div></td>';
    $r .= ($this->help ? '<td>&nbsp;</td><td>'.acs_help_call($this->var.'Help').'</td>' : '').'</tr></table>';
    if ($this->help)
      $r .= acs_help_div($this->var.'Help', $this->help);
    return $r;
  }
}

/**
 * \~french
 * Classe ctlHidden : contrôle caché 
 *
 * \~english
 * ctlHidden class : hidden control
 */
class ctlHidden extends Controle {
  public function draw() {
    return '<input type="hidden" name="'.$this->var.'_'.$this->wid.'" value="'.$this->value.'" />';  
  }
}

/**
 * \~french
 * Retourne la traduction spécifique au composant, ou sinon une traduction par 
 * défaut, ou sinon, le texte. 
 */
function _TC($composant, $texte) {
	// traduction ACS propre au composant
	$t = _T('acs:'.$composant.'_'.$texte);
	if ($t != str_replace('_', ' ', $composant.'_'.$texte) && (substr($t, 0, 12) != '<blink style')) // 2 tests : pré et post SPIP 3
		return $t;
	// traduction ACS generique 
	$t = _T('acs:'.strtolower($texte));
	if ($t != str_replace('_', ' ', strtolower($texte)) && (substr($t, 0, 12) != '<blink style'))
		return $t;
	// traduction SPIP generique 
	$t = _T(strtolower($texte));
	if ($t != str_replace('_', ' ', strtolower($texte)) && (substr($t, 0, 12) != '<blink style'))
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
  $texte .= ($enable ? '' : ' disabled')." />&nbsp;<label for='radio_$id_label'>$titre</label>";
  $id_label++;
  return $texte;
}
?>