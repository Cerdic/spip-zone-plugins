<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// compatibilite pour avant 1.9.3
if (!function_exists('test_espace_prive')) {
	function test_espace_prive() {
		return defined('_DIR_RESTREINT') ? !_DIR_RESTREINT : false;
	}
}

define('_DIR_BTV2_IMG', _DIR_PLUGIN_BARRETYPOENRICHIE.'/img_pack/icones_barre/');

// construit un bouton (ancre) de raccourci avec icone et aide

// http://doc.spip.org/@bouton_barre_racc
function bouton_barre_racc($action, $img, $help, $champhelp) {

	$a = attribut_html($help);
	return "<a\nhref=\"javascript:"
		.$action
		."\" tabindex='1000'\ntitle=\""
		. $a
		."\"" 
		  .(test_espace_prive() ? '' :  ("\nonmouseover=\"helpline('"
		  .addslashes(str_replace('&#39;',"'",$a))
		  ."',$champhelp)\"\nonmouseout=\"helpline('"
					 .attribut_html(_T('barre_aide')))
		  ."', $champhelp)\"")
		."><img src='"
		.$img
		."' style=\"height: 16px; width: 16px; background-position: center center;\" alt=\"$a\" /></a>";
}

// sert a construire les sousbarre
function bte_renomme_block($nom_block) { 
	global $numero_block, $compteur_block; 
	if (!isset($numero_block[$nom_block])){ 
		$compteur_block++; 
		$numero_block[$nom_block] = $compteur_block;
	}
	return $numero_block["$nom_block"];
}

function bte_debut_block_visible($nom_block){ 
	global $browser_layer; 
	if (!$browser_layer) return ''; 
	return "<div id='Layer".bte_renomme_block($nom_block)."' style='display: block;'>"; 
} 

function bte_debut_block_invisible($nom_block){ 
	global $browser_layer; 
	if (!$browser_layer) return ''; 

	// si on n'accepte pas js, ne pas fermer 
	if (!_SPIP_AJAX) 
		return debut_block_visible($nom_block); 
	return "<div id='Layer".bte_renomme_block($nom_block)."' style='display: none;'>"; 
}

function produceWharf($id, $title = '', $sb = '') {
  $visible = ($changer_virtuel || $virtuel);
  $res .= $title;
  $GLOBALS['numero_block'][$id] = ($GLOBALS['compteur_block']+1);
  if ($visible) {
    $res .= bte_debut_block_visible("arb_".$GLOBALS['numero_block'][$id]);
  } else {
    $res .= bte_debut_block_invisible("arb_".$GLOBALS['numero_block'][$id]);
  }
  $res .= $sb;
  $res .= '</div>';
  return $res;
}

//gestion des lignes optionnelles

// construction des liens
function afficher_gestion_lien($champ, $num_barre) {

$tableau_formulaire = '
 <table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">
<tr><td> 
'._T('bartypenr:barre_adresse').'&nbsp;: <input type="text" name="lien_nom" id="lien_nom'.$num_barre.'" value="http://" size="21" maxlength="255" /><a href="" onclick="javascript:barre_liens_spip('.$champ.', \'\'); return false;" title="'._T('icone_doc_rubrique').'" ><img src="../dist/images/rubrique-12.gif" style="position: relative; top: 4px; left: 2px;" alt="Chercher un objet SPIP" /></a><br />
'._T('bartypenr:barre_bulle').'&nbsp;: <input type="text" name="lien_bulle" id="lien_bulle'.$num_barre.'" value="" size="21" maxlength="255" />
</td><td>
'._T('bartypenr:barre_langue').'&nbsp;: <input type="text" name="lien_langue" id="lien_langue'.$num_barre.'" value="" size="10" maxlength="10" />
</td><td>
  <input type="button" value="'._T('pass_ok').'" class="fondo" onclick="javascript:barre_demande_lien(\'[\', \'->\', \']\', document.getElementById(\'lien_nom'.$num_barre.'\').value, document.getElementById(\'lien_bulle'.$num_barre.'\').value, document.getElementById(\'lien_langue'.$num_barre.'\').value,'.$champ.','.$num_barre.');document.getElementById(\'lien_nom'.$num_barre.'\').value=\'\';document.getElementById(\'lien_bulle'.$num_barre.'\').value=\'\';document.getElementById(\'lien_langue'.$num_barre.'\').value=\'\';" /> 
</td></tr></table>
';
  return produceWharf('tableau_lien','',$tableau_formulaire); 	
}

// Changer la casse
function RaccourcisMajusculesMinuscules($champ, $champhelp, $num_barre) {
	return bouton_barre_racc("barre_capitales($champ,true,$num_barre)",  _DIR_BTV2_IMG.'text_uppercase.png', _T('bartypenr:barre_gestion_cr_changercassemajuscules'), $champhelp) .'&nbsp;'
. bouton_barre_racc("barre_capitales($champ,false,$num_barre)",  _DIR_BTV2_IMG.'text_lowercase.png', _T('bartypenr:barre_gestion_cr_changercasseminuscules'), $champhelp);
}

// gestion de la recherche
function afficher_gestion_remplacer($champ, $champhelp, $num_barre) {

$tableau_formulaire = '
<table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">
<tr style="vertical-align:top;"><td>'.
_T('bartypenr:barre_gestion_cr_chercher')
.' <input type="text" id="barre_chercher'.$num_barre.'" name="barre_chercher" value="" size="12" maxlength="255" /><br />
<input style="width:auto;" type="checkbox" name="rec_case" id="rec_case'.$num_barre.'" value="yes" />&nbsp;'._T('bartypenr:barre_gestion_cr_casse').'<br />
   <input type="button" value="'._T('bartypenr:barre_gestion_cr_chercher').'" class="fondo"
  onclick="javascript:barre_search(document.getElementById(\'barre_chercher'.$num_barre.'\').value, document.getElementById(\'rec_entier'.$num_barre.'\').checked, document.getElementById(\'rec_case'.$num_barre.'\').checked, '.$champ.');" /> 
</td><td>'._T('bartypenr:barre_gestion_cr_remplacer')
.' <input type="text" name="barre_remplacer" id="barre_remplacer'.$num_barre.'" value="" size="12" maxlength="255" /><br />
<input style="width:auto;" type="checkbox" name="rec_tout" id="rec_tout'.$num_barre.'" value="yes" />&nbsp;'._T('bartypenr:barre_gestion_cr_tout').'<br />
<input style="width:auto;" type="checkbox" name="rec_entier" id="rec_entier'.$num_barre.'" value="yes" />&nbsp;'._T('bartypenr:barre_gestion_cr_entier').'<br />
   <input type="button" value="'._T('bartypenr:barre_gestion_cr_remplacer').'" class="fondo"
  onclick="javascript:barre_searchreplace(document.getElementById(\'barre_chercher'.$num_barre.'\').value, document.getElementById(\'barre_remplacer'.$num_barre.'\').value, document.getElementById(\'rec_tout'.$num_barre.'\').checked, document.getElementById(\'rec_case'.$num_barre.'\').checked, document.getElementById(\'rec_entier'.$num_barre.'\').checked,'.$champ.','.$num_barre.');" /> 
</td>
<td>'._T('bartypenr:barre_gestion_cr_changercasse').'<br />'. RaccourcisMajusculesMinuscules($champ, $champhelp, $num_barre).'
</td>
</tr></table>';

  return produceWharf('tableau_remplacer','',$tableau_formulaire); 
}

// pour les ancres
function afficher_gestion_ancre($champ, $num_barre) {

$tableau_formulaire = '
<table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">
  <tr style="vertical-align:top;">
    <td style="width:auto; text-align:center;"><strong>'.
_T('bartypenr:barre_gestion_anc_caption')
.'</strong></td>
    <td style="width:auto;"><strong>'.
_T('bartypenr:barre_gestion_anc_inserer')
.'</strong><br />
    <i>'._T('bartypenr:barre_gestion_anc_nom').'</i><br />
      <input type="text" name="ancre_nom" id="ancre_nom'.$num_barre.'" />
	  
	<input type="button" value="'._T('pass_ok').'" class="fondo" onclick="javascript:barre_ancre(\'[\', \'<-\', \']\', document.getElementById(\'ancre_nom'.$num_barre.'\').value, '.$champ.','.$num_barre.');" />
    </td>
	<td style="width:auto;"><strong>'.
_T('bartypenr:barre_gestion_anc_pointer')
.'</strong><br />
    <i>'._T('bartypenr:barre_gestion_anc_cible').'</i><br /><input type="text" name="ancre_cible" id="ancre_cible'.$num_barre.'" /><br />
	<i>'._T('bartypenr:barre_gestion_anc_bulle').'</i><br /><input type="text" name="ancre_bulle" id="ancre_bulle'.$num_barre.'" />
	<input type="button" value="'._T('pass_ok').'" class="fondo" onclick="javascript:barre_demande(\'[\', \'->#\', \']\', document.getElementById(\'ancre_cible'.$num_barre.'\').value, document.getElementById(\'ancre_bulle'.$num_barre.'\').value, '.$champ.', '.$num_barre.');" /> 
</td>
  </tr> 
</table>';

  return produceWharf('tableau_ancre','',$tableau_formulaire); 	
}

// pour les caracteres
function afficher_caracteres($champ, $spip_lang, $champhelp, $num_barre) {

	// guillemets
	if ($spip_lang == "fr" OR $spip_lang == "eo" OR $spip_lang == "cpf" OR $spip_lang == "ar" OR $spip_lang == "es") {
$reta .= bouton_barre_racc("barre_raccourci('&laquo;~','~&raquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets.png", _T('barre_guillemets'), $champhelp);
$reta .= bouton_barre_racc("barre_raccourci('&ldquo;','&rdquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-simples.png", _T('barre_guillemets_simples'), $champhelp);
}
else if ($spip_lang == "bg" OR $spip_lang == "de" OR $spip_lang == "pl" OR $spip_lang == "hr" OR $spip_lang == "src") {
$reta .= bouton_barre_racc("barre_raccourci('&bdquo;','&ldquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-de.png", _T('barre_guillemets'), $champhelp);
$reta .= bouton_barre_racc("barre_raccourci('&sbquo;','&lsquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-uniques-de.png", _T('barre_guillemets_simples'), $champhelp);
}
else {
$reta .= bouton_barre_racc("barre_raccourci('&ldquo;','&rdquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-simples.png", _T('barre_guillemets'), $champhelp);
$reta .= bouton_barre_racc("barre_raccourci('&lsquo;','&rsquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-uniques.png", _T('barre_guillemets_simples'), $champhelp);
}
$reta .= '&nbsp;';
	// caracteres
if ($spip_lang == "fr" OR $spip_lang == "eo" OR $spip_lang == "cpf") {
	$reta .= bouton_barre_racc("barre_inserer('&Agrave;',$champ)", _DIR_BTV2_IMG.'agrave-maj.png', _T('barre_a_accent_grave'), $champhelp);
	$reta .= bouton_barre_racc("barre_inserer('&Eacute;',$champ)", _DIR_BTV2_IMG.'eacute-maj.png', _T('barre_e_accent_aigu'), $champhelp);
	$reta .= bouton_barre_racc("barre_inserer('&Egrave;',$champ)", _DIR_BTV2_IMG.'eagrave-maj.png', _T('bartypenr:barre_e_accent_grave'), $champhelp);
	$reta .= bouton_barre_racc("barre_inserer('&aelig;',$champ)", _DIR_BTV2_IMG.'aelig.png', _T('bartypenr:barre_ea'), $champhelp);
	$reta .= bouton_barre_racc("barre_inserer('&AElig;',$champ)", _DIR_BTV2_IMG.'aelig-maj.png', _T('bartypenr:barre_ea_maj'), $champhelp);
	if ($spip_lang == "fr") {
		$reta .= bouton_barre_racc("barre_inserer('&oelig;',$champ)", _DIR_BTV2_IMG.'oelig.png', _T('barre_eo'), $champhelp);
		$reta .= bouton_barre_racc("barre_inserer('&OElig;',$champ)", _DIR_BTV2_IMG.'oelig-maj.png', _T('barre_eo_maj'), $champhelp);
		$reta .= bouton_barre_racc("barre_inserer('&Ccedil;',$champ)", _DIR_BTV2_IMG.'ccedil-maj.png', _T('bartypenr:barre_c_cedille_maj'), $champhelp);
	}
}
// euro
$reta .= '&nbsp;'.bouton_barre_racc("barre_inserer('&euro;',$champ)", _DIR_BTV2_IMG.'euro.png', _T('barre_euro'), $champhelp);
$reta .= '&nbsp;'.RaccourcisMajusculesMinuscules($champ, $champhelp, $num_barre);
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
function afficher_formatages_speciaux($champ, $spip_lang, $champhelp, $num_barre) {
	$reta = bouton_barre_racc("barre_raccourci('\n\n&lt;quote&gt;','&lt;/quote&gt;\n\n',$champ)", _DIR_IMG_ICONES_BARRE."quote.png", _T('barre_quote'), $champhelp);
	$reta .= bouton_barre_racc("barre_raccourci('&lt;code&gt;','&lt;/code&gt;',$champ)", _DIR_BTV2_IMG.'page_white_code_red.png', _T('bartypenr:barre_code'), $champhelp);
	$reta .= bouton_barre_racc("barre_raccourci('\n\n&lt;cadre&gt;','&lt;/cadre&gt;\n\n',$champ)", _DIR_BTV2_IMG.'page_white_code.png', _T('bartypenr:barre_cadre'), $champhelp);
	$reta .= bouton_barre_racc("barre_raccourci('\n\n&lt;poesie&gt;','&lt;/poesie&gt;\n\n',$champ)", _DIR_BTV2_IMG.'poesie.png', _T('bartypenr:barre_poesie'), $champhelp);
	$tableau_formulaire = '
	<table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">
	  <tr class="spip_barre">
		<td>'._T('bartypenr:barre_formatages_speciaux').'</td>
		<td>'.$reta.'
		</td>
	  </tr> 
	</table>
	';
	return produceWharf('tableau_formatages_speciaux','',$tableau_formulaire); 	
}

// cas ou CFG est absent...
function config_bte($cfg, $valeur) {
	if(function_exists('lire_config')) return lire_config($cfg) == $valeur;
	return false;
}

// construit un tableau de raccourcis pour un noeud de DOM

// http://doc.spip.org/@afficher_barre
function afficher_barre($champ, $forum=false, $lang='') {
	global $spip_lang, $spip_lang_right, $spip_lang_left;
	static $num_barre = 0;
	include_spip('inc/layer');
	if (!$GLOBALS['browser_barre']) return '';
	if (!$lang) $lang = $spip_lang;
	$num_barre++;
	$champhelp = "document.getElementById('barre_$num_barre')";
	$ecrire = test_espace_prive();
	$crayons = _request('action')=='crayons_html';
	// le champ est passe sous la forme document.formulaire.champ ou sous la forme document.getElementsByName('champ')[0]
	if(preg_match(",(document\.formulaire\.(\w+)|document\.getElementsByName\('(\w+)'\)),", $champ, $reg))
		$name = $reg[2]?$reg[2]:$reg[3]; else $name = '';
	// le champ est passe sous la forme document.getElementById('champ')
	if(preg_match(",document\.getElementById\('(\w+)'\),", $champ, $reg))
		$id = $reg[1]; else $id = '';
	$params_vierge = array('champ'=>$champ, 'help'=>$champhelp, 'lang'=>$spip_lang, 'name'=>$name, 'id'=>$id, 'num'=>$num_barre, 'forum'=>$forum, 'ecrire'=>$ecrire, 'crayons'=> $crayons, 'flux'=>'');

	if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) {
		$ret = ($num_barre > 1)  ? '' :
		  "<script type='text/javascript' src='". find_in_path(_JAVASCRIPT.'layer.js') ."'></script><script type='text/javascript' src='". find_in_path(_JAVASCRIPT.'spip_barre.js') ."'></script>";
	} else {
		$ret = ($num_barre > 1)  ? '' :
		  "<script type='text/javascript' src='". find_in_path(_JAVASCRIPT.'layer.js') ."'></script>".http_script('', _DIR_PLUGIN_BARRETYPOENRICHIE.'javascript/spip_barre.js');
	}


	// Pregeneration des toolzbox.. (wharfing)
	$toolbox .= afficher_caracteres($champ, $spip_lang, $champhelp, $num_barre);
	$toolbox .= afficher_formatages_speciaux($champ, $spip_lang, $champhelp, $num_barre);
	$toolbox .= afficher_gestion_lien($champ, $num_barre);
	$toolbox .= afficher_gestion_ancre($champ, $num_barre);
	$toolbox .= afficher_gestion_remplacer($champ, $champhelp, $num_barre);
	// Pipeline pour ajouter des toolzbox
	$add = pipeline("BT_toolbox", $params_vierge);
	$toolbox .= $add['flux'];

	$ret .= "<table class='spip_barre' cellpadding='0' cellspacing='0' border='0'>";
	$ret .= "\n<tr>";
	$ret .= "\n<td style='text-align: $spip_lang_left;' valign='middle'>";
	$col = 1;

	// Italique, gras, intertitres
	$ret .= bouton_barre_racc("barre_raccourci('{','}',$champ)", _DIR_IMG_ICONES_BARRE."italique.png", _T('barre_italic'), $champhelp);
	$ret .= bouton_barre_racc("barre_raccourci('{{','}}',$champ)", _DIR_IMG_ICONES_BARRE."gras.png", _T('barre_gras'), $champhelp);
	$add = pipeline("BT_caracteres", $params_vierge);
	$ret .= $add['flux'];


	$retP = '';
	// Raccourcis de paragraphes : intertitres, formatages speciaux
	if ($ecrire) {
		$retP .= bouton_barre_racc("barre_raccourci('\n\n{{{','}}}\n\n',$champ)", _DIR_IMG_ICONES_BARRE."intertitre.png", _T('barre_intertitre'), $champhelp);
		if (config_bte('btv2/avancee', 'Oui'))
			$retP .= bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_formatages_speciaux']."','');", _DIR_BTV2_IMG.'tag.png', _T('bartypenr:barre_formatages_speciaux'), $champhelp);;
	} else {
		$retP .= bouton_barre_racc("barre_raccourci('\n\n&lt;quote&gt;','&lt;/quote&gt;\n\n',$champ)", _DIR_IMG_ICONES_BARRE."quote.png", _T('barre_quote'), $champhelp);
	}
	$add = pipeline("BT_paragraphes", $params_vierge);
	$retP .= $add['flux'];
	$ret .= "&nbsp;$retP</td>\n<td>";
	$col++;

	$retL = '';
	// Gestion des liens, ancres, notes, glossaire
	$retL .= bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_lien']."','');", _DIR_IMG_ICONES_BARRE."lien.png", _T('barre_lien'), $champhelp);
	if ($ecrire) {
		$retL .= bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_ancre']."','');", _DIR_BTV2_IMG.'ancre.png', _T('bartypenr:barre_ancres'), $champhelp);  
		$retL .= bouton_barre_racc("barre_raccourci('[[',']]',$champ)", _DIR_IMG_ICONES_BARRE."notes.png", _T('barre_note'), $champhelp);
		$retL .= bouton_barre_racc("barre_raccourci('[?',']',$champ)", _DIR_BTV2_IMG.'barre-wiki.png', _T('bartypenr:barre_glossaire'), $champhelp);
	}
	$add = pipeline("BT_liens", $params_vierge);
	$retL .= $add['flux'];
	$ret .= "&nbsp;$retL</td>\n<td>";
	$col++;
		
	if ($ecrire) {
		$retS = '';
		// Gestion des structures : remplacement, tableaux, images
		$retS .= bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_remplacer']."','');", _DIR_BTV2_IMG.'chercher_remplacer.png', _T('bartypenr:barre_chercher'), $champhelp);
		$retS .= bouton_barre_racc("barre_tableau($champ, '"._DIR_RESTREINT."')", _DIR_BTV2_IMG.'barre-tableau.png', _T('bartypenr:barre_tableau'), $champhelp);
		// DEB Galerie JPK
		// idee originale de http://www.gasteroprod.com/la-galerie-spip-pour-reutiliser-facilement-les-images-et-documents.html
		$retS .= bouton_barre_racc("javascript:barre_galerie($champ, '"._DIR_RESTREINT."')", _DIR_BTV2_IMG.'galerie.png', _T('bartypenr:barre_galerie'), $champhelp);
		$add = pipeline("BT_structures", $params_vierge);
		$retS .= $add['flux'];
		$ret .= "&nbsp;$retS</td>\n<td>";
		$col++;
	}


	$retG = '';
	// Place pour les gadgets : caracteres difficiles a taper au clavier (guillemets, majuscules accentuees...), preview, stats
	$retG .= bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_caracteres']."','');", _DIR_BTV2_IMG.'clavier.png', _T('bartypenr:barre_caracteres'), $champhelp);
	if (!$crayons && $ecrire && !$forum) {
		$retG .= bouton_barre_racc("toggle_preview($num_barre,'".str_replace("'","\\'",$champ)."');", _DIR_BTV2_IMG.'eye.png', _T('bartypenr:barre_preview'), $champhelp);
		$retG .= bouton_barre_racc("toggle_stats($num_barre,'".str_replace("'","\\'",$champ)."');", _DIR_BTV2_IMG.'stats.png', _T('bartypenr:barre_stats'), $champhelp);
	}
	$add = pipeline("BT_gadgets", $params_vierge);
	$retG .= $add['flux'];
	$ret .= "&nbsp;$retG</td>";
	$col++;

	$ret .= "</tr>";

	// Sur les forums publics, petite barre d'aide en survol des icones
	if (!$ecrire)
		$ret .= "\n<tr>\n<td colspan='$col'><input disabled='disabled' type='text' class='barre' id='barre_$num_barre' size='45' maxlength='100'\nvalue=\"".attribut_html(_T('barre_aide'))."\" /></td></tr>";

	$ret .= "</table>";
	$ret .= $toolbox;
	$ret .= '<script type="text/javascript"><!--';
	$ret .= '
form_dirty = false;
warn_onunload = true;

/* ChainHandler, py Peter van der Beken
-------------------------------------------------------- */
function chainHandler(obj, handlerName, handler) {
        obj[handlerName] = (function(existingFunction) {
                return function() {
                        handler.apply(this, arguments);
                        if (existingFunction)
                                existingFunction.apply(this, arguments);
                };
        })(handlerName in obj ? obj[handlerName] : null);
};

$(document).ready(function(){';
	if ($ecrire) {
		$ret .= '
		$('.$champ.').after("<div id=\"article_preview'.$num_barre.'\"></div>");
		$('.$champ.').before("<div id=\"article_stats'.$num_barre.'\"></div>");
		';
		global $spip_version_code;
		if (version_compare($spip_version_code,'1.9250','<')){
			$ret .= '$.ajaxTimeout( 5000 );'; // jquery < 1.1.4
		} else {
			$ret .= '$.ajaxSetup({timeout: 5000});'; // a partir de jquery 1.1.4, donc de SPIP 1.9.3
		}
		$ret .= '
		$('.$champ.').keypress(function() { MajPreview('.$num_barre.',"'.$champ.'") });
		$('.$champ.').select(function() { MajStats('.$num_barre.',"'.$champ.'") });
		$('.$champ.').click(function() { MajStats('.$num_barre.',"'.$champ.'") });';
	}
	$ret .= '
	chainHandler(window,\'onbeforeunload\',function(e) { 
		if (e == undefined && window.event) {
			e = window.event;
		}
		if ( (warn_onunload == true) && (form_dirty == true) && ($.browser.mozilla) ) {
			e.returnValue = \'Quitter la page sans sauvegarder ?\';
			return \'Quitter la page sans sauvegarder ?\'; 
		}
		return false;
	} );
	$("form").submit ( function() {warn_onunload=false;} );
	$('.$champ.')
		.parents(\'form\')
		.find(\'input,textarea,select\')
		.not(\'[@type=hidden]\')
		.change ( function() {form_dirty=true;} );
	$("input").change ( function() {form_dirty=true;} );
});
	 //--></script>';
	return $ret;
}

// expliciter les 3 arguments pour avoir xhtml strict

// http://doc.spip.org/@afficher_textarea_barre
function afficher_textarea_barre($texte, $forum=false, $form='')
{
	global $spip_display, $spip_ecran;

	// par defaut champ avec classe .barre_inserer
	if (!$form) $form = "$('.barre_inserer')[0]";
	// sinon id parent passe, il faut selectionner le champ 'texte'
	else $form .= ".texte";
	
	$rows = ($spip_ecran == "large") ? 28 : 15;

	return (($spip_display == 4) ? '' : afficher_barre($form, $forum))
	. "<textarea name='texte' id='texte' "
	. $GLOBALS['browser_caret']
	. " rows='$rows' class='formo barre_inserer' cols='40'>"
	. entites_html($texte)
	. "</textarea>\n";
}

?>