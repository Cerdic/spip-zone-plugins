<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

function controleurs_var_dist($regs) {
	global $spip_lang;
	include_spip('inc/filtres');
	include_spip('inc/composant/classControles');
	include_spip('inc/composant/composants_variables');

	$cv = composants_variables();
	list(,$crayon,$type,$champ,$id) = $regs;
	$v = explode('_', $champ);
	$c = $v[0]; // composant
	$v = $v[1]; // variable
	$val = $GLOBALS['meta']['acs'.ucfirst($c).($id ? $id : '').$v];
	$type = $cv[$c]['vars'][$v]['type'];

  // il faut passer champ=>source pour les comparaisons dans action/crayons_store
  $crayon = new SecureCrayon("var-".$champ."-".$id, array($champ => $val));

	$ctlName = 'ctl'.ucfirst($type);
	if (class_exists($ctlName))
    $ctl = new $ctlName($c, ($id ? $id : ''), $v, $val, $cv[$c]['vars'][$v], $crayon->key);
  else
    $ctl = new ctlTextarea($c, ($id ? $id : ''), $v, $val, $cv[$c]['vars'][$v], $crayon->key); // On dessine un controle Textarea si le type est inconnu

	$html .= '<div class="acsVarControleur" style="width:'.($crayon->w + 5).'px; height:auto; font-size: '._request('font-size').';">'.
		'<form id="acs" name="acs" class="formulaire_crayon" action="?action=crayons_var_store" method="post">'.
		$crayon->code().
		'<input type="hidden" name="oldval_'.$crayon->key.'" value="'.htmlentities($val).'" />'.
		'<input type="hidden" name="type_'.$crayon->key.'" value="'.$type.'" />'.
		'<input type="hidden" name="var_mode" value="recalcul" />'.
		$ctl->draw().
		'<div style="height:5px"/>'.
		crayons_boutons().'</form></div>'.
		'<script language="javascript">
try {init_palette();}
catch(e) {}
</script>';
	return array($html, NULL);
}
?>