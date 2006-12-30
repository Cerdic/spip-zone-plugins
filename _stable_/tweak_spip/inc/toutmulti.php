<?php
/*
 - toutmulti -
 introduit le raccourci <:texte:> pour introduire librement des
 blocs multi dans un flux de texte (via typo ou propre)
*/

function ToutMulti_pre_typo($texte) {
	if (preg_match_all("|<:([^>]*):>|", $texte, $matches, PREG_SET_ORDER))
	foreach ($matches as $regs)
		$texte = str_replace($regs[0], _T('spip/ecrire/public:'.$regs[1]), $texte);
	return $texte;
}

?>