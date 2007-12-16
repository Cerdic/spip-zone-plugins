<?php
/*
 - ToutMulti -
 introduit le raccourci <:texte:> pour introduire librement des
 blocs multi dans un flux de texte (via typo ou propre)
*/

function ToutMulti_pre_typo($texte) {
	if (preg_match_all(',<:([^>]*):>,', $texte, $matches, PREG_SET_ORDER))
	foreach ($matches as $m)
		$texte = str_replace($m[0], _T('spip/ecrire/public:'.$m[1]), $texte);
	return $texte;
}

?>