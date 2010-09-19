<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
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

function acs_page_get_all_variables() {

  $cv = composants_variables();

  $r .= '<table frame="void" rules="cols" cellpadding="2" cellspacing="0" style="width: 100%; border: '.$GLOBALS['couleur_claire'].' 0 groove;">';
  $r .= '<tr style="text-align: center; border-bottom: '.$GLOBALS['couleur_claire'].' thin groove;"><th class="verdana1"><b>'._T('nom')."</b></th>\n<th class='verdana1'><b>"._T('acs:valeur')."</b></th>\n</tr>\n";
  foreach($cv as $c=>$p) {
		foreach($p['vars'] as $var=>$vp) {
			$r .= affiche_composant_variables($c, $var, false, $vp);
		}
  	foreach(composant_instances($c) as $nic) {
  		foreach($p['vars'] as $var=>$vp) {
				$r .= affiche_composant_variables($c, $var, $nic, $vp);
  		}
			$r .= '<tr><td colspan="2" style=" height: 1px; padding: 0; background-color: '.$GLOBALS['couleur_claire'].';"></td></tr>';
  	}
  	$r .= '<tr><td colspan="2" style=" height: 1px; padding: 0; background-color: '.$GLOBALS['couleur_claire'].';"></td></tr>';
  }
  $r .= '</table>';  
  return acs_box(_T('acs:toutes_les_variables'), $r, _DIR_PLUGIN_ACS.'/images/vars-24.gif');
}

function affiche_composant_variables($c, $v, $nic, $vp) {
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
	if ($vp['type'] == 'bord')
		$v2 = array($varname.'Color', $varname.'Width', $varname.'Style');
	else 
		$v2 = array($varname);
	$r = '<td class="verdana2"><a href="?exec=acs&onglet=composants&composant='.$c.($nic ? '&nic='.$nic : '').'" class="nompage">'.
      $before.'<span style="color:#8d8d8f">acs'.ucfirst($c).$nic.'</span>'.$v.$after.'</a></td>'.
      '<td class="crayon var-'.$c.'_'.$v.'-'.($nic ? $nic : 0).' type_pinceau crayon_'.$c.$v.' arial2">';
  foreach($v2 as $vn) {
		$r .= (isset($GLOBALS['meta'][$vn]) ? couper(htmlspecialchars($GLOBALS['meta'][$vn]), 150).' ' : '<span style="color:darkviolet">'._T('acs:undefined').'</span> ');
  }
  $r .= '</td>';
  return '<tr style="background: '.$bgcolor.'; vertical-align: top;">'.$r.'</tr>';
}
?>