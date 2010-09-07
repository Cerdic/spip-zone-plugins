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
				'<table width="100%"><tr><td width="99%">'.$nom.' '.$c->nic.'</td><td>'.$over.'</td><td>'.instance_create($c->type).'</td><td>'.instance_select($c->type, $c->nic).'</td></tr></table>',
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
	return liste_widgets();
}

function instance_select($c, $nic) {
  $instances = composant_instances($c);
  if (is_array($instances) && count($instances)) {
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
    $r .= '<td>'.instance_delete($c->type, $c->nic).'</td>';
  }
  return $r;
}

function instance_create($c) {
	return '<form action=""></td><td><noscript><input type="text" name="newnic" size="4" maxlength="4" /></td><td></noscript><input type="image" src="'._DIR_PLUGIN_ACS.'images/composant-creer.gif" title="'._T('acs:creer_composant').'" />
					<input type="hidden" name="exec" value="acs" />
					<input type="hidden" name="onglet" value="composants" />
					<input type="hidden" name="composant" value="'.$c.'" />
					</form>';
}

function instance_delete($c, $nic) {
	return '<form action=""><input type="image" src="'._DIR_PLUGIN_ACS.'images/composant-del.gif" title="'._T('acs:del_composant').'" />
					<input type="hidden" name="exec" value="acs" />
					<input type="hidden" name="onglet" value="composants" />
					<input type="hidden" name="composant" value="'.$c.'" />
					<input type="hidden" name="nic" value="'.$nic.'" />
					</form>';
}
?>