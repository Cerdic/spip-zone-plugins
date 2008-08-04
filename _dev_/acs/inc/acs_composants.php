<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Page composant
 */
include_spip('lib/composant/classComposantPrive');

function composants_gauche($c) {
	return acs_box(_T('acs:composant'), $c->gauche(), $c->icon, false, '<img src="'._DIR_PLUGIN_ACS.'/img_pack/info.png" />');
}

function composants($c) {
	include_spip('lib/composant/composants_liste');
	
	if(isset($GLOBALS['meta']['acsSqueletteOverACS']) && $GLOBALS['meta']['acsSqueletteOverACS']) {
		$in_path = find_in_path('composants/'.$c->type);
		if (!(strpos($in_path, $GLOBALS['meta']['acsSqueletteOverACS']) === false) && (strpos($in_path, _DIR_PLUGIN_ACS.'models/') === false))
			$over = '<img src="'._DIR_PLUGIN_ACS.'img_pack/over.gif" alt="over" title="'._T('acs:squelette').' '.$GLOBALS['meta']['acsSqueletteOverACS'].'" />';
	}
	$nom = $c->T('nom');
	if ($nom == $c->type.' nom') $nom = ucfirst($c->type);
	$r .= acs_box(
		'<table width="100%"><tr><td width="99%">'.$nom.'</td><td>'.$over.'</td></tr></table>',
			'<form id="acs" name="acs" action="?exec=acs&onglet=composants" method="post">'.
				$c->edit().
				'<table width="100%"><td valign="bottom"><div style="text-align:'.$GLOBALS['spip_lang_right'].';">'.
				'<input type="submit" class="crayon-submit fondo" name="'._T('bouton_valider').'" value="'._T('bouton_valider').'">'.
				'</div></td></tr></table>'.
				'</form>',
				$c->icon
				);
	$r .='<br /><a name="cTrad"></a><div id="cTrad"></div>'; // Container for translations - Ajax
	return $r;
}
?>