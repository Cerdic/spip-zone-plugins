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
  	foreach($cp['instances'] as $nic=>$c) {
      $wicon = find_in_path('composants/'.$class.'/images/'.$class.'_icon.gif');
      if (!file_exists($wicon))
        $wicon = _DIR_PLUGIN_ACS.'images/composant-24.gif';
  		$r .= '<div id="'.$class.($nic ? '#'.$nic : '').'" class="'.get_widget_class($cp['over'], $c['on'], 'widget').'">'.
        '<table><tr><td><a href="'._DIR_RESTREINT.'?exec=acs&amp;onglet=composants&amp;composant='.$class.($nic ? '&amp;nic='.$nic : '').'" title="'._T('composant').'"><img src="'.$wicon.'" style="width:20px;height:20px;vertical-align:middle" /></a>'.
        '</td><td title="'.ucfirst($class).'" style="padding-left: 5px; padding-right: 5px; width: 95%;"><div style="overflow:hidden; text-align:center">'.ucfirst(str_replace('_', ' ', $class)).($nic ? ' '.$nic : '').'</div></td></tr></table>'.
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
  if (!$on == 'oui')
		$ov .= ' '.$style.'_unused';
  return $ov;
}
?>