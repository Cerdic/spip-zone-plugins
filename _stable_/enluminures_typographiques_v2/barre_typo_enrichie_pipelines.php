<?php
if (!defined('_DIR_PLUGIN_TYPOENLUMINEE')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_TYPOENLUMINEE',(_DIR_PLUGINS.end($p)));
}


// pour les caracteres
function afficher_boutonsavances($champ, $champhelp) {

$reta = '';
$reta .= bouton_barre_racc ("barre_raccourci('\n\n{2{','}2}\n\n',$champ)", _DIR_PLUGIN_TYPOENLUMINEE.'/img_pack/icones_barre/intertitre2.png', _T('bartypenr:barre_intertitre2'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('\n\n{3{','}3}\n\n',$champ)", _DIR_PLUGIN_TYPOENLUMINEE.'/img_pack/icones_barre/intertitre3.png', _T('bartypenr:barre_intertitre3'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('[|','|]',$champ)", _DIR_PLUGIN_TYPOENLUMINEE.'/img_pack/icones_barre/center.png', _T('bartypenr:barre_centrer'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('[/','/]',$champ)", _DIR_PLUGIN_TYPOENLUMINEE.'/img_pack/icones_barre/right.png', _T('bartypenr:barre_alignerdroite'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('[(',')]',$champ)", _DIR_PLUGIN_TYPOENLUMINEE.'/img_pack/icones_barre/cadretexte.png', _T('bartypenr:barre_encadrer'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('<poesie>','</poesie>',$champ)", _DIR_PLUGIN_TYPOENLUMINEE."/img_pack/icones_barre/poesie.png", _T('bartypenr:barre_poesie'), $champhelp);
	global $spip_lang;
	$params = array($champ,$champhelp,$spip_lang);
	$add = pipeline("BarreTypoEnrichie_boutonsavances",array($champ,$champhelp,$spip_lang));
	if ($params!=$add)
		$reta .= $add;

$reta .= '&nbsp;';
	
$tableau_formulaire = '
<table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">
  <tr class="spip_barre">
    <td>'._T('bartypenr:barre_avances').'</td>
    <td>'.$reta.'
    </td>
  </tr> 
</table>
';

  return produceWharf('tableau_boutonsavances','',$tableau_formulaire); 	
}

function TypoEnluminee_BarreTypoEnrichie_toolbox($paramArray) {
	return afficher_boutonsavances($paramArray[0], $paramArray[1]);
}

function TypoEnluminee_BarreTypoEnrichie_avancees($paramArray) {
	$ret = bouton_barre_racc ("barre_raccourci('[*','*]',".$paramArray[0].")", _DIR_PLUGIN_TYPOENLUMINEE.'/img_pack/icones_barre/miseenevidence.png', _T('bartypenr:barre_miseenevidence'), $paramArray[1]);
	$ret .= bouton_barre_racc ("barre_raccourci('&lt;sup&gt;','&lt;/sup&gt;',".$paramArray[0].")", _DIR_PLUGIN_TYPOENLUMINEE.'/img_pack/icones_barre/exposant.png', _T('bartypenr:barre_exposant'), $paramArray[1]);
	$ret .= bouton_barre_racc ("barre_raccourci('&lt;sc&gt;','&lt;/sc&gt;',".$paramArray[0].")", _DIR_PLUGIN_TYPOENLUMINEE.'/img_pack/icones_barre/petitescapitales.png', _T('bartypenr:barre_petitescapitales'), $paramArray[1]);
	$ret .= bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_boutonsavances']."','');", _DIR_PLUGIN_TYPOENLUMINEE."/img_pack/icones_barre/avances.png", _T('bartypenr:barre_boutonsavances'), $paramArray[1]);

    return $ret;   
}


?>