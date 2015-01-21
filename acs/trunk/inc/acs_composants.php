<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2012
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Page composant
 */
include_spip('inc/composant/classComposantPrive');

function composants_gauche($c) {
	return acs_box(_T('acs:composant'), $c->gauche(), $c->icon, false, '<img src="'._DIR_PLUGIN_ACS.'/images/info.png" />');
}

function composants($c) {
	include_spip('inc/composant/composants_liste');
	$cl = composants_liste();	
	if ($cl[$c->class]['over'])
		$over = '<img src="'._DIR_PLUGIN_ACS.'images/over.gif" alt="over" title="'._T('ecrire:icone_squelette').' '.$GLOBALS['meta']['acsSqueletteOverACS'].'" />';

	$nom = $c->T('nom');
	if ($nom == $c->class.' nom') $nom = ucfirst($c->class);
	
	// Si le composant est instanciable, on affiche de quoi gérer l'instanciation
	if ($c->instanciable)
		$instances = '<td>'.instance_create($c).'</td><td>'.instance_select($c).'</td>';
	else
		$instances = '';

	$help = $c->help();

	// Affichage de la box de gestion du composant
	$r .= acs_box(
				'<table width="100%"><tr><td width="99%">'.$nom.' '.$c->nic.'</td>'.$instances.'<td>'.$over.'</td>'.($help ? '<td>'.acs_help_call($c->class.'_help').'</td>' : '').'</tr></table>',
	      ($help ? acs_help_div($c->class.'_help', $help) : '').
				'<form id="acs" name="acs" action="?exec=acs&onglet=composants&composant='.$c->class.'" method="post">'.
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

function instance_select($c) {
  $instances = composant_instances($c->class);
  if (is_array($instances) && count($instances) > 1) {
  	$vp = 'acs'.ucfirst($c->class);
    $r ='<select name="nic" onchange=submit()>';
    foreach($instances as $id) {
    	$v = $vp.$id.'Nom';
    	$title = $GLOBALS['meta'][$v] ? ' title="'.$GLOBALS['meta'][$v].'"' : '';
      $r .= '<option value="'.$id.'"'.($id==$c->nic ? ' selected': '').$title.'>'.$id.'</option>';
    }
    $r .='</select>';
    $r .= "<input type='hidden' name='exec' value='acs' />".
		'<input type="hidden" name="composant" value="'.$c->class.'" />'.
		'<input type="hidden" name="onglet" value="composants" />';
    $r = '<td><form action="">'.$r.'<noscript></td><td><input type="submit" value="'._T('bouton_valider').'"></noscript></form></td>';
    $r .= '<td>'.instance_delete($c).'</td>';
  }
  return $r;
}

function instance_create($c) {
		return '<form action="" id="form_instance_create" onSubmit="return instance_create(\''.$c->nextInstance().'\');"></td><td><noscript><input type="text" name="nic" size="4" maxlength="4" /></td><td></noscript><input type="image" src="'._DIR_PLUGIN_ACS.'images/composant-creer.gif" title="'._T('acs:creer_composant').'" />
					<input type="hidden" name="exec" value="acs" />
					<input type="hidden" name="onglet" value="composants" />
					<input type="hidden" name="composant" value="'.$c->class.'" />
					</form>';
}

function instance_delete($c) {
	$msg = str_replace("'", "\'", _T('acs:del_composant_confirm', array('c' => $c->class, 'nic' => $c->nic)));
	return '<form action="" onSubmit="return instance_delete(\''.$msg.'\');"><input type="image" src="'._DIR_PLUGIN_ACS.'images/composant-del.gif" title="'._T('acs:del_composant').'" />
					<input type="hidden" name="exec" value="acs" />
					<input type="hidden" name="onglet" value="composants" />
					<input type="hidden" name="composant" value="'.$c->class.'" />
					<input type="hidden" name="nic" value="'.$c->nic.'" />
					<input type="hidden" name="del_composant" value="delete" />
					</form>';
}
?>