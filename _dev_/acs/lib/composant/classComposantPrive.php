<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Classe Composant
 *
 * Chaque composant ACS peut définir une classe <MonComposant>
 * qui étend la classe Composant en définissant des méthodes de l'interface Icomposant
 * Ce sont des points d'entrée logiciels pour les objets de classe Composant,
 * un peu comparables aux pipelines spip, mais en technologie objet
 */

abstract class Composant implements Icomposant {
}

interface Icomposant {
  public function afterUpdate();
}

/**
 * Classe AdminComposant
 *
 * "Interface d'admin de coposants pour ACS
 */

class AdminComposant {
  // Constructeur
  // Instancie (au besoin) un objet Composant à l'éxécution
  // pour en adopter les méthodes implémentées.
  function AdminComposant($type, $debug = false)  {
    $this->debug = $debug;
    $this->type = $type;
    $this->fullname = "acs".ucfirst($type);
    $this->errors = array();
    $this->vars = array();
    $this->cvars = array(); // Variables issues d'un autre composant
    $this->widgets = array(); // Composants intégrables à une variable widget
    // Dossier racine du composant
    $this->rootDir = find_in_path('composants/'.$type);
    $this->icon = $this->rootDir.'/img_pack/'.$type.'_icon.gif';

    // Charge le fichier optionnel de langue du composant. Ordre de recherche:
    // 1. langue courante depuis dossier_squelettes_over_acs
    // 2. langue courante depuis dossier squelettes acs
    // 3. langue par défaut depuis dossier_squelettes_over_acs
    // 4. langue par défaut depuis dossier squelettes acs
    $GLOBALS['idx_lang'] = 'i18n_ecrire_'.$GLOBALS['spip_lang'];
    $langfile = find_in_path('composants/'.$type.'/ecrire/lang/'.$type.'_adm_'.$GLOBALS['spip_lang'].'.php');
    if (!$langfile)
      $langfile = find_in_path('composants/'.$type.'/ecrire/lang/'.$type.'_adm_fr.php');
    surcharger_langue($langfile);

    // Lit les paramètres de configuration du composant
    include_once('inc/xml.php');
    $config = spip_xml_load($this->rootDir.'/ecrire/composant.xml');

    // Affecte ses paramètres de configuration à l'objet Composant
    $c = $config['composant'][0];
    $this->nom = $c['nom'][0];
    $this->version = $c['version'][0];
    $this->version_spip_min = $c['version_spip_min'][0];
    $this->version_spip_max = $c['version_spip_max'][0];
    $this->description = _T($type.'_description');

    if (is_array($c['param'])) {
      foreach($c['param'] as $param) {
        if ($this->debug) echo  $type.'->'.$param['nom'][0], ' = ', htmlentities($param['valeur'][0]) , "<br />"; // dbg composant
        if (isset($param['valeur']))
          $this->$param['nom'][0] = $param['valeur'][0];
        else
          $this->$param['nom'][0] = true;
      }
    }
    if (is_array($c['variable'])) {
      foreach($c['variable'] as $k=>$var) {
        foreach($var as $varname=>$value) {
          if (count($value) > 1)
              $v = $value;
          else
            $v = $value[0];
          if ($this->debug) echo $type.'->vars['.$k.']['.$varname.'] = '.htmlentities((is_array($v) ? 'Array('.implode($v, ', ').')' : $v)) , "<br />"; // dbg composant
          $this->vars[$k][$varname] = $v;
          if ($varname == 'valeur') { // Default values
            if (substr($v,0,3) == 'acs') {
              if (!in_array($v, $this->cvars)) $this->cvars[ ] = $v;
              $v = $GLOBALS['meta'][$v];
            }
          }
          // Lit la liste des composants intégrables aux variables de type widget
          if ($varname == 'composant') {
            foreach ($value as $widget) {
              if (!in_array($widget, $this->widgets)) array_push($this->widgets, $widget);
            }
          }
        }
      }
    }
    // Mise à jour
    if ($_POST['changer_config']=='oui') {
      foreach ($this->vars as $var) {
        $v = $this->fullname.$var['nom'];
        if ($_POST[$v] != $GLOBALS['meta'][$v]) {
          if (($_POST[$v]=='') && (isset($var['valeur']))) {
            if (substr($var['valeur'], 0, 3) == 'acs')
              $nv = $GLOBALS['meta'][$var['valeur']];
            else $nv = $var['valeur'];
          }
          else $nv = $_POST[$v];
          ecrire_meta($v, $nv);
          $updated = true;
        }
      }
      if (isset($updated)) {
        if ($this->afterUpdate) {
          @include_once($this->rootDir.'/ecrire/'.$type.'.php');
          $cObj = 'acs'.ucfirst($type);
          if(class_exists($cObj)) {
            $$cObj = @new $cObj();
            if (($$cObj instanceof Composant) && is_callable(array($$cObj, 'afterUpdate'))) {
              if (!$$cObj->afterUpdate())
                $this->errors[] = $cObj.'->afterUpdate '._T('acs:failed');
            }
            else
              $this->errors[] = $cObj.'->afterUpdate '._T('acs:not_callable');
          }
            else
              $this->errors[] = $cObj.'->afterUpdate '._T('acs:not_found');
        }
        unset($updated);
      }
    }
  }

/**
 * Méthode getcvars: retourne du code html pour les variables du composant
 * faisant référence à une variable définie par un autre composant
 * pour leurs valeurs par défaut
 */
  function get_cvars_html() {
    foreach($this->cvars as $k =>$var) {
      if (!isset($GLOBALS['meta'][$var]))
        $class = ' alert';
      else
        $class = '';
      $this->cvars[$k] = '<a class="nompage'.$class.'" title="'.$GLOBALS['meta'][$var].'">'.substr($this->cvars[$k], 3).'</a>';
    }
    return implode(', ', $this->cvars);
  }

/**
 * Méthode gauche: affiche la colonne gauche dans spip admin
 */
  function gauche() {
    global $spip_version_code;

    if (_TC($this->type.'_description') != $this->type.' description')
      $r .= '<div>'._TC($this->type.'_description').'</div><br />';

    if (_TC($this->type.'_info') != $this->type.'_info')
      $r .= '<div class="onlinehelp" style="text-align: justify">'._TC($this->type.'_info').'</div><br />';
    if (_TC($this->type.'_help') != $this->type.'_help')
      $r .= '<div class="onlinehelp" onclick=\'$("#help_context").slideToggle("slow");\' style="cursor:pointer;"><img src="'._DIR_PLUGIN_ACS.'img_pack/aide.gif" onmouseover=\'$("#help_context").slideToggle("slow");\' /> '._T('icone_aide_ligne').'</div><div id="help_context" class="onlinehelp pliable" style="text-align: justify">'._TC($this->type.'_help').'</div><br />';

    $n = 999;
    $r .= '<div class="onlinehelp">'.acs_plieur('plieur_pu'.$n, 'pu'.$n, '#', false, 'if (typeof done'.$n.' == \'undefined\') {AjaxSqueeze(\'?exec=composant_get_infos&c='.$this->type.'\',\'puAjax'.$n.'\'); done'.$n.' = true;}', _T('acs:dev_infos') ).'</div><div class="pu'.$n.' pliable">';
    if (count($this->cvars))
      $r .= '<div class="onlinehelp">'._T('acs:references_autres_composants').'</div>'.
            '<div class="onlinehelplayer">'.$this->get_cvars_html().'</div><br />';
    $r .= '<div id="puAjax'.$n.'" class="puAjax'.$n.'"></div>';
    $r .= '<div>'._T('version').' '.$this->type.' <b>'.(($this->version != ACS_VERSION) ? '<span class="alert">'.$this->version.'</span>' : $this->version).'</b></div>';
    if ($spip_version_code < $this->version_spip_min)
      $r .= '<div class="alert">'._T('acs:spip_trop_ancien', array('min' => spip_version_texte($this->version_spip_min))).'</div>';
    elseif ($spip_version_code > $this->version_spip_max)
      $r .= '<div class="alert">'._T('acs:spip_non_supporte', array('max' => spip_version_texte($this->version_spip_max))).'</div>';
      $r .= '</div>';
    return $r;
  }

/**
 * Méthode editswitch: affiche/masque l'éditeur du composant
 */
  function editswitch($enable) {
  $varname = $this->fullname.'Use';
  $varconf = $this->fullname.'Config';

  if (isset($GLOBALS['meta'][$varname]) && $GLOBALS['meta'][$varname])
    $var = $GLOBALS['meta'][$varname];
  else
    $var = 'non';

  if ($var == "oui")  $this->display = "display: block;";
  else $this->display = "display: none;";

  // bouton_radio2($nom, $valeur, $titre, $actif = false, $onClick="", $enable=true)
  $o = bouton_radio2($varname, "oui", _T($this->type.'_on'), $var == "oui", "changeVisible(this.checked, '$varconf', 'block', 'none');",$enable);
  $o .= bouton_radio2($varname, "non", _T($this->type.'_off'), $var == "non", "changeVisible(this.checked, '$varconf', 'none', 'block');",$enable);
  return $o;
  }

/**
 * Méthode edit: affiche un éditeur pour les variables du composant
 */
  function edit() {
    static $n;
    $n++; // Numérotation du composant (utile pour js)
    $r = $this->n.'<div id="acs'.ucfirst($this->type).'Config" '.(isset($this->display) ? 'style="'.$this->display.'"' : '').'>';
    if (isset($this->preview) && ($this->preview != 'non')  && ($this->preview != 'no')) {
      $url = '../?page=wrap&c=composants/'.$this->type.'/'.$this->type.'&v='.$GLOBALS['meta']['acsDerniereModif'].'&var_mode=recalcul';
      $r .= '<fieldset class="apercu"><legend><a href="javascript:void(0)" onclick=" findObj(\''.$this->fullname.'\').src=\''.$url.'\';" title="'._T('admin_recalculer').'">'._T('previsualisation').'</a></legend><iframe id="'.$this->fullname.'" width="100%" height="'.(is_numeric($this->preview) ? $this->preview : 80).'px" frameborder="0" style="border:0" src="'.$url.'"></iframe></fieldset>';
    }
    // Affiche les variables paramétrables du composant:
    $controls = array();
    foreach ($this->vars as $var) {
      $v = $this->fullname.$var['nom'];
      if (isset($GLOBALS['meta'][$v]))
        $$v = $GLOBALS['meta'][$v];
      elseif (isset($var['valeur'])) {
        $default = $var['valeur'];
        if ((substr($default,0,3) =='acs') && isset($GLOBALS['meta'][$default]))
          $$v = $GLOBALS['meta'][$default];
        else
          $$v = $default;
      }
      $draw = 'ctl'.ucfirst($var['type']);
      if (is_callable(array($this,$draw)))
        $controls[$var['nom']] = $this->$draw($v, $$v, $var);
      else $controls[$var['nom']] = $draw."() undefined.<br />" ;
    }

    // Recherche une mise en page et y remplace les variables par des contrôles
    $mis_en_page = array();
    if (is_readable($this->rootDir.'/ecrire/'.$this->type.'_mep.html')) {
      $mep = file_get_contents($this->rootDir.'/ecrire/'.$this->type.'_mep.html');
      foreach ($controls as $nom=>$html) {
        $tag = '#'.$nom.'#';
        if (strpos($mep, $tag) !== false) $mis_en_page[] = $nom;
        $mep = str_replace($tag, $html, $mep);
      }
    }
    // Ajoute les contrôles non mis en page
    foreach ($controls as $nom=>$html) {
      if (!in_array($nom, $mis_en_page)) $r.= $html;
    }

    if (isset($mep)) $r .= '<div align="'.$GLOBALS['spip_lang_right'].'">'.$mep.'</div>';
    $r .= '</div><table width="100%"><tr><td>';

    if (count($this->errors)) $r .= '<div class="alert">'.implode('<br />', $this->errors).'</div>';
    $r .= '</td><td valign="bottom"><div style="text-align:'.$GLOBALS['spip_lang_right'].';"><input type="submit" name="'._T('bouton_valider').'" value="'._T('bouton_valider').'" class="fondo"></div></td></tr></table>';
    return $r;
  }
/**
 * Méthode page: retourne un tableau des pages qui utilisent ce composant
 */
  function pages() {
    include_once(_DIR_PLUGIN_ACS.'lib/cGetPages.php');
    return cGetPages($this->type);
  }

  // Choix de couleur
  function ctlColor($nom, $couleur, $param) {
    return '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr><td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$nom.'" title="'.$nom.'" class="label">'._T($nom).'</label></td><td><input type="text" id="'.$nom.'" name="'.$nom.'" size="8" maxlength="6" value="'.$couleur.'" class="forml" onKeyUp="javascript:document.getElementById(\'led_'.$nom.'\').style.background=\'#\' + this.value;"></td><td><a   href="javascript:TCP.popup(document.forms[\'acs\'].elements[\''.$nom.'\'],0);"><div id="led_'.$nom.'" class="led" style="background: #'.$couleur.';" title="'._T('acs:choix_couleur').'"></div></a></td></tr></table></div>';
  }

  // Choix d'image
  function ctlImg($nom, $image, $param) {
    $c = $GLOBALS['ACS_CHEMIN'].'/'.$param['chemin'];
    acs_creer_chemin($c);
    $s = @getimagesize('../'.$c.'/'.$image);
    $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
    if ($param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$nom.'" title="'.$nom.'"  class="label">'._T($nom).'</label></td>';
   $r .= '<td><input type="text" name="'.$nom.'"'.(is_array($s) ? ' title="'.$s[0].'x'.$s[1].'"' : '').' value="'.$image.'" size="40" class="forml"></td>';
    $r .= '<td><a href="javascript:TFP.popup(document.forms[\'acs\'].elements[\''.$nom.'\'], document.forms[\'acs\'].elements[\''.$nom.'\'].value, \''.$c.'\');" title="'._T('acs:choix_image').'"><img src="'._DIR_PLUGIN_ACS.'img_pack/folder_image.png" class="icon" alt="'._T('acs:choix_image').'" /></a></td></tr></table></div>';
    return $r;
  }

  // Choix d'une largeur de bordure
  function ctlLargeurBord($nom, $largeur='0', $param) {
    $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
    if ($param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$nom.'" title="'.$nom.'" class="label">'._T($nom).'</label></td>';
    $r .= '<td><select name="'.$nom.'" title="'._T('acs:bordlargeur').'" class="forml" style="width: auto">'.
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
  function ctlStyleBord($nom, $style='solid', $param) {
    $r = '<div align="'.$GLOBALS['spip_lang_right'].'"><table><tr>';
    if ($param['label'] != 'non') $r .= '<td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$nom.'" title="'.$nom.'" class="label">'._T($nom).'</label></td>';
    $r .= '<td><select name="'.$nom.'" title="'._T('acs:bordstyle').'" class="forml" style="width: auto">'.
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

  // Choix de valeur, avec + / - (todo)
  function ctlNombre($nom, $nombre=0, $param) {
    return '<table><tr><td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$nom.'" title="'.$nom.'"  class="label">'._T($nom).'</label></td><td><input type="text" name="'.$nom.'" size="8" maxlength="6" class="forml" value="'.$nombre.'" style="text-align:'.$GLOBALS['spip_lang_right'].'" /></td></tr></table>';
  }

  // Saisie d'un texte
  function ctlText($nom, $txt, $param = array('taille' => 30)) {
    return '<table width="100%"><tr><td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$nom.'" title="'.$nom.'"  class="label">'._T($nom).'</label></td><td><input type="text" name="'.$nom.'" size="'.$param['taille'].'" maxlength="'.$param['taille'].'" class="forml" value="'.$txt.'" /></td></tr></table>';
  }

  // Saisie d'un texte long
  function ctlTextarea($nom, $txt, $param) {
    return '<div align="'.$GLOBALS['spip_lang_left'].'"><label for "'.$nom.'" title="'.$nom.'">'._T($nom).'</label><textarea name="'.$nom.'" class="forml" rows="'.(isset($param['lines']) ? $param['lines']-1 : 2).'">'.$txt.'</textarea></div>';
  }

  // Choix oui / non
  function ctlChoix($nom, $value, $param) {
    if (!is_array($param['option'])) return 'Pas d\'options pour '.$nom;
    $r = '<table><tr valign="bottom"><td align="'.$GLOBALS['spip_lang_right'].'"><label for "'.$nom.'" title="'.$nom.'" class="label">'._T($nom).'</label></td><td>';
    foreach($param['option'] as $option) {
      $r .= bouton_radio($nom, $option, '<label for "'.$nom.'" title="'.$nom.ucfirst($option).'" class="label">'._T($nom.ucfirst($option)).'</label>', $value == $option);
    }
    $r .= '</td></tr></table>';
    return $r;
  }

  // Choix d'un composant
  function ctlWidget($nom, $value, $param ) {

  require_once(_DIR_PLUGIN_ACS.'lib/composant/composants_variables.php');
  $vars = composants_variables();

    if (!is_array($param['composant'])) return _T('acs:err_aucun_composant').' '.$nom.'<br />';

    $r = '<table><tr><td><label for "'.$nom.'" title="'.$nom.'"  class="label">'._T($nom).'</label></td><td><div id="'.$nom.'" class="ctlWidget">';
    $r .= '<select id="select_'.$nom.'" name="'.$nom.'" class="forml select_widget">';
    $r .= '<option value=""'.($value=='' ? ' selected' : '').'> </option>';

    foreach($param['composant'] as $c) {
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

  function ctlHidden($nom, $value, $param) {
    return '<input type="hidden" name="'.$nom.'" value="'.$value.'" />';
  }
}

// http://doc.spip.org/@bouton_radio
function bouton_radio2($nom, $valeur, $titre, $actif = false, $onClick="", $enable=true) {
  static $id_label = 0;

  if (strlen($onClick) > 0) $onClick = " onclick=\"$onClick\"";
  $texte = "<input type='radio' name='$nom' value='$valeur' id='label_$id_label'$onClick";
  if ($actif) {
    $texte .= ' checked="checked"';
    $titre = '<b>'.$titre.'</b>';
  }
  $texte .= ($enable ? '' : ' disabled')." /> <label for='label_$id_label'>$titre</label>\n";
  $id_label++;
  return $texte;
}

function acs_creer_chemin($c) {
  $dir = _DIR_RACINE.$c;

  if (is_readable($dir))
    return true;
  else
    return mkdir_recursive($dir);
}

/**
 * Makes directory, returns TRUE if exists or made
 *
 * @param string $pathname The directory path.
 * @return boolean returns TRUE if exists or made or FALSE on failure.
 */

function mkdir_recursive($pathname)
{
    is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname));
    return is_dir($pathname) || @mkdir($pathname);
}

// Convertit la version code de spip en version texte
// Le tableau ne contient que les versions de spip compatibles ET testées avec cette version d'ACS
function spip_version_texte($version_code) {
  $v = $GLOBALS['acs_table_versions_spip'];

  if (isset($v[$version_code]))
    return $v[$version_code];
  else
    return $version_code;
}
?>