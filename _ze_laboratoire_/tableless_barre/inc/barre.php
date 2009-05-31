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

// construit un tableau de raccourcis pour un noeud de DOM 

// http://doc.spip.org/@afficher_barre
function afficher_barre($champ, $forum=false, $lang='') {
	global $spip_lang, $spip_lang_right, $spip_lang_left, $spip_lang;
	static $num_barre = 0;
	include_spip('inc/layer');
	if (!$GLOBALS['browser_barre']) return '';
	if (!$lang) $lang = $spip_lang;
	$num_barre++;
	
	//too complex for me now to put in the modele
	if ($lang == "fr" OR $lang == "eo" OR $lang == "cpf" OR $lang == "ar" OR $lang == "es") {
		$quot="laquo";
	} else if ($lang == "bg" OR $lang == "de" OR $lang == "pl" OR $lang == "hr" OR $lang == "src") {
		$quot="bdquo";
	} else {
		$quot="ldquo";
	}
	if ($lang == "fr") {
		$schar = "fr";
	} else if ($lang == "eo" OR $lang == "cpf") {
		$schar = "eo-cpf";
	}
	
	$ret = recuperer_fond("modeles/barre",array(
		"champ" => $champ,
		"num_barre" => $num_barre,
		"spip_lang_left" => $spip_lang_left,
		"spip_lang_right" => $spip_lang_right,
		"forum" => $forum,
		"quot" => $quot,
		"schar" => $schar
	));

	return $ret;
}

// pour compatibilite arriere. utiliser directement le corps a present.

// http://doc.spip.org/@afficher_claret
function afficher_claret() {
	include_spip('inc/layer');
	return $GLOBALS['browser_caret'];
}

?>
