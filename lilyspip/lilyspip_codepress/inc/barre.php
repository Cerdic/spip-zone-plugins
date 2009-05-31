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
if (!defined('_DIR_Lilypond')){
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_Lilypond',(_DIR_PLUGINS.end($p)));
}

// construit un bouton (ancre) de raccourci avec icone et aide

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
		  .addslashes(str_replace('&#39;',"'",$a))
		  ."',$champhelp)\" onmouseout=\"helpline('"
		  .attribut_html(_T('barre_aide'))
		  ."', $champhelp)\"")
		."><img src='"
		.$img
		."' style=\"height: 16px; width: 16px; background-position: center center;\" alt=\"$a\" /></a>";
}

// http://doc.spip.org/@afficher_barre
function afficher_barre($champ, $forum=false, $lang='') {
	global $spip_lang, $spip_lang_right, $spip_lang_left, $spip_lang;
	static $num_barre = 0;
	include_spip('inc/layer');
	if (!$GLOBALS['browser_barre']) return '';
	if (!$lang) $lang = $spip_lang;
	
	$layer_public = '<script type="text/javascript" src="' . find_in_path('javascript/layer.js').'"></script>';
	$ret = ($num_barre > 0)  ? '' :
	  $layer_public . '<script type="text/javascript" src="' . find_in_path('js/spip_barre.js').'"></script>';

	$num_barre++;
	$champhelp = "document.getElementById('barre_$num_barre')";

	
	$ret .= "<table class='spip_barre' cellpadding='0' cellspacing='0' border='0'>";
	$ret .= "\n<tr>";
	$ret .= "\n<td style='text-align: $spip_lang_left;' valign='middle'>";
	$col = 1;

	// Italique, gras, intertitres
	$ret .= bouton_barre_racc ("barre_raccourci('{','}',$champ)", _DIR_IMG_ICONES_BARRE."italique.png", _T('barre_italic'), $champhelp);
	$ret .= bouton_barre_racc ("barre_raccourci('{{','}}',$champ)", _DIR_IMG_ICONES_BARRE."gras.png", _T('barre_gras'), $champhelp);
	if (!$forum) {
		$ret .= bouton_barre_racc ("barre_raccourci('\n\n{{{','}}}\n\n',$champ)", _DIR_IMG_ICONES_BARRE."intertitre.png", _T('barre_intertitre'), $champhelp);
	}
	$ret .= "</td>\n<td>";
	$col ++;

	//Lilypond
	$ret .= bouton_barre_racc("barre_lilypond($champ, '"._DIR_RESTREINT."')",
		_DIR_Lilypond.'/images/barre_lilypond.png', _T('lilyspip:barre_lilypond'),
		$champhelp);

	$ret .= "</td>\n<td style='text-align: $spip_lang_left;' valign='middle'>";

	

	

	// Lien hypertexte, notes de bas de page, citations
	$js = addslashes(_T('barre_lien_input'));
	
	
	$ret .= bouton_barre_racc ("barre_demande('[','->',']', '$js', $champ)",
		 _DIR_IMG_ICONES_BARRE."lien.png", _T('barre_lien'), $champhelp);
		
	// gestion des liens
	//$ret .=    bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['tableau_lien']."','');", _DIR_IMG_ICONES_BARRE."lien.png", _T('barre_lien'), $champhelp);

	if (!$forum) {
		$ret .= bouton_barre_racc ("barre_raccourci('[[',']]',$champ)", _DIR_IMG_ICONES_BARRE."notes.png", _T('barre_note'), $champhelp);
	} else {
		$col ++;
		$ret .= "</td>\n<td>"
		  . bouton_barre_racc ("barre_raccourci('\n\n&lt;quote&gt;','&lt;/quote&gt;\n\n',$champ)", _DIR_IMG_ICONES_BARRE."quote.png", _T('barre_quote'), $champhelp);
	}

	$ret .= "</td>";
	$col++;

	// Insertion de caracteres difficiles a taper au clavier (guillemets, majuscules accentuees...)
	$ret .= "\n<td style='text-align:$spip_lang_left;' valign='middle'>";
	$col++;
	if ($lang == "fr" OR $lang == "eo" OR $lang == "cpf" OR $lang == "ar" OR $lang == "es") {
		$ret .= bouton_barre_racc ("barre_raccourci('&laquo;','&raquo;',$champ)", _DIR_IMG_ICONES_BARRE."guillemets.png", _T('barre_guillemets'), $champhelp);
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

	$ret .= "</td>";
	$col++;

	if (!_DIR_RESTREINT) {
		$ret .= "\n<td style='text-align:$spip_lang_right;' valign='middle'>";
		$col++;
	//	$ret .= "&nbsp;&nbsp;&nbsp;";
		$ret .= aide("raccourcis");
		$ret .= "&nbsp;";
		$ret .= "</td>";
	}
	$ret .= "</tr>";

	// Sur les forums publics, petite barre d'aide en survol des icones
	if (_DIR_RESTREINT)
		$ret .= "\n<tr>\n<td colspan='$col'><input disabled='disabled' type='text' class='barre' id='barre_$num_barre' size='45' maxlength='100'\nvalue=\"".attribut_html(_T('barre_aide'))."\" /></td></tr>";

	$ret .= "</table>";

	return $ret;
}



// http://doc.spip.org/@afficher_textarea_barre
function afficher_textarea_barre($texte, $forum=false) {
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