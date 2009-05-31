<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt

//include_spip('inc/acs_presentation');//(implicite)
include_spip('lib/composant/composants_variables');

function acs_vars_gauche(){
	return acs_info_box(_T('acs:variables'), _T('acs:toutes_les_variables'), $help, $info, _DIR_PLUGIN_ACS.'/images/vars-24.gif');
  return acs_box(_T('acs:variables'), _T('acs:toutes_les_variables'), false, false, '<img src="'._DIR_PLUGIN_ACS.'/images/info.png" />');
}

function acs_vars() {
  return acs_page_get_all_variables();
}

function acs_vars_droite() {
  return acs_box(_T('acs:variables'), 'thème : todo', _DIR_PLUGIN_ACS."images/acs_32x32.gif", false);
}

function acs_page_get_all_variables() {

  $cv = composants_variables();
  ksort($cv);
  //print_r($cv);
  $r .= '<table frame="void" rules="cols" cellpadding="2" cellspacing="0" style="width: 100%; border: '.$GLOBALS['couleur_claire'].' 0 groove;">';
  $r .= '<tr style="text-align: center; border-bottom: '.$GLOBALS['couleur_claire'].' thin groove;"><th class="verdana1"><b>'._T('nom')."</b></th>\n<th class='verdana1'><b>"._T('acs:valeur')."</b></th>\n</tr>\n";
  foreach($cv as $c=>$p) {
		foreach($p['vars'] as $var=>$vp) {
			$r .= composant_variable($c, $var);
		}
  	foreach(composant_instances($c) as $nic) {
  		foreach($p['vars'] as $var=>$vp) {
				$r .= composant_variable($c, $var, $nic);
  		}
			$r .= '<tr><td colspan="2" style=" height: 1px; padding: 0; background-color: '.$GLOBALS['couleur_claire'].';"></td></tr>';
  	}
  	$r .= '<tr><td colspan="2" style=" height: 1px; padding: 0; background-color: '.$GLOBALS['couleur_claire'].';"></td></tr>';
  }
  $r .= '</table>';  
  return acs_box(_T('acs:toutes_les_variables'), $r, _DIR_PLUGIN_ACS.'/images/vars-24.gif');
}

function composant_variable($c, $v, $nic=false) {
  static $i;
  $bgcolor = alterner($i++, '#eeeeee','white');
  
  if (substr($v, 0, 6) == '#over#') {
    $v = substr($v, 6);
    $before = '<u>';
    $after = '</u>';
  }
  else {
    $before = '';
    $after = '';
  }
  $varname = 'acs'.ucfirst($c).$nic.$v;
  return '<tr style="background: '.$bgcolor.'; vertical-align: top;"><td class="verdana2">'.
  '<a href="?exec=acs&onglet=composants&composant='.$c.($nic ? '&nic='.$nic : '').'" class="nompage">'.
  $before.'<span style="color:#8d8d8f">acs'.ucfirst($c).$nic.'</span>'.$v.$after.'</a></td>'.
  '<td class="crayon var-'.$c.'_'.$v.'-'.($nic ? $nic : 0).' type_pinceau crayon_'.$c.$v.' arial2">'.
  (isset($GLOBALS['meta'][$varname]) ? couper(htmlspecialchars($GLOBALS['meta'][$varname]), 150) : '<span style="color:darkviolet">'._T('acs:undefined').'</span>').
  '</td></tr>';
}
?>