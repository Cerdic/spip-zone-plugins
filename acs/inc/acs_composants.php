<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Page composant
 */
include_spip('lib/composant/classComposantPrive');

function composants_gauche($c) {
	return acs_box(_T('acs:composant'), $c->gauche(), $c->icon, false, '<img src="'._DIR_PLUGIN_ACS.'/images/info.png" />');
}

function composants($c) {
	include_spip('lib/composant/composants_liste');
	
	if(isset($GLOBALS['meta']['acsSqueletteOverACS']) && $GLOBALS['meta']['acsSqueletteOverACS']) {
		$in_path = find_in_path('composants/'.$c->type);
		if (!(strpos($in_path, $GLOBALS['meta']['acsSqueletteOverACS']) === false) && (strpos($in_path, _DIR_PLUGIN_ACS.'models/') === false))
			$over = '<img src="'._DIR_PLUGIN_ACS.'images/over.gif" alt="over" title="'._T('acs:squelette').' '.$GLOBALS['meta']['acsSqueletteOverACS'].'" />';
	}
	$nom = $c->T('nom');
	if ($nom == $c->type.' nom') $nom = ucfirst($c->type);
	$r .= acs_box(
				'<table width="100%"><tr><td width="99%">'.$nom.' '.$c->nic.'</td><td>'.$over.'</td>'.composant_instances_select($c->type, $c->nic).'</tr></table>',
				'<form id="acs" name="acs" action="?exec=acs&onglet=composants" method="post">'.
				$c->edit().
				'<table width="100%"><td valign="bottom"><div style="text-align:'.$GLOBALS['spip_lang_right'].';">'.
				'<input type="submit" class="fondo" name="'._T('bouton_valider').'" value="'._T('bouton_valider').'">'.
				'</div></td></tr></table>'.
				'</form>',
				$c->icon
				);
	$r .='<br /><a name="cTrad"></a><div id="cTrad"></div>'; // Container for translations - Ajax
	return $r;
}

function composants_droite($c) {
  $choixComposants = array_keys(composants_liste());
  if (is_array($choixComposants))
    $l = liste_widgets($choixComposants, true);
  else
    $l = '&nbsp;';
  return acs_box(count($choixComposants).' '.((count($choixComposants)==1) ? strtolower(_T('composant')) : strtolower(_T('composants'))), $l, _DIR_PLUGIN_ACS."/images/composant-24.gif", 'acs_box_composants');
}

function composant_instances_select($c, $nic) {
  include_spip('lib/composant/composants_variables');
  $instances = composant_instances($c);
  if (is_array($instances) && count($instances)) {
    sort($instances);
    $r ='<select name="nic" onchange=submit()>';
    $r .= '<option value=""'.($id=="" ? ' selected': '').'></option>';
    foreach($instances as $id) {
      $r .= '<option value="'.$id.'"'.($id==$nic ? ' selected': '').'>'.$id.'</option>';
    }
    $r .='</select>';
    $r .= "<input type='hidden' name='exec' value='acs' />".
		'<input type="hidden" name="composant" value="'.$c.'" />'.
		'<input type="hidden" name="onglet" value="composants" />';
    $r = '<td><form action="">'.$r.'<noscript></td><td><input type="submit" value="'._T('bouton_valider').'"></noscript></form></td>';
  }
  return $r;
}
?>