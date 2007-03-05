<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
if (!defined('_DIR_PLUGIN_BARRETYPOENRICHIE')){
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_BARRETYPOENRICHIE',(_DIR_PLUGINS.end($p)));
}

include_spip('inc/vieilles_defs');
// construit un bouton (ancre) de raccourci avec icone et aide
if (!function_exists('afficher_claret')){
	function afficher_claret() {
		include_spip('inc/layer');
		return $GLOBALS['browser_caret'];
	}
}

// http://doc.spip.org/@bouton_barre_racc
function bouton_barre_racc($action, $img, $help, $champhelp) {
	include_spip('inc/charsets');

	$a = attribut_html($help);
	$action = str_replace ("&lt;", '%3C',  $action);
	$action = str_replace ("&gt;", '%3E',  $action);
	$action = str_replace ("\n", '%5Cn',  $action);
	$action = unicode_to_javascript(html2unicode($action));
	return "<a href=\"javascript:"
		.$action
		."\"\n class='spip_barre' tabindex='1000' title=\""
		. $a
		."\"" 
		.(!_DIR_RESTREINT ? '' :  " onmouseover=\"helpline('"
		  .addslashes($a)
		  ."',$champhelp)\" onmouseout=\"helpline('"
		  .attribut_html(_T('barre_aide'))
		  ."', $champhelp)\"")
		."><img src='"
		.$img
		."' height='16' width='16' alt=\"".$a."\" /></a>";
}

// sert a construire les sousbarre
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

// construction des liens
function afficher_gestion_lien($champ) {

$tableau_formulaire = '
 <table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">
<tr><td> 
'._T('bartypenr:barre_adresse').'&nbsp;: <input type="text" name="lien_nom" value="http://" size="21" maxlength="255" /><br />
'._T('bartypenr:barre_bulle').'&nbsp;: <input type="text" name="lien_bulle" value="" size="21" maxlength="255" />
</td><td>
'._T('bartypenr:barre_langue').'&nbsp;: <input type="text" name="lien_langue" value="" size="10" maxlength="10" />
</td><td>
  <input type="button" value="OK" class="fondo" onclick="javascript:barre_demande_lien(\'[\', \'->\', \']\', lien_nom.value, lien_bulle.value, lien_langue.value,'.$champ.');lien_nom.value=\'\';lien_bulle.value=\'\';lien_langue.value=\'\';" /> 
</td></tr></table>
';
  return produceWharf('tableau_lien','',$tableau_formulaire); 	
}

// Changer la casse
function RaccourcisMajusculesMinuscules($champ, $champhelp) {
	return bouton_barre_racc ("barre_2Majuscules($champ)",  _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/text_uppercase.png', _T('bartypenr:barre_gestion_cr_changercassemajuscules'), $champhelp) .'&nbsp;'
. bouton_barre_racc ("barre_2Minuscules($champ)",  _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/text_lowercase.png', _T('bartypenr:barre_gestion_cr_changercasseminuscules'), $champhelp);
}

// gestion de la recherche

function afficher_gestion_remplacer($champ, $champhelp) {

$tableau_formulaire = '
<table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">
<tr><td><label for="barre_chercher">'.
_T('bartypenr:barre_gestion_cr_chercher')
.'</label> <input type="text" id="barre_chercher" name="barre_chercher" value="" size="12" maxlength="255" /><br />
<input type="checkbox" name="rec_case" id="rec_case" value="yes" />
<label for="rec_case">'._T('bartypenr:barre_gestion_cr_casse').'</label><br />
   <input type="button" value="'._T('bartypenr:barre_gestion_cr_chercher').'" class="fondo"
  onclick="javascript:barre_search(document.formulaire.barre_chercher.value, document.formulaire.rec_entier.checked, document.formulaire.rec_case.checked,'.$champ.');" /> 
</td><td><label for="barre_remplacer">'
._T('bartypenr:barre_gestion_cr_remplacer')
.'</label> <input type="text" name="barre_remplacer" id="barre_remplacer" value="" size="12" maxlength="255" /><br />
<input type="checkbox" name="rec_tout" id="rec_tout" value="yes" />
<label for="rec_tout">'._T('bartypenr:barre_gestion_cr_tout').'</label><br />
<input type="checkbox" name="rec_entier" id="rec_entier" value="yes" />
<label for="rec_entier">'._T('bartypenr:barre_gestion_cr_entier').'</label><br />
   <input type="button" value="'._T('bartypenr:barre_gestion_cr_remplacer').'" class="fondo"
  onclick="javascript:barre_searchreplace(document.formulaire.barre_chercher.value, document.formulaire.barre_remplacer.value, document.formulaire.rec_tout.checked, document.formulaire.rec_case.checked, document.formulaire.rec_entier.checked,'.$champ.');" /> 
</td>
<td>'._T('bartypenr:barre_gestion_cr_changercasse').' :'. RaccourcisMajusculesMinuscules($champ, $champhelp).'
</td>
</tr></table>';

  return produceWharf('tableau_remplacer','',$tableau_formulaire); 
}

// pour les ancres
function afficher_gestion_ancre($champ) {

$tableau_formulaire = '
<table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">
  <tr>
    <td style="width:auto; text-align:center;"><strong>'.
_T('bartypenr:barre_gestion_anc_caption')
.'</strong></td>
    <td style="width:auto;"><strong>'.
_T('bartypenr:barre_gestion_anc_inserer')
.'</strong><br />
    <label for="ancre_nom"><i>'.
_T('bartypenr:barre_gestion_anc_nom')
.'</i></label> <br />
      <input type="text" name="ancre_nom" id="ancre_nom" />
	  
	<input type="button" value="OK" class="fondo" onclick="javascript:barre_ancre(\'[\', \'<-\', \']\', ancre_nom.value, '.$champ.');" />
    </td>
	<td style="width:auto;"><strong>'.
_T('bartypenr:barre_gestion_anc_pointer')
.'</strong><br />
    <label for="ancre_cible"><i>'.
_T('bartypenr:barre_gestion_anc_cible')
.'</i></label> <input type="text" name="ancre_cible" id="ancre_cible" /><br />
	<label for="ancre_bulle"><i>'.
_T('bartypenr:barre_gestion_anc_bulle')
.'</i></label> <input type="text" name="ancre_bulle" id="ancre_bulle" />
	<input type="button" value="OK" class="fondo" onclick="javascript:barre_demande(\'[\', \'->#\', \']\', ancre_cible.value, ancre_bulle.value, '.$champ.');" /> 
</td>
  </tr> 
</table>';

  return produceWharf('tableau_ancre','',$tableau_formulaire); 	
}

// pour les caracteres
function afficher_caracteres($champ, $spip_lang, $champhelp) {

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
	// caracteres
if ($spip_lang == "fr" OR $spip_lang == "eo" OR $spip_lang == "cpf") {

$reta .= bouton_barre_racc ("barre_inserer('&Agrave;',$champ)", _DIR_IMG_ICONES_BARRE."agrave-maj.png", _T('barre_a_accent_grave'), $champhelp);
$reta .= bouton_barre_racc ("barre_inserer('&Eacute;',$champ)", _DIR_IMG_ICONES_BARRE."eacute-maj.png", _T('barre_e_accent_aigu'), $champhelp);
$reta .= bouton_barre_racc ("barre_inserer('&Egrave;',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/eagrave-maj.png', _T('bartypenr:barre_e_accent_grave'), $champhelp);
$reta .= bouton_barre_racc ("barre_inserer('&aelig;',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/aelig.png', _T('bartypenr:barre_ea'), $champhelp);
$reta .= bouton_barre_racc ("barre_inserer('&AElig;',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/aelig-maj.png', _T('bartypenr:barre_ea_maj'), $champhelp);

if ($spip_lang == "fr") {
$reta .= bouton_barre_racc ("barre_inserer('&oelig;',$champ)", _DIR_IMG_ICONES_BARRE."oelig.png", _T('barre_eo'), $champhelp);
$reta .= bouton_barre_racc ("barre_inserer('&OElig;',$champ)", _DIR_IMG_ICONES_BARRE."oelig-maj.png", _T('barre_eo_maj'), $champhelp);
$reta .= bouton_barre_racc ("barre_inserer('&Ccedil;',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/ccedil-maj.png', _T('bartypenr:barre_c_cedille_maj'), $champhelp);
}
}
// euro
$reta .= bouton_barre_racc ("barre_inserer('&euro;',$champ)", _DIR_IMG_ICONES_BARRE."euro.png", _T('barre_euro'), $champhelp);
$reta .= '&nbsp;'.RaccourcisMajusculesMinuscules($champ, $champhelp);

$reta .= '&nbsp;';
	
$tableau_formulaire = '
<table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">
  <tr class="spip_barre">
    <td>'._T('bartypenr:barre_caracteres').'</td>
    <td>'.$reta.'
    </td>
  </tr> 
</table>
';

  return produceWharf('tableau_caracteres','',$tableau_formulaire); 	
}

// pour les caracteres
function afficher_boutonsavances($champ, $champhelp) {

$reta = '';
$reta .= bouton_barre_racc ("barre_raccourci('\n\n{2{','}2}\n\n',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/intertitre2.png', _T('bartypenr:barre_intertitre2'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('\n\n{3{','}3}\n\n',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/intertitre3.png', _T('bartypenr:barre_intertitre3'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('[|','|]',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/center.png', _T('bartypenr:barre_centrer'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('[/','/]',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/right.png', _T('bartypenr:barre_alignerdroite'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('[(',')]',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/cadretexte.png', _T('bartypenr:barre_encadrer'), $champhelp);
$reta .= bouton_barre_racc ("barre_raccourci('<poesie>','</poesie>',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE."/img_pack/icones_barre/poesie.png", _T('bartypenr:barre_poesie'), $champhelp);

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

// construit un tableau de raccourcis pour un noeud de DOM

function afficher_barre($champ, $forum=false, $lang='') {
	global $spip_lang, $options, $spip_lang_right, $spip_lang_left, $spip_lang;
	static $num_barre = 0;
	include_spip('inc/layer');
	if (!$GLOBALS['browser_barre']) return '';
	if (!$lang) $lang = $spip_lang;
	$layer_public = '<script type="text/javascript" src="' . find_in_path('javascript/layer.js').'"></script>';
	$ret = ($num_barre > 0)  ? '' :
	  $layer_public . '<script type="text/javascript" src="' . find_in_path('js/spip_barre.js').'"></script>';


	$num_barre++;
	$champhelp = "document.getElementById('barre_$num_barre')";


 // Pregeneration des toolzbox.. (wharfing)
    $toolbox .= afficher_boutonsavances($champ, $champhelp);
	$toolbox .= afficher_caracteres($champ, $spip_lang, $champhelp);
    $toolbox .= afficher_gestion_lien($champ);
	$toolbox .= afficher_gestion_ancre($champ);
    $toolbox .= afficher_gestion_remplacer($champ, $champhelp);
//

	$ret .= "<table class='spip_barre' style='width:auto;' cellpadding='0' cellspacing='0' border='0' summary=''>";
	$ret .= "\n<tr style='width: auto;' class='spip_barre'>";
	$ret .= "\n<td style='text-align: $spip_lang_left;' valign='middle'>";
	$col = 1;

	// Italique, gras, intertitres
	$ret .= bouton_barre_racc ("barre_raccourci('{','}',$champ)", _DIR_IMG_ICONES_BARRE."italique.png", _T('barre_italic'), $champhelp);
	$ret .= bouton_barre_racc ("barre_raccourci('{{','}}',$champ)", _DIR_IMG_ICONES_BARRE."gras.png", _T('barre_gras'), $champhelp);
	if ($options == "avancees") {
		$ret .= bouton_barre_racc ("barre_raccourci('[*','*]',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/miseenevidence.png', _T('bartypenr:barre_miseenevidence'), $champhelp);
		$ret .= bouton_barre_racc ("barre_raccourci('&lt;sup&gt;','&lt;/sup&gt;',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/exposant.png', _T('bartypenr:barre_exposant'), $champhelp);
		$ret .= bouton_barre_racc ("barre_raccourci('&lt;sc&gt;','&lt;/sc&gt;',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/petitescapitales.png', _T('bartypenr:barre_petitescapitales'), $champhelp);
	}
	if (!$forum) {
		$ret .= "&nbsp;";
		$ret .= bouton_barre_racc ("barre_raccourci('\n\n{{{','}}}\n\n',$champ)", _DIR_IMG_ICONES_BARRE."intertitre.png", _T('barre_intertitre'), $champhelp);
		if ($options == "avancees") {
			$ret .=    bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_boutonsavances']."','');", _DIR_PLUGIN_BARRETYPOENRICHIE."/img_pack/icones_barre/avances.png", _T('bartypenr:barre_boutonsavances'), $champhelp);
		}
	}
	$ret .= "&nbsp;</td>\n<td>";
	$col ++;

	// Lien hypertexte, notes de bas de page, citations
//	$ret .= bouton_barre_racc ("barre_demande('[','->',']', '".addslashes(_T('barre_lien_input'))."', $champ)", _DIR_IMG_ICONES_BARRE."lien.png", _T('barre_lien'), $champhelp);

// gestion des liens
      $ret .=    bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_lien']."','');", _DIR_IMG_ICONES_BARRE."lien.png", _T('barre_lien'), $champhelp);
// gestion des ancres		
		$ret .=    bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_ancre']."','');", _DIR_PLUGIN_BARRETYPOENRICHIE."/img_pack/icones_barre/ancre.png", _T('bartypenr:barre_ancres'), $champhelp);  
	if (!$forum) {
		$ret .= bouton_barre_racc ("barre_raccourci('[[',']]',$champ)", _DIR_IMG_ICONES_BARRE."notes.png", _T('barre_note'), $champhelp);
	}
	if ($forum) {
		$ret .= "&nbsp;</td>\n<td style='text-align: $spip_lang_left;' valign='middle'>";
		$col ++;
		$ret .= bouton_barre_racc ("barre_raccourci('\n\n&lt;quote&gt;','&lt;/quote&gt;\n\n',$champ)", _DIR_IMG_ICONES_BARRE."quote.png", _T('barre_quote'), $champhelp);
	}
	if ($options == "avancees") {
		$ret .= bouton_barre_racc ("barre_raccourci('[?',']',$champ)", _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/barre-wiki.png', _T('bartypenr:barre_glossaire'), $champhelp);


//gestion des tableaux
		$ret .= bouton_barre_racc("barre_tableau($champ, '"._DIR_PLUGIN_BARRETYPOENRICHIE."')",
			_DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/barre-tableau.png', _T('bartypenr:barre_tableau'),
			$champhelp);

	$ret .= "</td>\n<td style='text-align: $spip_lang_left;' valign='middle'>";

// gestion du remplacement
      $ret .=    bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_remplacer']."','');", _DIR_PLUGIN_BARRETYPOENRICHIE."/img_pack/icones_barre/chercher_remplacer.png", _T('bartypenr:barre_chercher'), $champhelp);


}

	$ret .= "</td>";
	$col++;

	// Insertion de caracteres difficiles a taper au clavier (guillemets, majuscules accentuees...)
	$ret .= "\n<td style='text-align: $spip_lang_left;' valign='middle'>";
	$col++;
	$ret .=    bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_caracteres']."','');", _DIR_PLUGIN_BARRETYPOENRICHIE."/img_pack/icones_barre/clavier.png", _T('bartypenr:barre_caracteres'), $champhelp);
	


	$ret .= "</td>";
	$col++;

	if (!_DIR_RESTREINT) {
		$ret .= "\n<td style='text-align: $spip_lang_left;' valign='middle'>";
		$col++;
		$ret .=    bouton_barre_racc("toggle_preview();", _DIR_PLUGIN_BARRETYPOENRICHIE."/img_pack/icones_barre/eye.png", _T('bartypenr:barre_preview'), $champhelp);
		$ret .= "</td>\n<td style='text-align: $spip_lang_left;' valign='middle'>";
		$ret .= aide("raccourcis");
		$ret .= "";
		$ret .= "</td>";
	}
	$ret .= "</tr>";

	// Sur les forums publics, petite barre d'aide en survol des icones
	if (_DIR_RESTREINT)
		$ret .= "\n<tr>\n<td colspan='$col'><input disabled='disabled' type='text' id='barre_$num_barre' size='45' maxlength='100' style='width:auto; font-size:11px; color: black; background-color: #e4e4e4; border: 0px solid #dedede;'\nvalue=\"".attribut_html(_T('barre_aide'))."\" /></td></tr>";

	$ret .= "</table>";
	 $ret .= $toolbox;
	 $ret .= '<script type="text/javascript"><!--
/*function hauteurTextarea() {
	hauteur = (hauteur_fenetre() - 40) /2;
	source = document.getElementById("text_area");
	source.style.height = hauteur + "px";
}*/

function MajPreviewCallBack() {
	$.post("' . generer_url_ecrire("article_preview",""). '", { texte:$("#text_area").val() }, function(data) {
		$("#article_preview").empty()
		$("#article_preview").append(data);
		});
}

function MajPreview() {
	if ($("#article_preview").css("display") != "none") {
		delayFunction("MajPreviewCallBack()",1);
	}
}

$(document).ready(function(){
	$("#text_area").after("<div id=\"article_preview\"></div>");
	//$("#text_area").oneresize($("#article_preview").height($("#text_area").height()+"px"));
	$.ajaxTimeout( 5000 );
	$("#text_area").keypress(function() { MajPreview() });
});
	 //--></script>';
	return $ret;
}

// http://doc.spip.org/@afficher_textarea_barre
function afficher_textarea_barre($texte, $forum=false)
{
	global $spip_display, $spip_ecran;

	$rows = ($spip_ecran == "large") ? 28 : 15;

	return (($spip_display == 4) ? '' :
		afficher_barre('document.formulaire.texte', $forum))
	. "<textarea name='texte' id='texte' "
	. $GLOBALS['browser_caret']
	. " rows='$rows' class='formo' cols='40'>"
	. entites_html($texte)
	. "</textarea>\n";
}

?>