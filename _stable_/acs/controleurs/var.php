<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2009
# Copyleft: licence GPL - Cf. LICENCES.txt

function controleurs_var_dist($regs) {
	global $spip_lang;
	include_spip('inc/filtres');
	include_spip('lib/composant/controles');
	include_spip('lib/composant/composants_variables');

	$cv = composants_variables();
	list(,$crayon,$type,$champ,$id) = $regs;
	$val = $GLOBALS['meta']['acs'.$champ];
	$nom = preg_replace('/'.$id.'/', '', $champ, 1);
	$composant = $cv[$nom]['composant'];
	$draw = 'ctl'.ucfirst($cv[$nom]['type']);
	// On dessine un controle Textarea si le type est inconnu
	if (!is_callable($draw))
		$draw = 'ctlTextarea';
	// il faut passer champ=>source pour les comparaisons dans action/crayons_store
	$crayon = new SecureCrayon("var-".$champ."-".$id, array($champ => $val));
	$ctl = $draw($composant, ($id ? $id : ''), substr($nom, strlen($composant)), $val, $cv[$champ], $crayon->key);
	$html .= '<div class="controle" style="width:'.$crayon->w.'px; height:'.$crayon->h.'px; font-size: '._request('font-size').';">'.
		'<form id="acs" name="acs" class="formulaire_crayon" action="?action=crayons_var_store" method="post">'.
		$crayon->code().
		'<input type="hidden" name="oldval_'.$crayon->key.'" value="'.htmlentities($val).'" />'.
		'<input type="hidden" name="var_mode" value="recalcul" />'.
		$ctl.'<div style="height:5px"/>'.
		crayons_boutons().'</form></div>'.
		'<script language="javascript">
try {init_palette();}
catch(e) {}
</script>';
	return array($html, NULL);
}
?>