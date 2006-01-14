<?php

/*
 * toutmulti
 *
 * introduit le raccourci <:texte:> pour introduire librement des
 * blocs multi dans un flux de texte (via typo ou propre)
 *
 * Auteur : collectif
 * © 2006 - Distribue sous licence BSD
 *
 */

$nom = 'toutmulti';
$version = 0.1;

// s'inserer dans le pipeline 'avant_propre' @ ecrire/inc_texte.php3
$GLOBALS['spip_pipeline']['pre_typo'] .= '|toutmulti';

// la fonction est tres legere on la definit directement ici
function toutmulti($texte) {
	$regexp = "|<:([^>]*):>|";
	if (preg_match_all($regexp, $texte, $matches, PREG_SET_ORDER))
	foreach ($matches as $regs)
		$texte = str_replace($regs[0],
		_T('spip/ecrire/public:'.$regs[1]), $texte);
	return $texte;
}

#$GLOBALS['spip_matrice']['ancres'] = dirname(__FILE__).'/ancres.php';

?>
