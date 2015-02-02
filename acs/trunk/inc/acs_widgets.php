<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2012
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Affiche une liste contextuelle des composants - Onglet composants
 * avec un filtrage par groupes de composants
 * 
 * Show a contextual widget list - Used by pages AND composants
 */
function liste_widgets($visible = true) {
	$composants = composants_liste();
	// On renvoie directement une liste vide si aucun composant n'est trouvé
	if (!is_array($composants))
		return acs_box('0 '.strtolower(_T('acs:composants')), '&nbsp;', _DIR_PLUGIN_ACS."/images/composant-24.gif", 'acs_box_composants');

  $chtml = array();
  foreach($composants as $class=>$cp) {
  	$vp = 'acs'.ucfirst($class);
  	$group = $cp['group'];
  	foreach($cp['instances'] as $nic=>$c) {
  		$vpi = $vp.($nic ? $nic : '');
      // Si le composant possede une variable Nom on l'affiche en nom et le nom du composant en info-bulle
      $v = $vpi.'Nom';
      if ($GLOBALS['meta'][$v]) {
      	$nom = couper($GLOBALS['meta'][$v], 50);
      	$title = ucfirst(str_replace('_', ' ', $class)).($nic ? ' '.$nic : '');
      }
      else {
      	$nom = ucfirst(str_replace('_', ' ', $class)).($nic ? ' '.$nic : '');
      	$title = _T('acs:composant').' '.$nom;
      }
  		$html = '<div id="widget_'.$class.($nic ? '-'.$nic : '').'" class="'.get_widget_class($cp['over'], $c['on'], 'widget').'">'.
        '<table><tr><td><a href="'._DIR_RESTREINT.'?exec=acs&amp;onglet=composants&amp;composant='.$class.($nic ? '&amp;nic='.$nic : '').'" title="'._T('acs:composant').'">'.widget_icon($class, $nic).'</a>'.
        '</td><td title="'.$title.'" style="width: 95%;"><div><a href="'._DIR_RESTREINT.'?exec=acs&amp;onglet=composants&amp;composant='.$class.($nic ? '&amp;nic='.$nic : '').'" title="'.$title.'">'.$nom.'</a></div></td></tr></table>'.
      '</div>';
  		$chtml[$group][] = $html;
  		$nbci++;
  	}
  	$nbc++;
  }
  $menuh = '<div class="widgets_filter_bar"><a class="grclbl" name="gr_all" title="all"><img src="'.find_in_path('images/composant_all.gif').'" alt="all"></a>'; // Affichage par script apres masquage par la CSS
  $menuc = '';
  foreach($chtml as $g => $grp) {
  	$menuh .= '<a class="grclbl" name="gr_'.$g.'" title="'.($g != '' ? $g : 'autres').'"><img src="'.find_in_path('images/composant_'.$g.'.gif').'" alt="'.$g.'"></a>';
  	$menuc .= '<ul class="grclist gr_'.$g.'">';
    foreach($grp as $cnt) {
  		$menuc .= '<li>'.$cnt.'</li>';
  	}
  	$menuc .= '</ul>';
  }
  $menuh .= '</div>';
  $r = $menuh.'<div id="widgets" class="widgets">'.$menuc.'</div>';
  return acs_box($nbci.' '.(($nbci==1) ? strtolower(_T('composant')) : strtolower(_T('composants'))).' ('.$nbc.')', $r, _DIR_PLUGIN_ACS."/images/composant-24.gif", 'acs_box_composants'.($visible ? '' : ' acs_box_composants_hidden').'');
}

function get_widget_class($over, $on, $style) {
  $ov .= $style;
  if ($over)
  	$ov .= ' '.$style.'_overriden';
  if (!($on == 'oui'))
		$ov .= ' '.$style.'_unused';
  return $ov;
}

function widget_icon($class, $nic, $size=24) {
	$o = 'acs'.ucfirst($class).($nic ? $nic : '').'Orientation';
	// Si le composant possède une propriete orientation ET une icone correspondante on oriente l'icone 
	$wicon = (isset($GLOBALS['meta'][$o]) && $GLOBALS['meta'][$o] == 'horizontal') ? 'horizontal' : 'icon';
  $wicon = find_in_path('composants/'.$class.'/images/'.$class.'_'.$wicon.'.gif');
  if (!file_exists($wicon))
    $wicon = _DIR_PLUGIN_ACS.'images/composant-24.gif';
	return '<img src="'.$wicon.'" height="'.$size.'px" width="'.$size.'px" />';
}
?>