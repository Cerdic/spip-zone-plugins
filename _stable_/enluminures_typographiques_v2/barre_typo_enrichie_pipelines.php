<?php
define('_DIR_BTE_IMG', _DIR_PLUGIN_TYPOENLUMINEE.'/img_pack/icones_barre/');

// toolbox pour les paragraphes
function afficher_boutonsavances($champ, $champhelp, $num_barre) {
	$reta = '';
	$reta .= bouton_barre_racc("barre_raccourci('\n\n{{{','}}}\n\n',$champ, $num_barre)", _DIR_IMG_ICONES_BARRE."intertitre.png", _T('barre_intertitre'), $champhelp);
	$reta .= bouton_barre_racc("barre_raccourci('\n\n{2{','}2}\n\n',$champ, $num_barre)", _DIR_BTE_IMG.'intertitre2.png', _T('enlumtypo:barre_intertitre2'), $champhelp);
	$reta .= bouton_barre_racc("barre_raccourci('\n\n{3{','}3}\n\n',$champ, $num_barre)", _DIR_BTE_IMG.'intertitre3.png', _T('enlumtypo:barre_intertitre3'), $champhelp);
	$reta .= '&nbsp;'.bouton_barre_racc("barre_raccourci('[|','|]',$champ, $num_barre)", _DIR_BTE_IMG.'center.png', _T('enlumtypo:barre_centrer'), $champhelp);
	$reta .= bouton_barre_racc("barre_raccourci('[/','/]',$champ, $num_barre)", _DIR_BTE_IMG.'right.png', _T('enlumtypo:barre_alignerdroite'), $champhelp);
	$reta .= '&nbsp;'.bouton_barre_racc("barre_raccourci('[(',')]',$champ, $num_barre)", _DIR_BTE_IMG.'cadretexte.png', _T('enlumtypo:barre_encadrer'), $champhelp);
	$reta .= '&nbsp;'.bouton_barre_racc("barre_raccourci('<del>','</del>',$champ, $num_barre)", _DIR_BTE_IMG.'text_strikethrough.png', _T('enlumtypo:barre_barre'), $champhelp);
		
	$tableau_formulaire = '
<table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">
  <tr class="spip_barre">
    <td>'._T('enlumtypo:barre_avances').'</td>
    <td>'.$reta.'
    </td>
  </tr> 
</table>
';
	if (function_exists('lire_config')) {
		if (lire_config('bte/defaultbarrestyle','close') == "open")
			return str_replace( "style='display: none;'>", " style='display: block;'>", produceWharf('tableau_boutonsavances','',$tableau_formulaire));
		else
			return produceWharf('tableau_boutonsavances','',$tableau_formulaire);
	} else
		return produceWharf('tableau_boutonsavances','',$tableau_formulaire);
}

function TypoEnluminee_BT_toolbox($params) {
	$params['flux'] .= afficher_boutonsavances($params['champ'], $params['help'], $params['num']);
	return $params;
}

function TypoEnluminee_BT_caracteres($params) {
	$params['flux'] .= bouton_barre_racc("barre_raccourci('[*','*]',$params[champ], $params[num])", _DIR_BTE_IMG.'miseenevidence.png', _T('enlumtypo:barre_miseenevidence'), $params['help'])
		. bouton_barre_racc("barre_raccourci('&lt;sup&gt;','&lt;/sup&gt;',$params[champ], $params[num])", _DIR_BTE_IMG.'exposant.png', _T('enlumtypo:barre_exposant'), $params['help'])
		. bouton_barre_racc("barre_raccourci('&lt;sub&gt;','&lt;/sub&gt;',$params[champ], $params[num])", _DIR_BTE_IMG.'indice.png', _T('enlumtypo:barre_indice'), $params['help'])
		. bouton_barre_racc("barre_raccourci('&lt;sc&gt;','&lt;/sc&gt;',$params[champ], $params[num])", _DIR_BTE_IMG.'petitescapitales.png', _T('enlumtypo:barre_petitescapitales'), $params['help']);
    return $params;
}

// bouton qui controle la toolbox 'tableau_boutonsavances'
function TypoEnluminee_BT_paragraphes($params) {
	if(!$params['forum']) $params['flux'] .= 
		bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_boutonsavances']."','');", _DIR_BTE_IMG.'avances.png', _T('enlumtypo:barre_boutonsavances'), $params['help']);
    return $params;
}

?>