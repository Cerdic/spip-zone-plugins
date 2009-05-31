<?php

// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function insertions_rempl($texte) {
	if (!isset($GLOBALS['meta']['cs_insertions'])) insertions_installe();
	$ins = unserialize($GLOBALS['meta']['cs_insertions']);
	$texte = str_replace($ins[0][0], $ins[0][1], $texte);
	return preg_replace($ins[1][0], $ins[1][1], $texte);
}

// Fonctions de traitement
function insertions_pre_propre($texte) {
	return cs_echappe_balises('', 'insertions_rempl', $texte);
}

?>