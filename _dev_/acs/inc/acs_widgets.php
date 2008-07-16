<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

require_once _DIR_ACS.'lib/composant/composants_actifs.php';

// Affiche une liste contextuelle des composants - Onglets pages, page ET composants
// Show a contextual widget list - Used by pages AND composants
function liste_widgets($composants, $islink=false) {
  if (!is_array($composants)) return false;

  $r .= '<div id="widgets" class="widgets">';
  sort($composants);
  foreach($composants as $widget) {
    if ($widget == '') {
      $r .= '<hr style="margin-bottom: 2px"/>';
    }
    else {
      $widget_icon = find_in_path('composants/'.$widget.'/img_pack/'.$widget.'_icon.gif');
      if (!file_exists($widget_icon))
        $widget_icon = _DIR_PLUGIN_ACS.'img_pack/composant-24.gif';
      $link = '<a href="?exec=acs&amp;onglet=composants&amp;composant='.$widget.'" title="'._T('composant').'">';
      $r .= '<div id="'.$widget.'" class="'.get_widget_class($widget, 'widget').'">'.
        ($islink ? $link : '').
        '<table><tr><td>'.
        ($islink ? '' : $link).
        '<img src="'.$widget_icon.'" />'.
        ($islink ? '' : '</a>').
        '</td><td title="'.ucfirst($widget).'" style="padding-left: 5px; padding-right: 5px; width: 95%;"><div style="overflow:hidden; text-align:center">'.ucfirst(str_replace('_', ' ', $widget)).'</div></td></tr></table>'.
        ($islink ? '</a>' : '').
      '</div>';
    }
  }
  $r .= '</div>';
  return $r;
}

function get_widget_class($widget, $class) {
  $ov .= $class;
  if(isset($GLOBALS['meta']['acsSqueletteOverACS']) && $GLOBALS['meta']['acsSqueletteOverACS']) {
    $in_path = find_in_path('composants/'.$widget);
    $tas = explode(':', $GLOBALS['meta']['acsSqueletteOverACS']);
    foreach($tas as $dir) {
      if (!(strpos($in_path, $dir) === false) && (strpos($in_path, _DIR_PLUGIN_ACS.'models/') === false))
        $ov .= ' '.$class.'_overriden';
      break;
    }
  }
  if (!in_array($widget, composants_actifs()))
      $ov .= ' '.$class.'_unused';
  return $ov;
}
?>