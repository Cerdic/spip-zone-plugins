<?php

//    Fichier créé pour SPIP avec un bout de code emprunté à celui ci.
//    Distribué sans garantie sous licence GPL./
//    Copyright (C) 2006  Pierre ANDREWS
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_COLORATION_CODE',(_DIR_PLUGINS.end($p)));

// pour interdire globalement et optionnellement le téléchargement associé
if (!defined('PLUGIN_COLORATION_CODE_TELECHARGE')) {
	define('PLUGIN_COLORATION_CODE_TELECHARGE', true);
}

function coloration_code_color($code, $language, $cadre='cadre') {

	$params = explode(' ', $language);
	$language = array_shift($params);
	include_once _DIR_PLUGIN_COLORATION_CODE . '/geshi/geshi.php';
	//
	// Create a GeSHi object
	//
	$geshi = & new GeSHi($code, $language);
	if ($geshi->error()) {
		return false;
	}
	global $spip_lang_right;

	$code = echappe_retour($code);

	$telecharge = 
		(PLUGIN_COLORATION_CODE_TELECHARGE || in_array('telecharge', $params))
	 && (strpos($code, "\n") !== false) && !in_array('sans_telecharge', $params);
	if ($telecharge) {
		// Gerer le fichier contenant le code au format texte
		$nom_fichier = md5($code);
		$dossier = sous_repertoire(_DIR_IMG, 'cache-code');
		$fichier = "$dossier$nom_fichier.txt";

		if (!file_exists($fichier)) {
			$handle = fopen($fichier, 'w');
			fwrite($handle, $code);
			fclose($handle);
		}
	}

	if ($cadre == 'cadre') {
	  $geshi->set_header_type(GESHI_HEADER_DIV);
	  $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
	} else {
	  $geshi->set_header_type(GESHI_HEADER_NONE);
	  $geshi->enable_line_numbers(GESHI_NO_LINE_NUMBERS);
	}

	//
	// And echo the result!
	//
	$rempl = $geshi->parse_code();

	if ($telecharge) {
		$rempl .= "<div class='" . $cadre . "_download'
		style='text-align: $spip_lang_right;'>
		<a href='$fichier'
		style='font-family: verdana, arial, sans; font-weight: bold; font-style: normal;'>" .
			propre("<multi>Download[fr]T&eacute;l&eacute;charger</multi>") .
				"</a></div>";
	}
	return $rempl;
}

function cadre_ou_code($regs) {
	$ret = false;
// pour l'instant, on oublie $matches[1] et $matches[4] les attributs autour de class="machin"
	if (!preg_match(',^(.*)class=("|\')(.*)\2(.*)$,Uims',$regs[2], $matches)
	|| !($ret = coloration_code_color($regs[3], $matches[3], $regs[1]))) {
		$ret = $regs[1] == 'code' ? traiter_echap_code_dist($regs)
					: traiter_echap_cadre_dist($regs);
	}
	return $ret;
}

function traiter_echap_code($regs) {
	return cadre_ou_code($regs);
}

function traiter_echap_cadre($regs) {
	return cadre_ou_code($regs);
}
