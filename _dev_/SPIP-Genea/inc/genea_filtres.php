<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2008
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// -- Recuperation de l'initial d'un mot ------------------------
function initial($texte){
	if (!strlen($texte)) return '';
	return strtoupper($texte[0]);
}

// -- Transforme les codes des tables en terme compréhensible ---
function gtraduc($texte){
	if (!strlen($texte)) return '';
	return _T('genea:'.$texte);
}
?>