<?php

/**
 * @author: cpaulus at quesaco.org
 * @license: GPL3
 * @see http://www.quesaco.org/plugin-spip-articles-lister
 */
// $LastChangedBy$
// $LastChangedDate$

if(!defined('_ECRIRE_INC_VERSION')) { return; }

/**
 * Filtre: Nettoyer le résultat de l'export.
 * @param string $texte
 * @return string
 */
function alis_nettoyer_export($texte)
{
	$texte = trim($texte);
	// le 1 génant 
	$texte = substr ($texte, 1);

	// les espaces en double
	$texte = preg_replace('/[ \t]+/m', ' ', $texte);
	// les commentaires HTML
	$texte = preg_replace('/(<!--.*-->)/', '', $texte);
	// l'espace en debut de ligne
	$texte = preg_replace('/^\s/m', '', $texte);
	// les lignes vides
	$texte = preg_replace('/\n+/m', "\n", $texte);
	// les espaces enrobants
	$texte = trim($texte);
	
	// virgule par point
	// (si virgule dans le titre...)
	$texte = str_replace(',', '.', $texte);
	// le séparateur alis par le traditionnel CSV
	$texte = str_replace(' _ALIS_SEPARATOR_', ',', $texte);
	
	return($texte);
}