<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Affiche une liste contextuelle des composants - Onglet composants
 * Show a contextual widget list - Used by pages AND composants
 */
function liste_widgets() {
	$composants = composants_liste();
	
	// On renvoie directement une liste vide si aucun composant n'est trouvé
	if (!is_array($composants)) return acs_box('0 '.strtolower(_T('composants')), '&nbsp;', _DIR_PLUGIN_ACS."/images/composant-24.gif", 'acs_box_composants');

   $r .= '<div id="widgets" class="widgets">';
  foreach($composants as $class=>$cp) {
  	$vp = 'acs'.ucfirst($class);
  	foreach($cp['instances'] as $nic=>$c) {
  		$vpi = $vp.($nic ? $nic : '');
  		// Si le composant possède une propriete orientation ET une icone correspondante on oriente l'icone 
  		$wicon = (isset($GLOBALS['meta'][$vpi.'Orientation']) && $GLOBALS['meta'][$vpi.'Orientation'] == 'horizontal') ? 'horizontal' : 'icon';
      $wicon = find_in_path('composants/'.$class.'/images/'.$class.'_'.$wicon.'.gif');
      if (!file_exists($wicon))
        $wicon = _DIR_PLUGIN_ACS.'images/composant-24.gif';
      // Si le composant possede une variable Comment on l'affiche en info-bulle
      $v = $vpi.'Comment';
      $title = $GLOBALS['meta'][$v] ? $GLOBALS['meta'][$v] : _T('composant');
  		$r .= '<div id="'.$class.($nic ? '-'.$nic : '').'" class="'.get_widget_class($cp['over'], $c['on'], 'widget').'">'.
        '<table><tr><td><a href="'._DIR_RESTREINT.'?exec=acs&amp;onglet=composants&amp;composant='.$class.($nic ? '&amp;nic='.$nic : '').'" title="'._T('composant').'"><img src="'.$wicon.'" /></a>'.
        '</td><td title="'.$title.'" style="width: 95%;"><div><a href="'._DIR_RESTREINT.'?exec=acs&amp;onglet=composants&amp;composant='.$class.($nic ? '&amp;nic='.$nic : '').'" title="'.$title.'">'.ucfirst(str_replace('_', ' ', $class)).($nic ? ' '.$nic : '').'</a></div></td></tr></table>'.
      '</div>';
  		$nbci++;
  	}
  	$nbc++;
  }
  $r .= '</div>';
  return acs_box($nbc.' '.(($nbc==1) ? strtolower(_T('composant')) : strtolower(_T('composants'))).' ('.$nbci.')', $r, _DIR_PLUGIN_ACS."/images/composant-24.gif", 'acs_box_composants');
}

function get_widget_class($over, $on, $style) {
  $ov .= $style;
  if ($over)
  	$ov .= ' '.$style.'_overriden';
  if (!($on == 'oui'))
		$ov .= ' '.$style.'_unused';
  return $ov;
}
?>