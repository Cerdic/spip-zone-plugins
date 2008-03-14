<?php

/*******************************************************************
 *
 * Copyright (c) 2008
 * Xavier BUROT
 * fichier : balise/genea_filtres
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL
 *
 * *******************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// -- Recuperation de l'initial d'un mot --------------------------------
function initial($texte){
	if (!strlen($texte)) return '';
	return strtoupper($texte[0]);
}

// -- Transforme les codes des tables en terme compréhensible -----------
function gtraduc($texte){
	if (!strlen($texte)) return '';
	return _T('genea:'.$texte);
}

// -- Transforme les codes des tables en abrevations --------------------
function abreviation($texte){
	if (!strlen($texte)) return '';
	return _T('genea:'.$texte.'_court');
}

function majuscule($texte){
	$mots = explode('-', $texte);
	foreach($mots as $val){
		$tampon[] = ucfirst($val);
	}
	$texte = implode('-', $tampon);
	unset($tampon);
	$mots = explode(' ', $texte);
	foreach($mots as $val){
		$tampon[] = ucfirst($val);
	}
	return implode(' ', $tampon);
}
?>