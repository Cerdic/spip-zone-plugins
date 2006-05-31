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
	$ret .= bouton_barre_racc ("barre_demande('[','->',']', '".addslashes(_T('barre_lien_input'))."', $champ)",
		_DIR_IMG_ICONES_BARRE."lien.png", _T('barre_lien'), $champhelp);
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
		$ret .= bouton_barre_racc ("barre_tableau($champ)", _DIR_PLUGIN_BARRE_TYPO.'/img_pack/icones_barre/barre-tableau.png', "Ins&eacute;rer un tableau", $formulaire, $texte);
	}

	$ret .= "&nbsp;</td>";
	$col++;

	// Insertion de caracteres difficiles a taper au clavier (guillemets, majuscules accentuees...)
	$ret .= "\n<td style='text-align:$spip_lang_left;' valign='middle'>";
	$col++;
	if ($lang == "fr" OR $lang == "eo" OR $lang == "cpf" OR $lang == "ar" OR $lang == "es") {
		$ret .= bouton_barre_racc ("barre_raccourci('&laquo;~','~&raquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets.png", _T('barre_guillemets'), $champhelp);
		$ret .= bouton_barre_racc ("barre_raccourci('&ldquo;','&rdquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-simples.png", _T('barre_guillemets_simples'), $champhelp);
	}
	else if ($lang == "bg" OR $lang == "de" OR $lang == "pl" OR $lang == "hr" OR $lang == "src") {
		$ret .= bouton_barre_racc ("barre_raccourci('&bdquo;','&ldquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-de.png", _T('barre_guillemets'), $champhelp);
		$ret .= bouton_barre_racc ("barre_raccourci('&sbquo;','&lsquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-uniques-de.png", _T('barre_guillemets_simples'), $champhelp);
	}
	else {
		$ret .= bouton_barre_racc ("barre_raccourci('&ldquo;','&rdquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-simples.png", _T('barre_guillemets'), $champhelp);
		$ret .= bouton_barre_racc ("barre_raccourci('&lsquo;','&rsquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets-uniques.png", _T('barre_guillemets_simples'), $champhelp);
	}
	if ($lang == "fr" OR $lang == "eo" OR $lang == "cpf") {
		$ret .= bouton_barre_racc ("barre_inserer('&Agrave;',$champ)", _DIR_IMG_ICONES_BARRE."agrave-maj.png", _T('barre_a_accent_grave'), $champhelp);
		$ret .= bouton_barre_racc ("barre_inserer('&Eacute;',$champ)", _DIR_IMG_ICONES_BARRE."eacute-maj.png", _T('barre_e_accent_aigu'), $champhelp);
		if ($lang == "fr") {
			$ret .= bouton_barre_racc ("barre_inserer('&oelig;',$champ)", _DIR_IMG_ICONES_BARRE."oelig.png", _T('barre_eo'), $champhelp);
			$ret .= bouton_barre_racc ("barre_inserer('&OElig;',$champ)", _DIR_IMG_ICONES_BARRE."oelig-maj.png", _T('barre_eo_maj'), $champhelp);
		}
	}
	$ret .= bouton_barre_racc ("barre_inserer('&euro;',$champ)", _DIR_IMG_ICONES_BARRE."euro.png", _T('barre_euro'), $champhelp);

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
	return $ret;
}

// pour compatibilite arriere. utiliser directement le corps a present.

function afficher_claret() {
	include_spip('inc/layer');
	return $GLOBALS['browser_caret'];
}

?>