<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_BARRE_TYPO',(_DIR_PLUGINS.end($p)));

// construit un bouton (ancre) de raccourci avec icone et aide

function bouton_barre_racc($action, $img, $help, $champhelp) {

	$a = attribut_html($help);
	return "<a\nhref=\"javascript:"
		.$action
		."\" class='spip_barre' tabindex='1000'\ntitle=\""
		. $a
		."\"" 
		.(!_DIR_RESTREINT ? '' :  "\nonmouseover=\"helpline('"
		  .addslashes($a)
		  ."',$champhelp)\"\nonmouseout=\"helpline('"
		  .attribut_html(_T('barre_aide'))
		  ."', $champhelp)\"")
		."><img\nsrc='"
		.$img
		."' border='0' height='16' width='16' align='middle' /></a>";
}

// sert à construire les sousbarre
function produceWharf($id, $title = '', $sb = '') {
  $visible = ($changer_virtuel || $virtuel);
  $res .= $title;
  $GLOBALS['numero_block'][$id] = ($GLOBALS['compteur_block']+1);
  if ($visible) {
    $res .= debut_block_visible("arb_".$GLOBALS['numero_block'][$id]);
  } else {
    $res .= debut_block_invisible("arb_".$GLOBALS['numero_block'][$id]);
  }
  $res .= $sb;
  $res .= fin_block();
  return $res;
}

//gestion des lignes optionnelles

//creation de tableau
function afficher_gestion_tableau($champ) {

$tableau_formulaire = '<table class="spip_barre" style="width: 100%; padding: 1px!important; border-top: 0px;">
<tr><td>
'._T('bartypenr:barre_gestion_colonne').': <input type="text" name="barre_nbcolones" style="width: 30px;" value="2" size="2" maxlength="2"   /></td><td>
'._T('bartypenr:barre_gestion_ligne').': <input type="text" name="barre_nbrangs" style="width: 30px;" value="2" size="2" maxlength="2" /></td><td>
<input type="checkbox" name="barre_doentete" value="yes" onclick="if
(!(barre_doentete.checked) && barre_docolsize.checked){ barre_docolsize.checked = false;  }" /> '._T('bartypenr:barre_gestion_entete')
.'</td><td>
  <input type="button" value="OK" class="fondo" onclick="javascript:
    barre_nbcolones.value = Math.abs(barre_nbcolones.value); barre_nbrangs.value
    = Math.abs(barre_nbrangs.value);
    if (!(barre_nbcolones.value == 0 || barre_nbrangs.value == 0)) {
    barre_tableau('.$champ.', barre_nbcolones.value, barre_nbrangs.value,
    barre_doentete.checked, barre_docolsize.checked); } "> 
</td></tr></table>
';  
  return produceWharf('tableau_gestion','',$tableau_formulaire); 
}

// construction des liens
function afficher_gestion_lien($champ) {

$tableau_formulaire = '
 <table class="spip_barre" style="width: 100%; padding: 1px!important; border-top: 0px;">
<tr><td> 
'._T('bartypenr:barre_adresse').': <input type="text" name="lien_nom" value="http://" size="21" maxlength="255"   /><br/>
'._T('bartypenr:barre_bulle').': <input type="text" name="lien_bulle" value="" size="21" maxlength="255"   />
</td><td>
<input type="checkbox" name="lien_ext" value="yes" />
'._T('bartypenr:barre_lien_externe').'
</td><td>
  <input type="button" value="OK" class="fondo" onclick="javascript:var token =
  \'->\'; if (lien_ext.checked){ token += \'>\';}barre_demande2(\'[\', token, \']\', lien_nom.value, lien_bulle.value,'.$champ.');"> 
</td></tr></table>
';
  return produceWharf('tableau_lien','',$tableau_formulaire); 	
}


// gestion de la recherche

function afficher_gestion_remplacer($champ) {

$tableau_formulaire = '
<table class="spip_barre" style="width: 100%; padding: 1px!important; border-top: 0px;">
<tr><td width = "25%">'.
_T('bartypenr:barre_gestion_cr_chercher')
.'<input type="text" name="barre_chercher" value="" size="12" maxlength="255" /></td><td width = "20%">
<input type="checkbox" name="rec_case" value="yes" />'
._T('bartypenr:barre_gestion_cr_casse')
.'<br/><input type="checkbox" name="rec_entier" value="yes" />
'._T('bartypenr:barre_gestion_cr_entier').'
</td><td  width = "25%">'
._T('bartypenr:barre_gestion_cr_remplacer')
.'<input type="text" name="barre_remplacer" value="" size="12" maxlength="255" /> 
</td><td width = "20%">
<input type="checkbox" name="rec_tout" value="yes" />'
._T('bartypenr:barre_gestion_cr_tout')
.'</td><td width = "10%">
   <input type="button" value="OK" class="fondo"
  onclick="javascript:barre_searchreplace(document.formulaire.barre_chercher.value, document.formulaire.barre_remplacer.value, document.formulaire.rec_tout.checked, document.formulaire.rec_case.checked, document.formulaire.rec_entier.checked,'.$champ.');"> 
</td></tr></table>';

  return produceWharf('tableau_remplacer','',$tableau_formulaire); 
}

// pour les ancres
function afficher_gestion_ancre($champ) {

$tableau_formulaire = '
<table class="spip_barre" style="width: 100%; padding: 1px!important; border-top: 0px;">
  <tr>
    <td width="10%" align="center"><strong>Gestion des ancres</strong></td>
    <td width="45%"><strong>Transformer en ancre</strong><input type="checkbox" name="r_hdp" value="yes" />Avec renvoi hdp<br/>
    <i>Nom de l\'ancre</i><br/>
      <input type="text" name="ancre_nom">
	  
	<input type="button" value="OK" class="fondo" onclick="javascript:barre_ancre2(\'[#\', \'<-\', \']\', ancre_nom.value, r_hdp.checked, '.$champ.');" > 
    </td>
	<td width="45%"><strong>Pointer vers une ancre</strong><br/>
    <i>Ancre cible</i><br/>
      <input type="text" name="ancre_cible">
	<input type="button" value="OK" class="fondo" onclick="javascript:barre_demande2(\'[\', \'->#\', \']\', ancre_cible.value, '.$champ.');" > 
</td>
  </tr> 
</table>';

  return produceWharf('tableau_ancre','',$tableau_formulaire); 	
}

// pour les caractères
function afficher_caracteres($champ,$spip_lang) {

$reta .=    "";

	// guillemets
	if ($spip_lang == "fr" OR $spip_lang == "eo" OR $spip_lang == "cpf" OR $spip_lang == "ar" OR $spip_lang == "es") {
$reta .= bouton_barre_racc ("barre_raccourci('&laquo;~','~&raquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets.png", _T('barre_guillemets'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('&ldquo;','&rdquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-simples.png", _T('barre_guillemets_simples'), $champhelp);
}
else if ($spip_lang == "bg" OR $spip_lang == "de" OR $spip_lang == "pl" OR $spip_lang == "hr" OR $spip_lang == "src") {
$reta .= bouton_barre_racc ("barre_raccourci('&bdquo;','&ldquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-de.png", _T('barre_guillemets'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('&sbquo;','&lsquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-uniques-de.png", _T('barre_guillemets_simples'), $champhelp);
}
else {
$reta .= bouton_barre_racc ("barre_raccourci('&ldquo;','&rdquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-simples.png", _T('barre_guillemets'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('&lsquo;','&rsquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-uniques.png", _T('barre_guillemets_simples'), $champhelp);
}
	// caaracteres
if ($spip_lang == "fr" OR $spip_lang == "eo" OR $spip_lang == "cpf") {

$reta .= bouton_barre_racc ("barre_inserer('&Agrave;',$champ)", _DIR_IMG_ICONES_BARRE."agrave-maj.png", _T('barre_a_accent_grave'), $champhelp);
$reta .= bouton_barre_racc ("barre_inserer('&Eacute;',$champ)", _DIR_IMG_ICONES_BARRE."eacute-maj.png", _T('barre_e_accent_aigu'), $champhelp);

if ($spip_lang == "fr") {
$reta .= bouton_barre_racc ("barre_inserer('&oelig;',$champ)", _DIR_IMG_ICONES_BARRE."oelig.png", _T('barre_eo'), $champhelp);
$reta .= bouton_barre_racc ("barre_inserer('&OElig;',$champ)", _DIR_IMG_ICONES_BARRE."oelig-maj.png", _T('barre_eo_maj'), $champhelp);
}
}
// euro
$reta .= bouton_barre_racc ("barre_inserer('&euro;',$champ)", _DIR_IMG_ICONES_BARRE."euro.png", _T('barre_euro'), $champhelp);

$reta .= "&nbsp;</td>";
	
$tableau_formulaire = '
<table class="spip_barre" style="width: 100%; padding: 1px!important; border-top: 0px;">
  <tr class="spip_barre">
    <td width="30%">'._T('bartypenr:barre_caracteres').'</td>
    <td>'.$reta.'
    </td>
  </tr> 
</table>
';

  return produceWharf('tableau_caracteres','',$tableau_formulaire); 	
}

// construit un tableau de raccourcis pour un noeud de DOM

function afficher_barre($champ, $forum=false, $lang='') {
	global $spip_lang, $options, $spip_lang_right, $spip_lang_left, $spip_lang;
	static $num_barre = 0;
	include_spip('inc/layer');
	if (!$GLOBALS['browser_barre']) return '';
	if (!$lang) $lang = $spip_lang;
	$ret = ($num_barre > 0)  ? '' :
	  '<script type="text/javascript" src="' . find_in_path('js/spip_barre.js').'"></script>';


	$num_barre++;
	$champhelp = "document.getElementById('barre_$num_barre')";


 // Prégénération des toolzbox.. (wharfing)
    $toolbox .= afficher_gestion_tableau($champ);
    $toolbox .= afficher_gestion_lien($champ);
	$toolbox .= afficher_gestion_ancre($champ);
	$toolbox .= afficher_caracteres($champ,$spip_lang);
    $toolbox .= afficher_gestion_remplacer($champ);
//

	$ret .= "<table class='spip_barre' width='100%' cellpadding='0' cellspacing='0' border='0'>";
	$ret .= "\n<tr width='100%' class='spip_barre'>";
	$ret .= "\n<td style='text-align: $spip_lang_left;' valign='middle'>";
	$col = 1;

	// Italique, gras, intertitres
	$ret .= bouton_barre_racc ("barre_raccourci('{','}',$champ)", _DIR_IMG_ICONES_BARRE."italique.png", _T('barre_italic'), $champhelp);
	$ret .= bouton_barre_racc ("barre_raccourci('{{','}}',$champ)", _DIR_IMG_ICONES_BARRE."gras.png", _T('barre_gras'), $champhelp);
	if ($options == "avancees") {
		$ret .= bouton_barre_racc ("barre_raccourci('[*','*]',$champ)", _DIR_PLUGIN_BARRE_TYPO.'/img_pack/icones_barre/miseenevidence.png', _T('bartypenr:barre_miseenevidence'), $formulaire, $texte);
		$ret .= bouton_barre_racc ("barre_raccourci('&lt;sup&gt;','&lt;/sup&gt;',$champ)", _DIR_PLUGIN_BARRE_TYPO.'/img_pack/icones_barre/exposant.png', _T('bartypenr:barre_exposant'), $formulaire, $texte);
		$ret .= bouton_barre_racc ("barre_raccourci('&lt;sc&gt;','&lt;/sc&gt;',$champ)", _DIR_PLUGIN_BARRE_TYPO.'/img_pack/icones_barre/petitescapitales.png', _T('bartypenr:barre_petitescapitales'), $formulaire, $texte);
	}
	if (!$forum) {
		$ret .= "&nbsp;";
		$ret .= bouton_barre_racc ("barre_raccourci('\n\n{{{','}}}\n\n',$champ)", _DIR_IMG_ICONES_BARRE."intertitre.png", _T('barre_intertitre'), $champhelp);
		if ($options == "avancees") {
			$ret .= bouton_barre_racc ("barre_raccourci('\n\n{2{','}2}\n\n',$champ)", _DIR_PLUGIN_BARRE_TYPO.'/img_pack/icones_barre/intertitre2.png', _T('bartypenr:barre_intertitre2'), $formulaire, $texte);
			$ret .= bouton_barre_racc ("barre_raccourci('\n\n{3{','}3}\n\n',$champ)", _DIR_PLUGIN_BARRE_TYPO.'/img_pack/icones_barre/intertitre3.png', _T('bartypenr:barre_intertitre3'), $formulaire, $texte);
			$ret .= bouton_barre_racc ("barre_raccourci('[|','|]',$champ)", _DIR_PLUGIN_BARRE_TYPO.'/img_pack/icones_barre/center.png', _T('bartypenr:barre_centrer'), $formulaire, $texte);
			$ret .= bouton_barre_racc ("barre_raccourci('[/','/]',$champ)", _DIR_PLUGIN_BARRE_TYPO.'/img_pack/icones_barre/right.png', _T('bartypenr:barre_alignerdroite'), $formulaire, $texte);
			$ret .= bouton_barre_racc ("barre_raccourci('[(',')]',$champ)", _DIR_PLUGIN_BARRE_TYPO.'/img_pack/icones_barre/cadretexte.png', _T('bartypenr:barre_encadrer'), $formulaire, $texte);
		}
	}
	$ret .= "&nbsp;</td>\n<td>";
	$col ++;

	// Lien hypertexte, notes de bas de page, citations
//	$ret .= bouton_barre_racc ("barre_demande('[','->',']', '".addslashes(_T('barre_lien_input'))."', $champ)", _DIR_IMG_ICONES_BARRE."lien.png", _T('barre_lien'), $champhelp);

// gestion des liens
      $ret .=    bouton_barre_racc("javascript:swap_couche('".$GLOBALS['numero_block']['tableau_lien']."','');", _DIR_IMG_ICONES_BARRE."lien.png", _T('barre_lien'), $formulaire, $texte,'tableau_lien' );
	  
	if (!$forum) {
		$ret .= bouton_barre_racc ("barre_raccourci('[[',']]',$champ)", _DIR_IMG_ICONES_BARRE."notes.png", _T('barre_note'), $champhelp);
	}
	if ($forum) {
		$ret .= "&nbsp;</td>\n<td>";
		$col ++;
		$ret .= bouton_barre_racc ("barre_raccourci('\n\n&lt;quote&gt;','&lt;/quote&gt;\n\n',$champ)", _DIR_IMG_ICONES_BARRE."quote.png", _T('barre_quote'), $champhelp);
	}
	if ($options == "avancees") {
		$ret .= bouton_barre_racc ("barre_raccourci('[?',']',$champ)", _DIR_PLUGIN_BARRE_TYPO.'/img_pack/icones_barre/barre-wiki.png', "Entr&eacute;e du [?glossaire] (Wikipedia)", $formulaire, $texte);


//gestion des tableaux		
		$ret .= bouton_barre_racc("javascript:swap_couche('".$GLOBALS['numero_block']['tableau_gestion']."',
      '');", _DIR_PLUGIN_BARRE_TYPO.'/img_pack/icones_barre/barre-tableau.png', "Ins&eacute;rer un tableau",
      $formulaire, $texte, 'tableau_gestion');
	  
	$ret .= "&nbsp</td>\n<td>";

// gestion du remplacement
      $ret .=    bouton_barre_racc("javascript:swap_couche('".$GLOBALS['numero_block']['tableau_remplacer']."','');", _DIR_PLUGIN_BARRE_TYPO."/img_pack/icones_barre/chercher_remplacer.png", "Chercher Remplacer", $formulaire, $texte,'tableau_remplacer' );

// gestion des ancres		
		$ret .=    bouton_barre_racc("javascript:swap_couche('".$GLOBALS['numero_block']['tableau_ancre']."','');", _DIR_PLUGIN_BARRE_TYPO."/img_pack/icones_barre/ancre.png", 'Gestion des ancres', $formulaire, $texte,'tableau_ancre' );  

}

	$ret .= "&nbsp;</td>";
	$col++;

	// Insertion de caracteres difficiles a taper au clavier (guillemets, majuscules accentuees...)
	$ret .= "\n<td style='text-align:$spip_lang_left;' valign='middle'>";
	$col++;
	$ret .=    bouton_barre_racc("javascript:swap_couche('".$GLOBALS['numero_block']['tableau_caracteres']."','');", _DIR_PLUGIN_BARRE_TYPO."/img_pack/icones_barre/clavier.png", _T('bartypenr:barre_caracteres'), $formulaire, $texte,'tableau_caracteres' );
	


	$ret .= "&nbsp;</td>";
	$col++;

	if (!_DIR_RESTREINT) {
		$ret .= "\n<td style='text-align:$spip_lang_right;' valign='middle'>";
		$col++;
	//	$ret .= "&nbsp;";
		$ret .= aide("raccourcis");
		$ret .= "&nbsp;";
		$ret .= "</td>";
	}
	$ret .= "</tr>";

	// Sur les forums publics, petite barre d'aide en survol des icones
	if (_DIR_RESTREINT)
		$ret .= "\n<tr>\n<td colspan='$col'><input disabled='disabled' type='text' id='barre_$num_barre' size='45' maxlength='100' style='width:100%; font-size:11px; color: black; background-color: #e4e4e4; border: 0px solid #dedede;'\nvalue=\"".attribut_html(_T('barre_aide'))."\" /></td></tr>";

	$ret .= "</table>";
	 $ret .= $toolbox;
	return $ret;
}

// pour compatibilite arriere. utiliser directement le corps a present.

function afficher_claret() {
	include_spip('inc/layer');
	return $GLOBALS['browser_caret'];
}

?>