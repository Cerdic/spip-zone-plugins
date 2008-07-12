<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/acs_presentation');
include_spip('lib/composant/composants_variables');

function acs_page_get_all_variables() {

  $cv = composants_variables();
  ksort($cv);
  $r .= "\n<table cellpadding='3' cellspacing='1' border='0' width=\"100%\">";
  $r .= "\n<tr style='background: ".$GLOBALS['couleur_foncee']."; color:white;'><td class='verdana1'><b>"._T('acs:variable')."</b></td>\n<td class='verdana2'><b>"._T('acs:valeur')."</b></td>\n</tr>\n";

  foreach($cv as $v=>$c) {
    if (substr($v, 0, 6) == '#over#') {
      $v = substr($v, 6);
      $before = '<u>';
      $after = '</u>';
    }
    else {
      $before = '';
      $after = '';
    }
    if ($last && ($last != $c['composant'])) $r .= '<tr><td colspan="2" style=" height: 1px; padding: 0; background-color: '.$GLOBALS['couleur_claire'].'"></td></tr>';
    $bgcolor = alterner($i++, '#eeeeee','white');
    $r .= '<tr style="background: '.$bgcolor.'; vertical-align: top;"><td class="verdana2"><a href="?exec=acs&onglet=composants&composant='.$c['composant'].'" class="nompage" style="font-size: 1.2em">'.$before.$v.$after.'</a></td><td class="arial2">'.(isset($GLOBALS['meta']['acs'.$v]) ? couper(htmlspecialchars($GLOBALS['meta']['acs'.$v]), 150) : '<span style="color:darkviolet">'._T('acs:undefined').'</span>').'</td></tr>';
    $last = $c['composant'];
  }
  $r .= '</table>';
  return acs_box('', $r);
}
?>