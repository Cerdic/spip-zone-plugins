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

function TypoEnluminee_bt_toolbox($params) {
	$params['flux'] .= afficher_boutonsavances($params['champ'], $params['help'], $params['num']);
	return $params;
}

function TypoEnluminee_bt_caracteres($params) {
	$params['flux'] .= bouton_barre_racc("barre_raccourci('[*','*]',".$params['champ'].", ".$params['num'].")", _DIR_BTE_IMG.'miseenevidence.png', _T('enlumtypo:barre_miseenevidence'), $params['help'])
		. bouton_barre_racc("barre_raccourci('&lt;sup&gt;','&lt;/sup&gt;',".$params['champ'].", ".$params['num'].")", _DIR_BTE_IMG.'exposant.png', _T('enlumtypo:barre_exposant'), $params['help'])
		. bouton_barre_racc("barre_raccourci('&lt;sub&gt;','&lt;/sub&gt;',".$params['champ'].", ".$params['num'].")", _DIR_BTE_IMG.'indice.png', _T('enlumtypo:barre_indice'), $params['help'])
		. bouton_barre_racc("barre_raccourci('&lt;sc&gt;','&lt;/sc&gt;',".$params['champ'].", ".$params['num'].")", _DIR_BTE_IMG.'petitescapitales.png', _T('enlumtypo:barre_petitescapitales'), $params['help']);
    return $params;
}

// bouton qui controle la toolbox 'tableau_boutonsavances'
function TypoEnluminee_bt_paragraphes($params) {
	if(!$params['forum']) $params['flux'] .= 
		bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_boutonsavances']."','');", _DIR_BTE_IMG.'avances.png', _T('enlumtypo:barre_boutonsavances'), $params['help']);
    return $params;
}

function typoenluminee_porte_plume_barre_pre_charger($barres){
	$barre = &$barres['edition'];
	
	// E majsucule accent grave
	$barre->ajouterApres('E_aigu', array(
		"id"          => 'E_grave',
		"name"        => _T('enlumtypo:barre_e_accent_grave'),
		"className"   => "outil_e_maj_grave",
		"replaceWith"   => "&Egrave;",
		"display"     => true,
		"lang"    => array('fr','eo','cpf'),
	));
	// e dans le a
	$barre->ajouterApres('E_grave', array(
		"id"          => 'aelig',
		"name"        => _T('enlumtypo:barre_ea'),
		"className"   => "outil_aelig",
		"replaceWith"   => "&aelig;",
		"display"     => true,
		"lang"    => array('fr','eo','cpf'),
	));
	// e dans le a majuscule
	$barre->ajouterApres('aelig', array(
		"id"          => 'AElig',
		"name"        => _T('enlumtypo:barre_ea_maj'),
		"className"   => "outil_aelig_maj",
		"replaceWith"   => "&AElig;",
		"display"     => true,
		"lang"    => array('fr','eo','cpf'),
	));
	// c cedille majuscule
	$barre->ajouterApres('OE', array(
		"id"          => 'Ccedil',
		"name"        => _T('enlumtypo:barre_c_cedille_maj'),
		"className"   => "outil_ccedil_maj",
		"replaceWith"   => "&Ccedil;",
		"display"     => true,
		"lang"    => array('fr','eo','cpf'),
	));
	// c cedille majuscule
	$barre->ajouterApres('Ccedil', array(
		"id"          => 'euro',
		"name"        => _T('enlumtypo:barre_euro'),
		"className"   => "outil_euro",
		"replaceWith"   => "&euro;",
		"display"     => true,
		"lang"    => array('fr','eo','cpf'),
	));
	
	// Transformation en majuscule
	$barre->ajouterApres('euro', array(
		"id"          => 'uppercase',
		"name"        => _T('barre_outils:barre_gestion_cr_changercassemajuscules'),
		"className"   => "outil_uppercase",
		"replaceWith" => 'function(markitup) { return markitup.selection.toUpperCase() }',
		"display"     => true,
	));
	// Transformation en minuscule
	$barre->ajouterApres('uppercase', array(
		"id"          => 'lowercase',
		"name"        => _T('barre_outils:barre_gestion_cr_changercasseminuscules'),
		"className"   => "outil_lowercase",
		"replaceWith" => 'function(markitup) { return markitup.selection.toLowerCase() }',
		"display"     => true,
	));
	
	return $barres;
}

function typoenluminee_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_e_maj_grave' => 'eagrave-maj.png',
		'outil_aelig' => 'aelig.png',
		'outil_aelig_maj' => 'aelig-maj.png',
		'outil_ccedil_maj' => 'ccedil-maj.png',
		'outil_euro' => 'euro.png',
		'outil_uppercase' => 'text_uppercase.png',
		'outil_lowercase' => 'text_lowercase.png',
	));
}

?>